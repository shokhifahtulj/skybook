<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-skybook-secondary">Flight details</p>
                <h2 class="mt-2 text-[28px] font-bold text-slate-900">{{ $flight->flight_number }} · {{ $flight->airline->name ?? 'SkyBook' }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $flight->route->origin->city ?? 'Origin' }} → {{ $flight->route->destination->city ?? 'Destination' }}</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to booking</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Route overview" description="Additional flight details and cabin information for the selected departure.">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[18px] border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Airline</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $flight->airline->name ?? 'SkyBook' }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Origin</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $flight->route->origin->city ?? 'Origin' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $flight->route->origin->iata_code ?? 'N/A' }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Destination</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $flight->route->destination->city ?? 'Destination' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $flight->route->destination->iata_code ?? 'N/A' }}</p>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card title="Available schedules" description="Choose the departing schedule that you want to continue booking for.">
            <div class="space-y-4">
                @forelse($flight->schedules as $schedule)
                    @php
                        $availableSeats = $schedule->available_seats ?? $schedule->seats()->where('status', 'available')->count();
                    @endphp
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="text-lg font-bold text-slate-900">{{ $schedule->departure_datetime->format('d M Y') }}</div>
                                <div class="mt-1 text-sm text-slate-500">Depart {{ $schedule->departure_datetime->format('H:i') }} — Arrive {{ $schedule->arrival_datetime->format('H:i') }}</div>
                                <div class="mt-1 text-sm text-slate-500">Available seats: {{ $availableSeats }}</div>
                            </div>
                            <div class="flex flex-col gap-2 md:items-end">
                                <a href="{{ route('bookings.create', ['schedule_id' => $schedule->id]) }}" class="inline-flex items-center justify-center rounded-[16px] bg-skybook-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-skybook-secondary/90">Continue booking</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No schedules are currently available for this flight.</p>
                @endforelse
            </div>
        </x-admin.card>
    </div>
</x-app-layout>
