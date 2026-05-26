<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-3">
                    <a href="{{ route('checkin.portal', ['pnr' => $pnr] + ['signature' => request()->query('signature'), 'expires' => request()->query('expires')]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-slate-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Passenger List
                    </a>
                </div>
                <h2 class="text-[28px] font-bold text-slate-900">Select Seat</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $sp->passenger->first_name }} {{ $sp->passenger->last_name }} • Current Seat: {{ $sp->seat ? $sp->seat->seat_number : 'None' }}</p>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Seat change</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Choose a new seat from the available options.</p>
            </div>
        </div>
    </x-slot>

    @php
        $signedParams = [
            'signature' => request()->query('signature'),
            'expires' => request()->query('expires'),
        ];
        $currentSeatNumber = $sp->seat ? $sp->seat->seat_number : '';
    @endphp

    <div class="space-y-6">
        <x-admin.card>
            <form action="{{ route('checkin.seatmap.update', ['pnr' => $pnr, 'passenger_id' => $sp->id] + $signedParams) }}" method="POST">
                @csrf

                <div class="mb-10 flex flex-wrap justify-center gap-6 border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded border border-emerald-300 bg-emerald-100"></div>
                        <span class="text-sm font-medium text-slate-600">Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded border border-emerald-700 bg-emerald-600"></div>
                        <span class="text-sm font-medium text-slate-600">Selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-6 w-6 rounded border border-slate-300 bg-slate-200"></div>
                        <span class="text-sm font-medium text-slate-600">Booked</span>
                    </div>
                </div>

                <div class="mx-auto max-w-sm rounded-[28px] border-4 border-slate-200 bg-slate-50 p-6 relative">
                    <div class="absolute -top-16 left-1/2 h-16 w-32 -translate-x-1/2 rounded-t-[100px] border-4 border-slate-200 border-b-0 bg-slate-200"></div>

                    <div class="relative z-10 grid grid-cols-7 gap-x-2 gap-y-4 pt-4 text-center">
                        @php $currentRow = null; @endphp
                        @foreach($seats as $seat)
                            @if($currentRow !== $seat->row_number)
                                @if($currentRow !== null)
                                    </div>
                                @endif
                                @php $currentRow = $seat->row_number; @endphp
                                <div class="col-span-1 flex items-center justify-center text-sm font-bold text-slate-400">
                                    {{ $currentRow }}
                                </div>
                            @endif

                            @php
                                $isCurrentSeat = $currentSeatNumber === $seat->seat_number;
                                $isOccupied = ! in_array($seat->status, ['available', 'active'], true);
                                $isSelectable = $isCurrentSeat || $seat->status === 'available';
                            @endphp

                            @if($isSelectable)
                                <label class="relative col-span-1 cursor-pointer">
                                    <input type="radio" name="new_seat" value="{{ $seat->seat_number }}" class="peer sr-only" {{ $isCurrentSeat ? 'checked' : '' }} required>
                                    <div class="flex aspect-square w-full items-center justify-center rounded-md border-2 text-xs font-semibold transition-all peer-checked:border-emerald-700 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-md {{ $isCurrentSeat ? 'border-emerald-700 bg-emerald-600 text-white' : 'border-emerald-200 bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100' }}">
                                        {{ $seat->seat_letter }}
                                    </div>
                                </label>
                            @else
                                <label class="relative col-span-1 cursor-not-allowed">
                                    <input type="radio" name="new_seat" value="{{ $seat->seat_number }}" class="peer sr-only" disabled>
                                    <div class="flex aspect-square w-full items-center justify-center rounded-md border-2 border-slate-200 bg-slate-100 text-xs font-semibold text-slate-400">
                                        {{ $seat->seat_letter }}
                                    </div>
                                </label>
                            @endif

                            @if($loop->iteration % 3 === 0 && $loop->iteration % 6 !== 0)
                                <div class="col-span-1"></div>
                            @endif
                        @endforeach

                        @if($currentRow !== null)
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-10 flex justify-center">
                    <button type="submit" class="rounded-[16px] bg-emerald-600 px-12 py-3 text-lg font-bold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700">
                        Confirm Seat
                    </button>
                </div>
            </form>
        </x-admin.card>
    </div>
</x-app-layout>
