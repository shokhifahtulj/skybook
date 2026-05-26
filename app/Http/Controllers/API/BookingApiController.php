<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Schedule;
use App\Services\Booking\BookingAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingApiController extends Controller
{
    public function __construct(protected BookingAvailabilityService $availabilityService)
    {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user?->role === 'admin';
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $paymentStatus = trim((string) $request->input('payment_status', ''));
        $dateFrom = $this->normalizeDateFilter($request->input('date_from'));
        $dateTo = $this->normalizeDateFilter($request->input('date_to'));
        $perPage = max(1, min(100, (int) $request->input('per_page', 15)));
        $sortField = $this->sanitizeSort($request->input('sort'), ['created_at', 'updated_at', 'pnr', 'booking_status']);
        $direction = $this->sanitizeDirection($request->input('direction'));

        $query = Booking::query()
            ->with(['schedule.flight', 'flight', 'user', 'bookedBy'])
            ->when(! $isAdmin, fn ($scope) => $scope->where(fn ($bookingScope) => $bookingScope
                ->where('user_id', $user?->id)
                ->orWhere('booked_by', $user?->id)));

        if ($status !== '') {
            $query->where('booking_status', $status);
        }

        if ($paymentStatus !== '') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($search !== '') {
            $query->where(function ($scope) use ($search) {
                $scope->where('pnr', 'like', "%{$search}%")
                    ->orWhere('booking_status', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userScope) => $userScope->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bookedBy', fn ($bookedByScope) => $bookedByScope->where('name', 'like', "%{$search}%"));
            });
        }

        if ($dateFrom !== null || $dateTo !== null) {
            $query->whereHas('schedule', function ($scheduleQuery) use ($dateFrom, $dateTo) {
                if ($dateFrom !== null) {
                    $scheduleQuery->whereDate('tanggal', '>=', $dateFrom);
                }

                if ($dateTo !== null) {
                    $scheduleQuery->whereDate('tanggal', '<=', $dateTo);
                }
            });
        }

        $paginator = $query->orderBy($sortField, $direction)
            ->paginate($perPage);

        return $this->paginatedResponse(
            'Data berhasil ditampilkan',
            $paginator,
            fn (Booking $booking) => $booking,
            [
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
                'sort' => $sortField,
                'direction' => $direction,
            ]
        );
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['schedule.flight', 'flight', 'user', 'bookedBy']);

        return $this->successResponse('Data berhasil ditampilkan', $booking);
    }

    public function store(BookingRequest $request)
    {
        $this->authorize('create', Booking::class);

        $validated = $request->validated();
        $schedule = Schedule::findOrFail($validated['schedule_id']);

        if (! $this->availabilityService->canBook($schedule, (int) $validated['jumlah_tiket'])) {
            return $this->errorResponse('Jumlah tiket melebihi kapasitas yang tersedia.', 422);
        }

        $this->availabilityService->reserve($schedule, (int) $validated['jumlah_tiket']);

        $booking = Booking::create([
            'pnr' => strtoupper(Str::random(6)),
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'total_amount' => 0,
            'currency' => 'IDR',
            'booked_by' => auth()->id(),
            'user_id' => auth()->id(),
            'schedule_id' => $schedule->id,
            'flight_id' => $schedule->flight_id,
            'jumlah_tiket' => (int) $validated['jumlah_tiket'],
            'total_harga' => 0,
            'status_booking' => 'confirmed',
            'expires_at' => now()->addMinutes(15),
        ]);

        $booking->load(['schedule.flight', 'flight', 'user', 'bookedBy']);

        return $this->successResponse('Booking berhasil dibuat', $booking);
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validated();
        $newSchedule = Schedule::findOrFail($validated['schedule_id']);
        $oldQuantity = (int) ($booking->jumlah_tiket ?? 1);
        $oldSchedule = $booking->schedule;

        if ($oldSchedule && $oldSchedule->id !== $newSchedule->id) {
            $this->availabilityService->release($oldSchedule, $oldQuantity);
        }

        $delta = (int) $validated['jumlah_tiket'] - $oldQuantity;

        if ($delta > 0 && ! $this->availabilityService->canBook($newSchedule, $delta)) {
            return $this->errorResponse('Jumlah tiket melebihi kapasitas yang tersedia.', 422);
        }

        if ($delta > 0) {
            $this->availabilityService->reserve($newSchedule, $delta);
        }

        if ($delta < 0) {
            $this->availabilityService->release($newSchedule, abs($delta));
        }

        $booking->update([
            'schedule_id' => $newSchedule->id,
            'flight_id' => $newSchedule->flight_id,
            'jumlah_tiket' => (int) $validated['jumlah_tiket'],
            'total_harga' => 0,
            'status_booking' => 'confirmed',
            'booking_status' => $booking->booking_status ?: 'confirmed',
            'payment_status' => $booking->payment_status ?: 'paid',
            'total_amount' => $booking->total_amount ?? 0,
            'currency' => $booking->currency ?: 'IDR',
            'booked_by' => $booking->booked_by ?? auth()->id(),
            'user_id' => $booking->user_id ?? auth()->id(),
        ]);

        $booking->load(['schedule.flight', 'flight', 'user', 'bookedBy']);

        return $this->successResponse('Booking berhasil diperbarui', $booking);
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        if ($booking->schedule) {
            $this->availabilityService->release($booking->schedule, (int) ($booking->jumlah_tiket ?? 1));
        }

        $booking->delete();

        return $this->successResponse('Booking berhasil dibatalkan', null);
    }

    protected function paginatedResponse(string $message, $paginator, callable $transformer, array $meta = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->getCollection()->map($transformer)->values(),
            'meta' => array_merge([
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ], $meta),
        ]);
    }

    protected function sanitizeSort(?string $sort, array $allowed): string
    {
        $sort = trim((string) $sort);

        return in_array($sort, $allowed, true) ? $sort : $allowed[0];
    }

    protected function sanitizeDirection(?string $direction): string
    {
        $direction = strtolower(trim((string) $direction));

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }

    protected function normalizeDateFilter(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function successResponse(string $message, mixed $data, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $status);
    }
}

