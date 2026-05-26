<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Baggage Drop Counter</h2>
            <p class="text-sm text-slate-500">Scan or enter passenger PNR to process checked baggage.</p>
        </div>
    </div>

    @if(session('open_pdf'))
        <script>
            window.open("{{ session('open_pdf') }}", "_blank");
        </script>
    @endif

    <x-admin.card>
        <form action="{{ route('admin.operations.baggage.drop') }}" method="GET" class="flex gap-4 max-w-xl">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Enter PNR..." class="w-full rounded-lg border-slate-300 uppercase tracking-widest" required>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">Search</button>
        </form>
    </x-admin.card>

    @if($search)
        <div class="mt-8">
            <h3 class="font-bold text-lg text-slate-800 mb-4">Results for PNR: <span class="uppercase">{{ $search }}</span></h3>
            
            @if($ancillaries->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-700 p-4 rounded-lg">
                    No prepaid baggage found for this PNR. Passengers must purchase baggage allowance first.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($ancillaries as $ancillary)
                        @php
                            $passenger = $ancillary->bookingSegmentPassenger->passenger;
                            $flight = $ancillary->bookingSegmentPassenger->bookingSegment->flightSchedule->flight;
                            $allowance = $ancillary->metadata['weight_kg'] ?? 0;
                            $used = $ancillary->baggageTags->sum('weight_kg');
                            $remaining = $allowance - $used;
                        @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                            <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-4">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-lg">{{ $passenger->first_name }} {{ $passenger->last_name }}</h4>
                                    <p class="text-sm text-slate-500">Flight: {{ $flight->flight_number }} | {{ $flight->route->origin->iata_code }} &rarr; {{ $flight->route->destination->iata_code }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-bold uppercase text-slate-400">Allowance</div>
                                    <div class="text-xl font-black text-indigo-600">{{ $allowance }} KG</div>
                                </div>
                            </div>

                            @if($ancillary->baggageTags->count() > 0)
                                <div class="mb-4">
                                    <h5 class="text-xs font-bold text-slate-500 uppercase mb-2">Processed Tags</h5>
                                    <ul class="space-y-2">
                                        @foreach($ancillary->baggageTags as $tag)
                                            <li class="flex justify-between items-center text-sm bg-slate-50 px-3 py-2 rounded border border-slate-100">
                                                <span class="font-mono font-bold">{{ $tag->tag_number }}</span>
                                                <div class="flex items-center gap-4">
                                                    <span class="font-semibold text-slate-600">{{ $tag->weight_kg }} kg</span>
                                                    <a href="{{ route('api.baggage-tags.render', $tag->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="Reprint">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($remaining > 0)
                                <form action="{{ route('admin.operations.baggage.generate') }}" method="POST" class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                                    @csrf
                                    <input type="hidden" name="ancillary_id" value="{{ $ancillary->id }}">
                                    <div class="flex items-end gap-3">
                                        <div class="flex-1">
                                            <label class="block text-xs font-bold text-indigo-800 mb-1">New Bag Weight (KG)</label>
                                            <input type="number" name="weight_kg" max="{{ $remaining }}" step="0.1" required class="w-full rounded border-indigo-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            <p class="text-xs text-indigo-600 mt-1">Max {{ $remaining }} kg remaining</p>
                                        </div>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-bold text-sm h-[42px] transition mb-6">
                                            Print Tag
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-3 rounded text-sm text-center font-bold">
                                    Allowance fully utilized
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</x-admin-layout>
