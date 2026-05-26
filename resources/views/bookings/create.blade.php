<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-skybook-secondary">Step 1 of 3</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Choose your departure</h1>
                <p class="mt-2 text-sm text-slate-500">Select a departure, then choose seats and traveler details before confirming.</p>
            </div>
            <div class="rounded-[20px] border border-slate-200 bg-skybook-accent px-4 py-3 text-sm text-slate-600">
                The passenger wizard now uses the same FlightSchedule inventory and pricing path as the booking API.
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($selectedSchedule)
            <x-admin.card title="Selected departure" description="You came from the search results, and this schedule is preselected for the booking wizard.">
                <div class="rounded-[24px] border border-sky-200 bg-sky-50 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm text-slate-500">{{ $selectedSchedule->flight->route->origin->city ?? 'Origin' }} → {{ $selectedSchedule->flight->route->destination->city ?? 'Destination' }}</p>
                            <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $selectedSchedule->flight->flight_number }}</h2>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-sky-700">Selected</span>
                    </div>
                </div>
            </x-admin.card>
        @endif

        <x-admin.card title="Available departures" description="Pick the flight that fits your journey.">
            @if($schedules->isEmpty())
                <div class="rounded-[20px] border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                    <p class="text-lg font-semibold text-slate-700">No departures available yet</p>
                    <p class="mt-2 text-sm text-slate-500">Please check back later or search a different day.</p>
                </div>
            @else
                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach($schedules as $schedule)
                        @php
                            $isSelected = $selectedSchedule && $selectedSchedule->id === $schedule->id;
                        @endphp
                        <div class="rounded-[24px] border {{ $isSelected ? 'border-sky-300 bg-sky-50/60' : 'border-slate-200 bg-white' }} p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm text-slate-500">{{ $schedule->flight->route->origin->city ?? 'Origin' }} → {{ $schedule->flight->route->destination->city ?? 'Destination' }}</p>
                                    <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $schedule->flight->flight_number }}</h2>
                                    <p class="text-sm text-slate-500">{{ $schedule->flight->airline->name ?? 'SkyBook' }}</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-600">{{ $schedule->available_seats ?? 0 }} seats left</span>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Date</p>
                                    <p class="mt-1 font-semibold text-slate-700">{{ $schedule->departure_datetime->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Depart</p>
                                    <p class="mt-1 font-semibold text-slate-700">{{ $schedule->departure_datetime->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Arrive</p>
                                    <p class="mt-1 font-semibold text-slate-700">{{ $schedule->arrival_datetime->format('H:i') }}</p>
                                </div>
                            </div>

                            <form action="{{ route('bookings.create.select') }}" method="POST" class="mt-5">
                                @csrf
                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                <x-admin.button type="submit" class="w-full">{{ $isSelected ? 'Continue with selected schedule' : 'Continue booking' }}</x-admin.button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $schedules->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-app-layout>
