<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[28px] font-bold text-slate-900">Search Flights</h2>
                <p class="mt-1 text-sm text-slate-500">Find the best available departure and continue directly to booking.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Search flights" description="Filter by origin, destination, date, or airline to find your next departure.">
            <form method="GET" action="{{ route('search.flights') }}" class="grid gap-4 lg:grid-cols-4">
                <div>
                    <label for="origin" class="mb-2 block text-sm font-semibold text-slate-700">Origin</label>
                    <input id="origin" name="origin" type="text" value="{{ old('origin', $filters['origin'] ?? '') }}" placeholder="Jakarta" class="w-full rounded-[16px] border border-slate-200 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none">
                </div>
                <div>
                    <label for="destination" class="mb-2 block text-sm font-semibold text-slate-700">Destination</label>
                    <input id="destination" name="destination" type="text" value="{{ old('destination', $filters['destination'] ?? '') }}" placeholder="Denpasar" class="w-full rounded-[16px] border border-slate-200 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none">
                </div>
                <div>
                    <label for="departure_date" class="mb-2 block text-sm font-semibold text-slate-700">Departure Date</label>
                    <input id="departure_date" name="departure_date" type="date" value="{{ old('departure_date', $filters['departure_date'] ?? '') }}" class="w-full rounded-[16px] border border-slate-200 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none">
                </div>
                <div>
                    <label for="airline" class="mb-2 block text-sm font-semibold text-slate-700">Airline</label>
                    <input id="airline" name="airline" type="text" value="{{ old('airline', $filters['airline'] ?? '') }}" placeholder="SkyBook Air" class="w-full rounded-[16px] border border-slate-200 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none">
                </div>
                <div class="lg:col-span-4 flex flex-wrap gap-3">
                    <x-admin.button type="submit">Search flights</x-admin.button>
                    <a href="{{ route('search.flights') }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                </div>
            </form>
        </x-admin.card>

        <x-admin.card title="Results" description="Choose the departure you want and continue to the booking wizard.">
            @if($flights->isEmpty())
                <div class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                    <p class="text-lg font-semibold text-slate-700">No flights found</p>
                    <p class="mt-2 text-sm text-slate-500">Try adjusting your search filters or check back later for new departures.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($flights as $flight)
                        @php
                            $nextSchedule = $flight->schedules->sortBy('departure_datetime')->first();
                        @endphp
                        <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $flight->airline->name ?? 'SkyBook' }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $flight->flight_number }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900">{{ $flight->route->origin->city ?? 'Origin' }} → {{ $flight->route->destination->city ?? 'Destination' }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $flight->route->origin->iata_code ?? 'N/A' }} to {{ $flight->route->destination->iata_code ?? 'N/A' }}</p>
                                    </div>
                                    @if($nextSchedule)
                                        <div class="grid gap-3 sm:grid-cols-3">
                                            <div>
                                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Departure</p>
                                                <p class="mt-1 font-semibold text-slate-700">{{ $nextSchedule->departure_datetime->format('D, d M Y H:i') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Arrival</p>
                                                <p class="mt-1 font-semibold text-slate-700">{{ $nextSchedule->arrival_datetime->format('D, d M Y H:i') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Seats left</p>
                                                <p class="mt-1 font-semibold text-slate-700">{{ $nextSchedule->available_seats ?? 0 }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-amber-600">No upcoming departure is currently available for this flight.</p>
                                    @endif
                                </div>

                                <div class="flex flex-col gap-3 sm:min-w-[220px]">
                                    @if($nextSchedule)
                                        <a href="{{ route('bookings.create', ['schedule_id' => $nextSchedule->id]) }}" class="inline-flex items-center justify-center rounded-[16px] bg-skybook-secondary px-5 py-3 text-sm font-semibold text-white transition hover:bg-skybook-secondary/90">Book Now</a>
                                    @else
                                        <span class="inline-flex items-center justify-center rounded-[16px] bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-400">Book Now</span>
                                    @endif
                                    <a href="{{ route('flights.show', $flight) }}" class="inline-flex items-center justify-center rounded-[16px] border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View details</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $flights->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-app-layout>
