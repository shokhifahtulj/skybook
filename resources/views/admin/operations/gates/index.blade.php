<x-admin-layout>
    <div class="space-y-6">
        @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-lg p-4 flex items-center gap-3 font-semibold">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Hub Selector -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Airport Surface Dashboard</h3>
                    <p class="text-sm text-slate-500">Monitor gate occupancy, conflicts, and towing windows.</p>
                </div>
                <form action="{{ route('admin.operations.gates') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <select name="airport_id" class="rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1 sm:w-64" onchange="this.form.submit()">
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}" {{ $selectedAirport && $selectedAirport->id === $airport->id ? 'selected' : '' }}>
                                {{ $airport->iata_code }} - {{ $airport->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Gate Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Gate Occupancy (Today)</h3>
                    <div class="flex gap-4 text-xs font-medium">
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded"></div> Departure (-60m to +15m)</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-emerald-100 border border-emerald-300 rounded"></div> Arrival (+0m to +45m)</span>
                        <span class="flex items-center gap-1"><div class="w-3 h-3 bg-rose-100 border border-rose-300 rounded"></div> Conflict</span>
                    </div>
                </div>
                
                <div class="p-0 overflow-x-auto">
                    @if($gates->isEmpty())
                        <div class="p-8 text-center text-slate-500">
                            No gates defined for this airport.
                        </div>
                    @else
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-3 w-32">Gate</th>
                                    <th class="px-6 py-3">Scheduled Occupancy Timeline</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($gates as $gate)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-slate-800 border-r border-slate-100 bg-slate-50/30">
                                            T{{ $gate->terminal }} - {{ $gate->gate_number }}
                                            @if($gate->status !== 'active')
                                                <span class="block text-xs text-rose-500 mt-1 uppercase">{{ $gate->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2 items-center">
                                                @foreach($gate->departureSchedules as $depSchedule)
                                                    <div class="bg-blue-50 border border-blue-200 rounded p-2 text-xs flex flex-col gap-1 w-48 shrink-0 relative group">
                                                        <div class="font-bold text-blue-800 flex justify-between">
                                                            <span>DEP: {{ $depSchedule->flight->flight_number }}</span>
                                                            <span class="text-blue-500">{{ $depSchedule->departure_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="text-slate-500">To: {{ $depSchedule->flight->route->destination->iata_code }}</div>
                                                        <div class="text-blue-600 font-mono text-[10px]">Occupy: {{ $depSchedule->departure_datetime->copy()->subMinutes(60)->format('H:i') }} - {{ $depSchedule->departure_datetime->copy()->addMinutes(15)->format('H:i') }}</div>
                                                        
                                                        <!-- Hover Actions -->
                                                        <div class="absolute inset-0 bg-slate-900/90 rounded text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button onclick="document.getElementById('swapModal_{{ $depSchedule->id }}_dep').classList.remove('hidden')" class="text-xs font-bold px-3 py-1 bg-white/20 hover:bg-white/30 rounded mb-1">Gate Swap</button>
                                                        </div>
                                                    </div>

                                                    <!-- Swap Modal -->
                                                    <div id="swapModal_{{ $depSchedule->id }}_dep" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                                                        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden text-slate-800">
                                                            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                                                <h3 class="font-bold">Gate Swap (Departure) - {{ $depSchedule->flight->flight_number }}</h3>
                                                                <button onclick="document.getElementById('swapModal_{{ $depSchedule->id }}_dep').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </div>
                                                            <div class="p-6">
                                                                @php
                                                                    $suggestions = app(\App\Services\Operations\RecoveryEngineService::class)->suggestGateSwap($depSchedule, 'departure');
                                                                @endphp
                                                                <p class="text-sm text-slate-500 mb-4">Original Gate: T{{ $gate->terminal }}-{{ $gate->gate_number }}</p>
                                                                
                                                                @if($suggestions->isEmpty())
                                                                    <div class="p-3 bg-rose-50 text-rose-800 text-sm rounded border border-rose-200">No other available gates without conflict.</div>
                                                                @else
                                                                    <form action="{{ route('admin.operations.gates.swap', $depSchedule) }}" method="POST" class="space-y-4">
                                                                        @csrf
                                                                        <input type="hidden" name="type" value="departure">
                                                                        <select name="gate_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                                            <option value="">-- Select Recommended Gate --</option>
                                                                            @foreach($suggestions as $suggestion)
                                                                                <option value="{{ $suggestion->gate->id }}">T{{ $suggestion->gate->terminal }}-{{ $suggestion->gate->gate_number }} ({{ implode(', ', $suggestion->reasons) }})</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="flex justify-end gap-3">
                                                                            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition">Confirm Gate Swap</button>
                                                                        </div>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @foreach($gate->arrivalSchedules as $arrSchedule)
                                                    <div class="bg-emerald-50 border border-emerald-200 rounded p-2 text-xs flex flex-col gap-1 w-48 shrink-0 relative group">
                                                        <div class="font-bold text-emerald-800 flex justify-between">
                                                            <span>ARR: {{ $arrSchedule->flight->flight_number }}</span>
                                                            <span class="text-emerald-500">{{ $arrSchedule->arrival_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="text-slate-500">From: {{ $arrSchedule->flight->route->origin->iata_code }}</div>
                                                        <div class="text-emerald-600 font-mono text-[10px]">Occupy: {{ $arrSchedule->arrival_datetime->format('H:i') }} - {{ $arrSchedule->arrival_datetime->copy()->addMinutes(45)->format('H:i') }}</div>
                                                        
                                                        <!-- Hover Actions -->
                                                        <div class="absolute inset-0 bg-slate-900/90 rounded text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button onclick="document.getElementById('swapModal_{{ $arrSchedule->id }}_arr').classList.remove('hidden')" class="text-xs font-bold px-3 py-1 bg-white/20 hover:bg-white/30 rounded mb-1">Gate Swap</button>
                                                        </div>
                                                    </div>

                                                    <!-- Swap Modal -->
                                                    <div id="swapModal_{{ $arrSchedule->id }}_arr" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                                                        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden text-slate-800">
                                                            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                                                <h3 class="font-bold">Gate Swap (Arrival) - {{ $arrSchedule->flight->flight_number }}</h3>
                                                                <button onclick="document.getElementById('swapModal_{{ $arrSchedule->id }}_arr').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                </button>
                                                            </div>
                                                            <div class="p-6">
                                                                @php
                                                                    $suggestions = app(\App\Services\Operations\RecoveryEngineService::class)->suggestGateSwap($arrSchedule, 'arrival');
                                                                @endphp
                                                                <p class="text-sm text-slate-500 mb-4">Original Gate: T{{ $gate->terminal }}-{{ $gate->gate_number }}</p>
                                                                
                                                                @if($suggestions->isEmpty())
                                                                    <div class="p-3 bg-rose-50 text-rose-800 text-sm rounded border border-rose-200">No other available gates without conflict.</div>
                                                                @else
                                                                    <form action="{{ route('admin.operations.gates.swap', $arrSchedule) }}" method="POST" class="space-y-4">
                                                                        @csrf
                                                                        <input type="hidden" name="type" value="arrival">
                                                                        <select name="gate_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                                            <option value="">-- Select Recommended Gate --</option>
                                                                            @foreach($suggestions as $suggestion)
                                                                                <option value="{{ $suggestion->gate->id }}">T{{ $suggestion->gate->terminal }}-{{ $suggestion->gate->gate_number }} ({{ implode(', ', $suggestion->reasons) }})</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="flex justify-end gap-3">
                                                                            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition">Confirm Gate Swap</button>
                                                                        </div>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if($gate->departureSchedules->isEmpty() && $gate->arrivalSchedules->isEmpty())
                                                    <span class="text-xs text-slate-400 italic">No scheduled occupancy</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
