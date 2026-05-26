<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-skybook-secondary">Step 3 of 3</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Confirm your booking</h1>
                <p class="mt-2 text-sm text-slate-500">Review the schedule and traveler details, then create the booking and issue the ticket.</p>
            </div>
            <div class="rounded-[20px] border border-slate-200 bg-skybook-accent px-4 py-3 text-sm text-slate-600">
                This step creates the booking draft, locks the selected seats, and confirms the payment so the ticket can be issued.
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Booking summary" description="Double-check the travel details before submitting.">
            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Flight</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $schedule->flight->flight_number }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $schedule->flight->route->origin->city ?? 'Origin' }} → {{ $schedule->flight->route->destination->city ?? 'Destination' }}</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Date</p>
                            <p class="mt-1 font-semibold text-slate-700">{{ $schedule->departure_datetime->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Time</p>
                            <p class="mt-1 font-semibold text-slate-700">{{ $schedule->departure_datetime->format('H:i') }} - {{ $schedule->arrival_datetime->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Travelers</p>
                    <ul class="mt-3 space-y-2">
                        @foreach($passengers as $passenger)
                            <li class="rounded-[16px] bg-white px-4 py-3 text-sm text-slate-700">
                                <div class="font-semibold">{{ $passenger['first_name'] ?? '' }} {{ $passenger['last_name'] ?? '' }}</div>
                                <div class="mt-1 text-slate-500">Seat: {{ $passenger['seat_number'] ?? 'TBD' }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <form action="{{ route('bookings.store') }}" method="POST" class="mt-6">
                @csrf
                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                <input type="hidden" name="jumlah_tiket" value="{{ $jumlahTiket }}">
                <input type="hidden" name="wizard" value="1">
                @foreach($passengers as $index => $passenger)
                    @foreach($passenger as $field => $value)
                        <input type="hidden" name="passengers[{{ $index }}][{{ $field }}]" value="{{ is_array($value) ? json_encode($value) : $value }}">
                    @endforeach
                @endforeach

                <div class="flex flex-wrap gap-3">
                    <x-admin.button type="submit">Confirm booking</x-admin.button>
                    <x-admin.button href="{{ route('bookings.passengers') }}" variant="secondary">Back</x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-app-layout>
