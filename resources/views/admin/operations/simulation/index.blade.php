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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Controls -->
                <div class="space-y-6">
                    
                    <!-- Sandbox Control -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Chaos Injector
                        </h3>

                        @if($activeSession)
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                <div class="text-amber-800 font-bold mb-1">Active Simulation Running</div>
                                <div class="text-sm text-amber-700 font-mono">{{ $activeSession->name }}</div>
                                <div class="text-xs text-amber-600 mt-2">Started: {{ $activeSession->started_at->format('H:i:s') }}</div>
                            </div>

                            <form action="{{ route('admin.operations.simulation.restore', $activeSession) }}" method="POST">
                                @csrf
                                <x-admin.button type="submit" variant="secondary" class="w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    Restore Baseline
                                </x-admin.button>
                                <p class="text-xs text-slate-500 mt-2 text-center">Restores flight schedules to normal. Audit logs are kept.</p>
                            </form>
                        @else
                            <form action="{{ route('admin.operations.simulation.start') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Scenario Seed</label>
                                    <select name="scenario_seed" class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="CGK_STORM_3H">CGK Severe Thunderstorm (3 Hours)</option>
                                        <option value="DPS_CONGESTION" disabled>DPS Runway Congestion (Coming Soon)</option>
                                    </select>
                                </div>
                                <x-admin.button type="submit" variant="danger" class="w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Inject Chaos
                                </x-admin.button>
                                <p class="text-xs text-slate-500 mt-2 text-center">This will instantly create a baseline snapshot and massively delay flights.</p>
                            </form>
                        @endif
                    </div>

                    <!-- Session History -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-bold text-slate-800 text-lg mb-4">Past Simulations</h3>
                        <div class="space-y-3">
                            @forelse($sessions->where('status', 'RESTORED')->take(5) as $sess)
                                <div class="border border-slate-100 rounded-lg p-3 bg-slate-50 flex justify-between items-center">
                                    <div>
                                        <div class="font-bold text-slate-700 text-xs">{{ $sess->scenario_seed }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $sess->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <a href="{{ route('admin.operations.simulation.replay', $sess) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded text-xs font-bold">Replay</a>
                                </div>
                            @empty
                                <div class="text-sm text-slate-500 text-center py-4">No past simulations found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Heatmap & KPI -->
                <div class="lg:col-span-2 space-y-6">
                    
                    @if($activeSession)
                    <!-- Live KPI Dashboard -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl shadow-sm border border-rose-200 p-4 border-b-4 border-b-rose-500">
                            <div class="text-xs text-rose-500 font-bold uppercase tracking-wider mb-1">Delayed Flights</div>
                            <div class="text-3xl font-black text-rose-700">{{ $activeSession->kpi_snapshot['total_delayed_flights'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-4 border-b-4 border-b-amber-500">
                            <div class="text-xs text-amber-500 font-bold uppercase tracking-wider mb-1">Gate Conflicts</div>
                            <div class="text-3xl font-black text-amber-700">{{ $activeSession->kpi_snapshot['gate_conflicts'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 border-b-4 border-b-slate-800">
                            <div class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">Stranded Pax</div>
                            <div class="text-3xl font-black text-slate-700">{{ $activeSession->kpi_snapshot['stranded_passengers'] ?? 0 }}</div>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-indigo-200 p-4 border-b-4 border-b-indigo-500">
                            <div class="text-xs text-indigo-500 font-bold uppercase tracking-wider mb-1">Notifs Sent</div>
                            <div class="text-3xl font-black text-indigo-700">{{ $activeSession->kpi_snapshot['notifications_sent'] ?? 0 }}</div>
                        </div>
                    </div>
                    @endif

                    <!-- Operational Heatmap -->
                    <div class="bg-slate-900 rounded-xl shadow-lg border border-slate-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center">
                            <h3 class="font-bold text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $activeSession ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                Network Heatmap
                            </h3>
                            <div class="flex gap-4 text-xs font-medium">
                                <div class="flex items-center gap-1 text-slate-300"><span class="w-3 h-3 rounded bg-emerald-500"></span> On Time</div>
                                <div class="flex items-center gap-1 text-slate-300"><span class="w-3 h-3 rounded bg-amber-500"></span> Delayed</div>
                                <div class="flex items-center gap-1 text-slate-300"><span class="w-3 h-3 rounded bg-rose-600"></span> Conflict/Cancelled</div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach($heatmapData as $flight)
                                    @php
                                        $color = 'bg-emerald-500';
                                        if($flight->status === 'delayed') $color = 'bg-amber-500';
                                        if($flight->status === 'cancelled') $color = 'bg-rose-600';
                                    @endphp
                                    <div class="rounded p-3 {{ $color }} bg-opacity-20 border {{ str_replace('bg-', 'border-', $color) }} border-opacity-30 relative overflow-hidden group">
                                        <div class="absolute inset-0 {{ $color }} opacity-10"></div>
                                        <div class="relative z-10">
                                            <div class="text-white font-bold text-sm">{{ $flight->flight->flight_number }}</div>
                                            <div class="text-xs {{ str_replace('bg-', 'text-', $color) }} font-medium uppercase tracking-widest mt-1">{{ $flight->status }}</div>
                                            <div class="text-[10px] text-slate-400 mt-2 font-mono">{{ $flight->flight->route->origin->iata_code }} &rarr; {{ $flight->flight->route->destination->iata_code }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($activeSession)
                        <div class="text-right">
                            <a href="{{ route('admin.operations.simulation.replay', $activeSession) }}" class="inline-flex items-center gap-2 text-indigo-600 font-bold hover:text-indigo-800">
                                View Live Event Replay
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
