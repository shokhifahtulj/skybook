<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[28px] font-bold text-slate-900">Select Passengers</h2>
                <p class="mt-1 text-sm text-slate-500">PNR: <span class="tracking-[0.2em]">{{ $booking->pnr }}</span></p>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white px-4 py-3">
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Check-in status</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">Complete passenger selection and confirm boarding.</p>
            </div>
        </div>
    </x-slot>

    @php
        $signedParams = [
            'signature' => request()->query('signature'),
            'expires' => request()->query('expires'),
        ];
    @endphp

    <div class="space-y-6">
        <form action="{{ route('checkin.process', ['pnr' => $booking->pnr] + $signedParams) }}" method="POST">
            @csrf

            @foreach($booking->segments as $segment)
                <x-admin.card title="{{ $segment->schedule->flight->route->origin->iata_code }} → {{ $segment->schedule->flight->route->destination->iata_code }}" description="{{ $segment->schedule->departure_datetime->format('d M Y, H:i') }} • {{ $segment->schedule->flight->flight_number }}">
                    <div class="space-y-4">
                        @foreach($segment->segmentPassengers as $sp)
                            @php
                                $status = $passengersStatus[$sp->id];
                                $isEligible = $status['is_eligible'];
                                $isCheckedIn = $sp->operational_status === 'checked_in';
                            @endphp

                            <div class="flex flex-col gap-4 rounded-[20px] border p-4 sm:flex-row sm:items-center sm:justify-between {{ $isCheckedIn ? 'border-emerald-200 bg-emerald-50/30' : ($isEligible ? 'border-slate-200 bg-slate-50' : 'border-slate-100 bg-slate-50 opacity-75') }}">
                                <div class="flex items-center gap-4">
                                    @if($isCheckedIn)
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @elseif($isEligible)
                                        <input type="checkbox" name="passenger_segment_ids[]" value="{{ $sp->id }}" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    @else
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-200">
                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="font-bold text-slate-800">{{ $sp->passenger->title }} {{ $sp->passenger->first_name }} {{ $sp->passenger->last_name }}</div>
                                        <div class="text-xs font-medium {{ $isCheckedIn ? 'text-emerald-600' : ($isEligible ? 'text-slate-500' : 'text-red-500') }}">
                                            @if($isCheckedIn)
                                                Checked In
                                            @elseif($isEligible)
                                                Eligible for Check-in
                                            @else
                                                {{ $status['reason'] }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Seat</span>
                                        <span class="block text-lg font-bold text-slate-900">{{ $sp->seat ? $sp->seat->seat_number : 'N/A' }}</span>
                                    </div>

                                    @if(!$isCheckedIn && $isEligible)
                                        <a href="{{ route('checkin.seatmap', ['pnr' => $booking->pnr, 'passenger_id' => $sp->id] + $signedParams) }}" class="rounded-[14px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                            Change
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-admin.card>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="rounded-[16px] bg-emerald-600 px-8 py-3 text-lg font-bold text-white shadow-lg shadow-emerald-600/30 transition hover:bg-emerald-700">
                    Check-in Selected
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
