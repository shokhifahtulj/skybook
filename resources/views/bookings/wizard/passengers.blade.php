<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-skybook-secondary">Step 2 of 3</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Traveler details</h1>
                <p class="mt-2 text-sm text-slate-500">Complete the passenger information and seat selection for {{ $schedule->flight->flight_number }} on {{ $schedule->departure_datetime->format('d M Y') }}.</p>
            </div>
            <div class="rounded-[20px] border border-slate-200 bg-skybook-accent px-4 py-3 text-sm text-slate-600">
                Selected schedule: {{ $schedule->flight->route->origin->city ?? 'Origin' }} → {{ $schedule->flight->route->destination->city ?? 'Destination' }}
            </div>
        </div>
    </x-slot>

    @php
        $passengerCount = max((int) old('jumlah_tiket', $jumlahTiket), 1);
        $currentPassengers = old('passengers', $storedPassengers);
        $allSeats = $schedule->seats->sortBy(function ($seat) {
            $row = (int) preg_replace('/[^0-9]/', '', (string) $seat->seat_number);
            $letter = strtoupper((string) preg_replace('/[^A-Z]/', '', (string) $seat->seat_number));

            return sprintf('%03d-%s', $row, $letter);
        });
        $availableSeats = $allSeats->where('status', 'available');
        $occupiedSeats = $allSeats->where('status', '!=', 'available')->count();
        $rows = $allSeats->groupBy(function ($seat) {
            return (int) preg_replace('/[^0-9]/', '', (string) $seat->seat_number);
        });
        $selectedSeats = collect($currentPassengers)
            ->pluck('seat_number')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $selectedSeatSummary = ! empty($selectedSeats) ? implode(', ', $selectedSeats) : 'No seat selected yet';
        $selectedSeatMap = collect($currentPassengers)
            ->mapWithKeys(function ($passenger, $index) {
                return [(int) $index => data_get($passenger, 'seat_number', '')];
            })
            ->all();
    @endphp

    <div class="space-y-6">
        <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Flight summary</p>
                <h2 class="mt-3 text-2xl font-bold text-slate-900">{{ $schedule->flight->flight_number }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $schedule->flight->airline->name ?? 'SkyBook' }}</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Route</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $schedule->flight->route->origin->city ?? 'Origin' }} → {{ $schedule->flight->route->destination->city ?? 'Destination' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Departure</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $schedule->departure_datetime->format('D, d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Arrival</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $schedule->arrival_datetime->format('D, d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Cabin</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">Economy</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Traveler details</p>
                <div class="mt-4 space-y-3">
                    <div class="rounded-[18px] bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Passengers</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $passengerCount }}</p>
                    </div>
                    <div class="rounded-[18px] bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Available seats</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $availableSeats->count() }}</p>
                    </div>
                    <div class="rounded-[18px] bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Occupied seats</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900">{{ $occupiedSeats }}</p>
                    </div>
                    <div class="rounded-[18px] bg-white px-4 py-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Selected seats</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $selectedSeatSummary }}</p>
                    </div>
                </div>
            </div>
        </div>

        <x-admin.card title="Passenger information" description="Enter traveler details and choose a seat for each passenger before proceeding to review.">
            <form id="bookingPassengerForm" action="{{ route('bookings.passengers.save') }}" method="POST">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="jumlah_tiket" class="mb-2 block text-sm font-semibold text-slate-700">How many passengers?</label>
                        <select id="jumlah_tiket" name="jumlah_tiket" class="h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20">
                            @for($i = 1; $i <= max(5, $jumlahTiket); $i++)
                                <option value="{{ $i }}" {{ (int) old('jumlah_tiket', $jumlahTiket) === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('jumlah_tiket')
                            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="rounded-[20px] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        <p class="font-semibold text-slate-700">Tip</p>
                        <p class="mt-2">Seats are locked when the booking is confirmed, so choose the seat for each traveler before reviewing the booking.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @for($i = 0; $i < $passengerCount; $i++)
                        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-900">Traveler {{ $i + 1 }}</h2>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                <x-admin.input name="passengers[{{ $i }}][first_name]" label="First name" :value="old('passengers.' . $i . '.first_name', $currentPassengers[$i]['first_name'] ?? '')" required />
                                <x-admin.input name="passengers[{{ $i }}][last_name]" label="Last name" :value="old('passengers.' . $i . '.last_name', $currentPassengers[$i]['last_name'] ?? '')" required />
                                <div>
                                    <label for="passengers[{{ $i }}][identity_type]" class="mb-2 block text-sm font-semibold text-slate-700">Identity type</label>
                                    <select id="passengers[{{ $i }}][identity_type]" name="passengers[{{ $i }}][identity_type]" class="h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20">
                                        <option value="">Select</option>
                                        <option value="passport" {{ old('passengers.' . $i . '.identity_type', $currentPassengers[$i]['identity_type'] ?? '') === 'passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="national_id" {{ old('passengers.' . $i . '.identity_type', $currentPassengers[$i]['identity_type'] ?? '') === 'national_id' ? 'selected' : '' }}>National ID</option>
                                    </select>
                                </div>
                                <x-admin.input name="passengers[{{ $i }}][identity_number]" label="Identity number" :value="old('passengers.' . $i . '.identity_number', $currentPassengers[$i]['identity_number'] ?? '')" />
                                <x-admin.input name="passengers[{{ $i }}][date_of_birth]" type="date" label="Date of birth" :value="old('passengers.' . $i . '.date_of_birth', $currentPassengers[$i]['date_of_birth'] ?? '')" />
                                <div>
                                    <label for="passengers[{{ $i }}][nationality]" class="mb-2 block text-sm font-semibold text-slate-700">Nationality</label>
                                    <input type="text" id="passengers[{{ $i }}][nationality]" name="passengers[{{ $i }}][nationality]" value="{{ old('passengers.' . $i . '.nationality', $currentPassengers[$i]['nationality'] ?? 'ID') }}" class="h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20">
                                </div>
                            </div>

                            <div class="mt-4 rounded-[20px] border border-slate-200 bg-white p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">Seat selection</p>
                                        <p class="text-sm text-slate-500">Click a seat to assign it to Traveler {{ $i + 1 }}. Occupied seats are disabled.</p>
                                    </div>
                                    <div class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">
                                        Selected: <span id="seat-selected-{{ $i }}" class="font-semibold text-slate-900">{{ old('passengers.' . $i . '.seat_number', $currentPassengers[$i]['seat_number'] ?? 'None') }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 overflow-x-auto">
                                    <div class="min-w-[34rem] space-y-2">
                                        <div class="grid grid-cols-[3rem_repeat(6,minmax(0,1fr))] gap-2">
                                            <div></div>
                                            @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                                <div class="text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $letter }}</div>
                                            @endforeach
                                        </div>

                                        @foreach($rows as $rowNumber => $rowSeats)
                                            @php
                                                $seatMap = $rowSeats->keyBy(fn ($seat) => strtoupper((string) preg_replace('/[^A-Z]/', '', (string) $seat->seat_number)));
                                            @endphp
                                            <div class="grid grid-cols-[3rem_repeat(6,minmax(0,1fr))] gap-2 items-center">
                                                <div class="text-center text-sm font-semibold text-slate-600">{{ $rowNumber }}</div>
                                                @foreach(['A', 'B', 'C', 'D', 'E', 'F'] as $letter)
                                                    @php
                                                        $seat = $seatMap->get($letter);
                                                        $currentSelection = old('passengers.' . $i . '.seat_number', $selectedSeatMap[$i] ?? '');
                                                        $isSelected = $seat && $currentSelection === $seat->seat_number;
                                                        $isAvailable = $seat?->status === 'available';
                                                        $isSelectedByOtherPassenger = collect($selectedSeatMap)
                                                            ->reject(fn ($selectedSeat, $passengerIndex) => (int) $passengerIndex === (int) $i)
                                                            ->contains(fn ($selectedSeat) => $selectedSeat === $seat?->seat_number);
                                                        $isDisabled = $seat === null || $isSelectedByOtherPassenger || (! $isAvailable && ! $isSelected);
                                                        $buttonTitle = $seat === null
                                                            ? 'Seat not available in this layout'
                                                            : ($isSelectedByOtherPassenger
                                                                ? 'Reserved by another traveler'
                                                                : (! $isAvailable && ! $isSelected
                                                                    ? 'Seat is currently unavailable'
                                                                    : 'Click to assign this seat'));
                                                    @endphp

                                                    @if($seat)
                                                        <button
                                                            type="button"
                                                            data-seat-button
                                                            data-passenger-index="{{ $i }}"
                                                            data-seat-number="{{ $seat->seat_number }}"
                                                            data-seat-status="{{ $seat->status }}"
                                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                                            aria-disabled="{{ $isDisabled ? 'true' : 'false' }}"
                                                            title="{{ $buttonTitle }}"
                                                            @if($isDisabled) disabled @endif
                                                            class="h-11 rounded-[12px] border px-3 text-sm font-semibold transition {{ $isSelected ? 'border-skybook-secondary bg-skybook-secondary text-white' : ($isDisabled ? 'border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:border-skybook-secondary') }}"
                                                        >
                                                            {{ $seat->seat_number }}
                                                        </button>
                                                    @else
                                                        <div class="h-11 rounded-[12px] border border-dashed border-slate-200 bg-slate-50"></div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Available</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Occupied</span>
                                    <span class="rounded-full bg-skybook-secondary/10 px-3 py-1 text-xs font-semibold text-skybook-secondary">Selected</span>
                                </div>

                                <input type="hidden" id="seat-hidden-{{ $i }}" name="passengers[{{ $i }}][seat_number]" value="{{ old('passengers.' . $i . '.seat_number', $currentPassengers[$i]['seat_number'] ?? '') }}" data-seat-hidden>
                                @error('passengers.' . $i . '.seat_number')
                                    <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <x-admin.button type="submit" id="continueBooking" class="disabled:cursor-not-allowed disabled:opacity-50" disabled>Continue</x-admin.button>
                    <x-admin.button href="{{ route('bookings.create', ['schedule_id' => $schedule->id]) }}" variant="secondary">Previous</x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const continueButton = document.getElementById('continueBooking');
            const seatButtons = document.querySelectorAll('[data-seat-button]');
            const hiddenInputs = document.querySelectorAll('[data-seat-hidden]');

            const setButtonState = (button, isSelected) => {
                button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                button.className = button.className.replace(/border-skybook-secondary bg-skybook-secondary text-white/g, '').replace(/border-slate-200 bg-white text-slate-700 hover:border-skybook-secondary/g, '').replace(/border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed/g, '');

                if (isSelected) {
                    button.className += ' border-skybook-secondary bg-skybook-secondary text-white';
                    return;
                }

                if (button.disabled) {
                    button.className += ' border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed';
                    return;
                }

                button.className += ' border-slate-200 bg-white text-slate-700 hover:border-skybook-secondary';
            };

            const getSelectedSeats = () => {
                const selectedSeats = [];

                hiddenInputs.forEach((input) => {
                    const passengerIndex = input.id.replace('seat-hidden-', '');
                    const seatNumber = input.value ? input.value.trim() : '';

                    if (seatNumber) {
                        selectedSeats.push({ passengerIndex, seatNumber });
                    }
                });

                return selectedSeats;
            };

            const syncSelectionState = () => {
                const selectedSeats = getSelectedSeats();

                seatButtons.forEach((button) => {
                    const passengerIndex = button.dataset.passengerIndex;
                    const hiddenInput = document.getElementById('seat-hidden-' + passengerIndex);
                    const selectedLabel = document.getElementById('seat-selected-' + passengerIndex);
                    const selectedSeat = hiddenInput ? hiddenInput.value : '';
                    const isCurrentSelection = selectedSeat === button.dataset.seatNumber;
                    const isSelectedByAnotherPassenger = selectedSeats.some((entry) => entry.passengerIndex !== passengerIndex && entry.seatNumber === button.dataset.seatNumber);
                    const isSeatAvailable = button.dataset.seatStatus === 'available';

                    button.disabled = (isSelectedByAnotherPassenger || (!isSeatAvailable && !isCurrentSelection));
                    button.setAttribute('aria-disabled', String(button.disabled));
                    setButtonState(button, isCurrentSelection);

                    if (selectedLabel) {
                        selectedLabel.textContent = selectedSeat || 'None';
                    }
                });

                updateContinueState();
            };

            const updateContinueState = () => {
                let allSelected = true;

                hiddenInputs.forEach((input) => {
                    if (!input.value) {
                        allSelected = false;
                    }
                });

                if (continueButton) {
                    continueButton.disabled = !allSelected;
                }
            };

            seatButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const passengerIndex = button.dataset.passengerIndex;
                    const seatNumber = button.dataset.seatNumber;
                    const hiddenInput = document.getElementById('seat-hidden-' + passengerIndex);
                    const selectedLabel = document.getElementById('seat-selected-' + passengerIndex);

                    if (!hiddenInput || !selectedLabel || button.disabled) {
                        return;
                    }

                    hiddenInput.value = seatNumber;
                    selectedLabel.textContent = seatNumber;
                    syncSelectionState();
                });
            });

            syncSelectionState();
        });
    </script>
</x-app-layout>
