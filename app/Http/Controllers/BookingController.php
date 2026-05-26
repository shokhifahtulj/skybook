<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\FlightSchedule;
use App\Models\Notification;
use App\Services\Booking\BookingService;
use App\Services\Inventory\FlightSeatInventoryService;
use App\Services\Payment\PaymentCallbackService;
use App\Services\Payment\PaymentService;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected PaymentService $paymentService,
        protected PaymentCallbackService $paymentCallbackService,
        protected FlightSeatInventoryService $flightSeatInventoryService
    ) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));

        $bookings = Booking::query()
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('booked_by', auth()->id());
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('pnr', 'like', "%{$search}%")
                        ->orWhere('booking_status', 'like', "%{$search}%")
                        ->orWhere('payment_status', 'like', "%{$search}%")
                        ->orWhereHas('segments.schedule.flight', function ($flightQuery) use ($search) {
                            $flightQuery->where('flight_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where(function ($inner) use ($status) {
                    $inner->where('booking_status', $status)
                        ->orWhere('payment_status', $status)
                        ->orWhere('status_booking', $status);
                });
            })
            ->with([
                'segments.schedule.flight.route.origin',
                'segments.schedule.flight.route.destination',
                'segments.schedule.flight.airline',
                'schedule.flight.route.origin',
                'schedule.flight.route.destination',
                'schedule.flight.airline',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $selectedScheduleId = $request->query('schedule_id');

        $schedules = FlightSchedule::query()
            ->with(['flight.route.origin', 'flight.route.destination', 'flight.airline'])
            ->where('departure_datetime', '>=', now())
            ->orderBy('departure_datetime')
            ->paginate(12)
            ->withQueryString();

        $selectedSchedule = $selectedScheduleId
            ? FlightSchedule::with(['flight.route.origin', 'flight.route.destination', 'flight.airline'])
                ->find($selectedScheduleId)
            : null;

        return view('bookings.create', compact('schedules', 'selectedSchedule'));
    }

    public function selectSchedule(Request $request)
    {
        $request->validate([
            'schedule_id' => ['required', 'exists:flight_schedules,id'],
        ]);

        $schedule = FlightSchedule::with(['flight.route.origin', 'flight.route.destination', 'flight.airline'])
            ->findOrFail($request->input('schedule_id'));

        session([
            'booking_wizard.schedule_id' => $schedule->id,
            'booking_wizard.jumlah_tiket' => null,
            'booking_wizard.passengers' => [],
        ]);

        return redirect()->route('bookings.passengers')->with('success', 'Jadwal terpilih. Silakan lengkapi data penumpang.');
    }

    public function passengers()
    {
        $scheduleId = session('booking_wizard.schedule_id');

        if (! $scheduleId) {
            return redirect()->route('bookings.create')->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $schedule = FlightSchedule::with([
            'flight.route.origin',
            'flight.route.destination',
            'flight.airline',
            'seats' => function ($query) {
                $query->orderBy('seat_number');
            },
        ])->findOrFail($scheduleId);

        $this->flightSeatInventoryService->generate($schedule);

        $schedule->load(['seats' => function ($query) {
            $query->orderBy('seat_number');
        }]);

        $storedPassengers = session('booking_wizard.passengers', []);
        $jumlahTiket = session('booking_wizard.jumlah_tiket', 1);

        return view('bookings.wizard.passengers', compact('schedule', 'storedPassengers', 'jumlahTiket'));
    }

    public function savePassengers(Request $request)
    {
        $scheduleId = session('booking_wizard.schedule_id');

        if (! $scheduleId) {
            return redirect()->route('bookings.create')->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $schedule = FlightSchedule::with('seats')->findOrFail($scheduleId);
        $jumlahTiket = (int) $request->input('jumlah_tiket', 1);
        $availableSeats = $schedule->seats()->where('status', 'available')->pluck('seat_number')->all();

        $request->validate([
            'jumlah_tiket' => ['required', 'integer', 'min:1', 'max:' . count($availableSeats)],
            'passengers' => ['required', 'array', 'size:' . $jumlahTiket],
            'passengers.*.first_name' => ['required', 'string', 'min:2'],
            'passengers.*.last_name' => ['required', 'string', 'min:2'],
            'passengers.*.identity_type' => ['nullable', 'string'],
            'passengers.*.identity_number' => ['nullable', 'string'],
            'passengers.*.date_of_birth' => ['nullable', 'date'],
            'passengers.*.seat_number' => ['required', 'string'],
        ]);

        $passengers = $request->input('passengers', []);
        $seatNumbers = collect($passengers)->pluck('seat_number')->filter()->all();
        $duplicateSeats = array_filter(array_count_values($seatNumbers), fn ($count) => $count > 1);
        $invalidSeats = array_diff($seatNumbers, $availableSeats);

        if ($duplicateSeats || $invalidSeats) {
            return back()->withErrors([
                'passengers' => $duplicateSeats
                    ? 'Each passenger must select a unique seat.'
                    : 'One or more selected seats are no longer available.',
            ])->withInput();
        }

        session([
            'booking_wizard.jumlah_tiket' => $jumlahTiket,
            'booking_wizard.passengers' => $passengers,
        ]);

        return redirect()->route('bookings.confirm');
    }

    public function confirm()
    {
        $scheduleId = session('booking_wizard.schedule_id');

        if (! $scheduleId) {
            return redirect()->route('bookings.create')->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $schedule = FlightSchedule::with([
            'flight.route.origin',
            'flight.route.destination',
            'flight.airline',
        ])->findOrFail($scheduleId);

        $passengers = session('booking_wizard.passengers', []);
        $jumlahTiket = session('booking_wizard.jumlah_tiket', count($passengers));

        return view('bookings.wizard.confirm', compact('schedule', 'passengers', 'jumlahTiket'));
    }

    public function store(BookingRequest $request)
    {
        $wizard = $request->boolean('wizard');

        if (! $wizard) {
            return redirect()->route('bookings.create')->with('error', 'Silakan menyelesaikan langkah wizard booking terlebih dahulu.');
        }

        $schedule = FlightSchedule::with(['flight.route.origin', 'flight.route.destination', 'flight.airline'])
            ->findOrFail($request->schedule_id);

        $passengers = collect($request->input('passengers', []))->map(function ($passenger) use ($schedule) {
            return [
                'title' => $passenger['title'] ?? 'Mr',
                'first_name' => $passenger['first_name'],
                'last_name' => $passenger['last_name'],
                'identity_type' => $passenger['identity_type'] ?? 'KTP',
                'identity_number' => $passenger['identity_number'] ?? 'N/A',
                'date_of_birth' => $passenger['date_of_birth'] ?? null,
                'nationality' => $passenger['nationality'] ?? 'ID',
                'passenger_type' => $passenger['passenger_type'] ?? 'adult',
                'seat_number' => $passenger['seat_number'] ?? null,
                'seats' => [$schedule->id => $passenger['seat_number'] ?? null],
            ];
        })->all();

        $booking = $this->bookingService->createBookingDraft(
            [['flight_schedule_id' => $schedule->id, 'cabin_class' => 'economy']],
            $passengers,
            session()->getId(),
            auth()->id()
        );

        $payment = $this->paymentService->createPaymentForBooking($booking, $booking->total_amount);
        $this->paymentCallbackService->handleSuccess($payment->payment_reference, [
            'source' => 'web_wizard',
            'timestamp' => now()->toDateTimeString(),
        ]);

        Notification::create([
            'user_id' => $booking->user_id ?? auth()->id(),
            'title' => 'Booking confirmed',
            'message' => 'Your booking ' . $booking->pnr . ' has been confirmed and is ready for check-in.',
            'type' => 'booking_created',
            'is_read' => false,
        ]);

        session()->forget(['booking_wizard.schedule_id', 'booking_wizard.jumlah_tiket', 'booking_wizard.passengers']);

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dibuat. PNR: ' . $booking->pnr);
    }

    public function destroy(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id && auth()->id() !== $booking->booked_by) {
            abort(403);
        }

        $userId = $booking->user_id ?? $booking->booked_by ?? auth()->id();
        $releasedBySchedule = [];

        foreach ($booking->segments as $segment) {
            foreach ($segment->segmentPassengers as $segmentPassenger) {
                if ($segmentPassenger->seat && $segmentPassenger->seat->status === 'booked') {
                    $scheduleId = $segmentPassenger->seat->flight_schedule_id;
                    $releasedBySchedule[$scheduleId] = ($releasedBySchedule[$scheduleId] ?? 0) + 1;

                    $segmentPassenger->seat->update([
                        'status' => 'available',
                        'booking_id' => null,
                        'locked_by' => null,
                        'lock_session' => null,
                        'locked_until' => null,
                        'reserved_at' => null,
                        'booked_at' => null,
                    ]);
                }
            }
        }

        // Keep the schedule snapshot stable for canceled bookings and rely on
        // flight_schedule_seats status to drive actual seat availability.

        session()->forget([
            'booking_wizard.schedule_id',
            'booking_wizard.jumlah_tiket',
            'booking_wizard.passengers',
        ]);

        Notification::create([
            'user_id' => $userId,
            'title' => 'Booking cancelled',
            'message' => 'Your booking ' . $booking->pnr . ' has been cancelled.',
            'type' => 'booking_cancelled',
            'is_read' => false,
        ]);

        $booking->delete();

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function ticket(Booking $booking)
    {
        if (auth()->id() !== $booking->user_id && auth()->id() !== $booking->booked_by) {
            abort(403);
        }

        $booking = Booking::with([
            'user',
            'schedule',
            'flight',
            'passengers',
            'segments.schedule.flight.route.origin',
            'segments.schedule.flight.route.destination',
            'segments.schedule.flight.airline',
            'segments.segmentPassengers.passenger',
            'segments.segmentPassengers.seat',
            'segments.segmentPassengers.ticket',
        ])->findOrFail($booking->id);

        $primarySegment = $booking->segments->first();
        $schedule = $primarySegment?->schedule ?? $booking->schedule;
        $flight = $primarySegment?->schedule?->flight ?? $booking->flight;
        $route = $flight?->route;

        $departureDateTime = $schedule?->departure_datetime;
        $arrivalDateTime = $schedule?->arrival_datetime;

        if (! $departureDateTime instanceof CarbonInterface) {
            $legacySchedule = $booking->schedule;
            $legacyDate = $legacySchedule?->tanggal;
            $legacyTime = $legacySchedule?->jam_berangkat;

            if ($legacyDate) {
                $parsedDate = $legacyDate instanceof CarbonInterface
                    ? $legacyDate->copy()
                    : \Carbon\Carbon::parse((string) $legacyDate);

                if ($legacyTime) {
                    $parsedTime = \Carbon\Carbon::parse((string) $legacyTime);
                    $departureDateTime = $parsedDate->copy()->setTime(
                        $parsedTime->hour,
                        $parsedTime->minute,
                        $parsedTime->second
                    );
                } else {
                    $departureDateTime = $parsedDate;
                }
            } elseif ($legacyTime) {
                $departureDateTime = \Carbon\Carbon::parse((string) $legacyTime);
            }
        }

        $ticketRows = $booking->segments->flatMap(function ($segment) use ($booking) {
            $routeLabel = trim(($segment->schedule?->flight?->route?->origin?->iata_code ?? 'N/A') . ' → ' . ($segment->schedule?->flight?->route?->destination?->iata_code ?? 'N/A'));

            return $segment->segmentPassengers->map(function ($segmentPassenger, $index) use ($booking, $routeLabel) {
                $passenger = $segmentPassenger->passenger;
                $ticket = $segmentPassenger->ticket;

                return [
                    'no' => $index + 1,
                    'passenger' => trim(($passenger->title ?? '') . ' ' . ($passenger->first_name ?? '') . ' ' . ($passenger->last_name ?? '')),
                    'route' => $routeLabel,
                    'seat' => $segmentPassenger->seat?->seat_number ?? 'Unassigned',
                    'facilities' => '7kg cabin baggage',
                    'ticket_number' => $ticket?->ticket_number ?? 'TK' . strtoupper(substr($booking->pnr ?? 'SKYBOOK', 0, 6)),
                ];
            });
        })->values();

        $payload = sprintf('%s|%s|%s', $booking->id, $booking->pnr, route('bookings.ticket', $booking));
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode((string) QrCode::format('svg')->size(180)->generate($payload));

        $snapshot = [
            'booking_id' => $booking->id,
            'pnr' => $booking->pnr,
            'booking_reference' => $booking->pnr,
            'flight_number' => $flight?->flight_number ?? 'N/A',
            'cabin' => $primarySegment?->cabin_class ?? 'economy',
            'departure_date' => $departureDateTime ? $departureDateTime->format('d M Y') : 'Tanggal belum tersedia',
            'departure_time' => $departureDateTime ? $departureDateTime->format('H:i') : 'Tidak tersedia',
            'arrival_time' => $arrivalDateTime ? $arrivalDateTime->format('H:i') : 'Tidak tersedia',
            'origin_code' => $route?->origin?->iata_code ?? 'N/A',
            'origin_airport' => $route?->origin?->city ?? 'N/A',
            'destination_code' => $route?->destination?->iata_code ?? 'N/A',
            'destination_airport' => $route?->destination?->city ?? 'N/A',
            'ticket_url' => route('bookings.ticket', $booking),
            'rows' => $ticketRows,
        ];

        return view('tickets.eticket', compact('booking', 'snapshot', 'qrCode', 'flight', 'schedule', 'route'));
    }
}
