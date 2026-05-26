<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[28px] font-bold text-slate-900">Booking Portal</h2>
                <p class="mt-1 text-sm text-slate-500">PNR: <span class="tracking-[0.2em]">{{ $booking->pnr }}</span></p>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white px-4 py-3 text-right">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Status</p>
                <p class="mt-1 text-lg font-bold capitalize text-slate-900">{{ $booking->booking_status }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @php
            $flight = $booking->segments->first()->schedule;
        @endphp

        @if($flight->status === 'delayed')
            <x-admin.card title="Flight Delayed" description="Kamu memiliki pembaruan operasional penting untuk perjalanan ini.">
                <div class="flex items-start gap-4">
                    <div class="rounded-[16px] bg-amber-100 p-3 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm text-amber-800">Your flight has been delayed. The new departure time is <span class="font-bold">{{ \Carbon\Carbon::parse($flight->departure_datetime)->format('H:i') }}</span>. Please check your updated boarding pass.</p>
                </div>
            </x-admin.card>
        @elseif($flight->status === 'cancelled')
            <x-admin.card title="Flight Cancelled" description="Perjalanan ini membutuhkan penanganan lanjutan.">
                <div class="flex items-start gap-4">
                    <div class="rounded-[16px] bg-rose-100 p-3 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-sm text-rose-800">We regret to inform you that this flight has been cancelled. Please contact customer support for reaccommodation.</p>
                </div>
            </x-admin.card>
        @endif

        <x-admin.card title="Enhance Your Journey" description="Tambahkan layanan tambahan untuk memperlancar perjalanan Anda.">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm text-slate-500">Add extra baggage, premium meals, or priority boarding to your flight.</p>
                </div>
                <a href="{{ URL::signedRoute('manage-booking.ancillary.catalog', ['pnr' => $booking->pnr]) }}" class="inline-flex items-center gap-2 rounded-[16px] bg-skybook-secondary px-5 py-3 text-sm font-semibold text-white transition hover:bg-skybook-secondary/90">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Services
                </a>
            </div>
        </x-admin.card>

        <x-admin.card title="Booking Timeline" description="Status perjalanan Anda dari pembuatan hingga ticketed.">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-1 items-center justify-between gap-4">
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-skybook-secondary text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Created</span>
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $booking->payment_status === 'paid' ? 'bg-skybook-secondary text-white' : 'bg-slate-200 text-slate-400' }}">
                            @if($booking->payment_status === 'paid')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>
                        <span class="text-xs font-semibold {{ $booking->payment_status === 'paid' ? 'text-slate-700' : 'text-slate-400' }}">Paid</span>
                    </div>

                    <div class="flex flex-col items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $booking->booking_status === 'ticketed' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400' }}">
                            @if($booking->booking_status === 'ticketed')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            @endif
                        </div>
                        <span class="text-xs font-semibold {{ $booking->booking_status === 'ticketed' ? 'text-slate-700' : 'text-slate-400' }}">Ticketed</span>
                    </div>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card title="Flight Itinerary" description="Rangkuman rute, waktu, dan informasi penerbangan.">
            <div class="space-y-4">
                @foreach($booking->segments as $segment)
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-skybook-secondary border border-slate-200">{{ $segment->schedule->flight->airline->name ?? 'Airline' }}</span>
                                    <span class="text-sm text-slate-500">{{ $segment->schedule->flight->flight_number }} • {{ ucfirst($segment->cabin_class) }}</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-3xl font-black text-slate-900">{{ $segment->schedule->flight->route->origin->iata_code }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $segment->schedule->departure_datetime->format('H:i') }}</p>
                                        <p class="text-xs text-slate-400">{{ $segment->schedule->departure_datetime->format('d M Y') }}</p>
                                    </div>
                                    <div class="flex-1 px-4">
                                        <div class="border-t-2 border-dashed border-slate-300"></div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-3xl font-black text-slate-900">{{ $segment->schedule->flight->route->destination->iata_code }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $segment->schedule->arrival_datetime->format('H:i') }}</p>
                                        <p class="text-xs text-slate-400">{{ $segment->schedule->arrival_datetime->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>

        <x-admin.card title="Passengers & E-Tickets" description="Maskapai dan tiket untuk setiap penumpang dalam booking ini.">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-600">
                    <thead class="bg-[#F5F6FA] text-[11px] uppercase tracking-[0.18em] text-slate-700">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Passenger</th>
                            <th class="px-6 py-4 font-semibold hidden md:table-cell">Identity</th>
                            <th class="px-6 py-4 font-semibold">Seat</th>
                            <th class="px-6 py-4 font-semibold text-right">E-Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EAEAEA] bg-white">
                        @foreach($booking->passengers as $passenger)
                            @php
                                $ticket = null;
                                $seatNum = '-';
                                $ancillaries = collect();
                                if($booking->segments->isNotEmpty()) {
                                    $sp = $booking->segments->first()->segmentPassengers->where('passenger_id', $passenger->id)->first();
                                    if($sp) {
                                        $ticket = $sp->ticket;
                                        $seatNum = $sp->seat ? $sp->seat->seat_number : '-';
                                        $ancillaries = $sp->ancillaries ?? collect();
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}</div>
                                    <div class="text-xs text-slate-500 capitalize">{{ $passenger->passenger_type }}</div>
                                    @if($ancillaries->count() > 0)
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($ancillaries as $ancillary)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $ancillary->snapshot_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-sm text-slate-600">
                                    {{ $passenger->identity_type }} - {{ Str::mask($passenger->identity_number, '*', 4, -4) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $seatNum }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($ticket && $ticket->document_path)
                                        <a href="{{ route('tickets.download', $ticket->id) }}" class="inline-flex items-center gap-2 rounded-[16px] border border-skybook-secondary/20 bg-sky-50 px-4 py-2 text-sm font-semibold text-skybook-secondary">
                                            Download PDF
                                        </a>
                                    @else
                                        <span class="text-sm text-slate-400 italic">Processing...</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    </div>
</x-app-layout>
