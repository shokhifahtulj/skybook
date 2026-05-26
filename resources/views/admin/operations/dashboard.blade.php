<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.operations.index') }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Command Center: {{ $schedule->flight->flight_number }}</h2>
            </div>
            <p class="text-sm text-slate-500 ml-9">{{ $schedule->flight->route->origin->iata_code }} &rarr; {{ $schedule->flight->route->destination->iata_code }} | {{ $schedule->departure_datetime->format('d M Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="relative flex h-3 w-3 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <span class="text-sm font-semibold text-slate-600">LIVE SYNC</span>
        </div>
    </div>

    <!-- Live Flight Board -->
    <div class="mb-4">
        @if($schedule->status !== 'cancelled' && $schedule->status !== 'departed' && $schedule->status !== 'arrived')
            <!-- IROPS Dropdown/Buttons -->
            <div x-data="{ iropsMenu: false }" class="relative inline-block">
                <button @click="iropsMenu = !iropsMenu" class="flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-slate-900 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    IROPS Actions
                </button>
                
                <div x-show="iropsMenu" @click.away="iropsMenu = false" class="absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden" style="display: none;">
                    <div class="p-3 bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">Disruption Management</div>
                    
                    <!-- Delay Action -->
                    <div x-data="{ showDelay: false }">
                        <button @click="showDelay = !showDelay" class="w-full text-left px-4 py-3 text-sm font-bold text-amber-700 hover:bg-amber-50 border-b border-slate-100 flex justify-between items-center">
                            Declare Delay
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        <div x-show="showDelay" class="p-4 bg-amber-50/50 border-b border-slate-100">
                            <form action="{{ route('admin.operations.irops.delay', $schedule->id) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Delay Minutes</label>
                                        <input type="number" name="delay_minutes" min="5" max="1440" value="30" class="w-full rounded border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason (Optional)</label>
                                        <input type="text" name="reason" class="w-full rounded border-slate-300 text-sm" placeholder="e.g. Late inbound aircraft">
                                    </div>
                                    <button type="submit" class="w-full bg-amber-600 text-white py-2 rounded text-sm font-bold hover:bg-amber-700">Apply Delay</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Gate Change Action -->
                    <div x-data="{ showGate: false }">
                        <button @click="showGate = !showGate" class="w-full text-left px-4 py-3 text-sm font-bold text-indigo-700 hover:bg-indigo-50 border-b border-slate-100 flex justify-between items-center">
                            Change Gate
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </button>
                        <div x-show="showGate" class="p-4 bg-indigo-50/50 border-b border-slate-100">
                            <form action="{{ route('admin.operations.irops.gate', $schedule->id) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">New Gate</label>
                                        <input type="text" name="new_gate" class="w-full rounded border-slate-300 text-sm uppercase" required placeholder="e.g. B7">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Reason (Optional)</label>
                                        <input type="text" name="reason" class="w-full rounded border-slate-300 text-sm" placeholder="e.g. Aircraft swap">
                                    </div>
                                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded text-sm font-bold hover:bg-indigo-700">Apply Change</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Cancel Action -->
                    <div x-data="{ showCancel: false }">
                        <button @click="showCancel = !showCancel" class="w-full text-left px-4 py-3 text-sm font-bold text-rose-700 hover:bg-rose-50 flex justify-between items-center">
                            Cancel Flight
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        <div x-show="showCancel" class="p-4 bg-rose-50/50 border-t border-rose-100">
                            <form action="{{ route('admin.operations.irops.cancel', $schedule->id) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Cancellation Reason</label>
                                        <input type="text" name="reason" class="w-full rounded border-slate-300 text-sm" required placeholder="e.g. Severe weather">
                                    </div>
                                    <button type="submit" onclick="return confirm('WARNING: This will cancel the flight, revoke all boarding passes, and halt operations. Proceed?')" class="w-full bg-rose-600 text-white py-2 rounded text-sm font-bold hover:bg-rose-700">CONFIRM CANCELLATION</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-indigo-500">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Status</div>
            <div class="text-xl font-bold text-indigo-700" id="stat-status">{{ strtoupper(str_replace('_', ' ', $flight_status)) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-blue-500">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Checked-In</div>
            <div class="text-2xl font-bold text-slate-800" id="stat-checkedin">{{ $metrics['checked_in_count'] }} <span class="text-sm font-normal text-slate-500">/ {{ $metrics['total_capacity'] }}</span></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-emerald-500">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Boarded</div>
            <div class="text-2xl font-bold text-slate-800" id="stat-boarded">{{ $metrics['boarded_count'] }} <span class="text-sm font-normal text-slate-500">/ {{ $metrics['checked_in_count'] }}</span></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-amber-500">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Remaining / No-Show</div>
            <div class="text-2xl font-bold text-slate-800"><span id="stat-remaining">{{ $metrics['remaining_to_board'] }}</span> <span class="text-sm font-normal text-slate-500">/ <span id="stat-noshow">{{ $metrics['no_show_count'] }}</span></span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Funnel & Metrics -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Boarding Progress</h3>
                
                @php
                    $boardedPercent = $metrics['checked_in_count'] > 0 ? round(($metrics['boarded_count'] / $metrics['checked_in_count']) * 100) : 0;
                @endphp
                
                <div class="mb-2 flex justify-between text-sm">
                    <span class="font-semibold text-slate-600">Completion</span>
                    <span class="font-bold text-emerald-600" id="progress-text">{{ $boardedPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 mb-6">
                    <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" id="progress-bar" style="width: {{ $boardedPercent }}%"></div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Aircraft Capacity</span>
                        <span class="font-bold text-slate-700" id="funnel-capacity">{{ $metrics['total_capacity'] }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Booked Seats</span>
                        <span class="font-bold text-slate-700" id="funnel-booked">{{ $metrics['booked_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-slate-100 pt-3">
                        <span class="text-slate-500">Checked-In</span>
                        <span class="font-bold text-blue-600" id="funnel-checkedin">{{ $metrics['checked_in_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Boarded</span>
                        <span class="font-bold text-emerald-600" id="funnel-boarded">{{ $metrics['boarded_count'] }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Ancillary Commerce -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 text-indigo-600">Ancillary Commerce</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Checked Bags</span>
                        <span class="font-bold text-slate-700" id="stat-bags">{{ $metrics['checked_bags_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Priority Boarding</span>
                        <span class="font-bold text-slate-700" id="stat-priority">{{ $metrics['priority_boarding_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-slate-100 pt-3">
                        <span class="text-slate-500">Total Revenue</span>
                        <span class="font-bold text-indigo-600" id="stat-revenue">Rp {{ number_format($metrics['ancillary_revenue'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Fraud Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 text-red-600">Security & Fraud Alerts</h3>
                <div id="alerts-container" class="space-y-3">
                    @forelse($alerts as $alert)
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800">{{ str_replace('_', ' ', strtoupper($alert->event_type)) }}</h3>
                                    <div class="mt-1 text-xs text-red-700">{{ $alert->description }}</div>
                                    <div class="mt-1 text-xs text-red-500 font-mono">{{ $alert->created_at->format('H:i:s') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500 text-center py-4 italic">No security alerts detected.</div>
                    @endforelse
                </div>
            </div>

            <!-- Fleet & Crew Status -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mt-6">
                <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Fleet & Crew Status
                    </h3>
                    <a href="{{ route('admin.operations.assignment', $schedule->id) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded">Manage</a>
                </div>
                
                <div class="space-y-4">
                    <!-- Aircraft Status -->
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Aircraft Assignment</div>
                        @if($schedule->assignedAircraft)
                            <div class="flex items-center gap-2">
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded">{{ $schedule->assignedAircraft->model }}</span>
                                <span class="text-xs font-mono text-slate-500">Cap: {{ $schedule->assignedAircraft->capacity }}</span>
                                @if($schedule->assignedAircraft->operational_status === 'assigned')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 ml-auto">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Ready
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 ml-auto">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg> {{ strtoupper(str_replace('_', ' ', $schedule->assignedAircraft->operational_status)) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <!-- Warnings if Aircraft Grounded Conflict -->
                            @php
                                $groundedConflict = \App\Models\OperationalLog::where('flight_schedule_id', $schedule->id)
                                    ->where('event_type', 'aircraft_grounded_conflict')
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                            @endphp

                            @if(!$schedule->assignedAircraft && $groundedConflict)
                                <div class="mt-2 text-xs text-rose-800 flex items-center gap-1 font-medium bg-rose-100 border border-rose-200 p-2 rounded">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <span>CRITICAL: Aircraft grounded. Equipment swap required immediately.</span>
                                </div>
                            @elseif(!$schedule->assignedAircraft)
                                <div class="mt-2 text-xs text-amber-600 flex items-center gap-1 font-medium">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Awaiting Aircraft Assignment
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Crew Status -->
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Crew Manifest</div>
                        @php
                            $crewList = $schedule->crewAssignments()->where('status', 'assigned')->with(['crewMember', 'role'])->get();
                            $hasPic = $crewList->contains(fn($c) => $c->role->code === 'PIC');
                            $hasCabin = $crewList->contains(fn($c) => $c->role->type === 'cabin');
                        @endphp
                        
                        @if($crewList->isEmpty())
                            <div class="text-sm text-amber-700 bg-amber-50 p-2 rounded border border-amber-100 flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Crew pending assignment
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($crewList as $assignment)
                                    <div class="flex justify-between items-center text-sm border border-slate-100 p-2 rounded">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800">{{ $assignment->crewMember->first_name }} {{ $assignment->crewMember->last_name }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $assignment->crewMember->crew_code }}</span>
                                        </div>
                                        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2 py-1 rounded">{{ $assignment->role->code }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Warnings if essential crew missing -->
                        @if($schedule->assignedAircraft && (!$hasPic || !$hasCabin))
                            <div class="mt-2 text-xs text-rose-600 flex items-center gap-1 font-medium bg-rose-50 p-2 rounded">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Incomplete Crew (Missing PIC or Cabin)
                            </div>
                        @endif

                        <!-- Crew Legality Violations (Duty Bust) -->
                        @php
                            // Check operational logs for any crew legality bust for this schedule
                            $legalityViolations = \App\Models\OperationalLog::where('flight_schedule_id', $schedule->id)
                                ->where('event_type', 'crew_legality_violation')
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->unique('payload.crew_member_id'); // Only get latest per crew
                        @endphp

                        @if($legalityViolations->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach($legalityViolations as $violation)
                                    <div class="text-xs text-rose-800 bg-rose-100 p-2 rounded border border-rose-200">
                                        <div class="font-bold flex items-center gap-1 mb-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            CRITICAL: Legality Bust
                                        </div>
                                        <div class="opacity-90">{{ $violation->description }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Aircraft Rotation Tracker -->
            @if($schedule->assignedAircraft)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mt-6">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Aircraft Rotation Chain
                        </h3>
                    </div>
                    
                    @php
                        // Get the full chain for today
                        $rotationChain = \App\Models\FlightSchedule::where('aircraft_id', $schedule->aircraft_id)
                            ->whereDate('departure_datetime', $schedule->departure_datetime->toDateString())
                            ->orderBy('departure_datetime', 'asc')
                            ->with('flight.route')
                            ->get();
                    @endphp

                    <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                        @foreach($rotationChain as $index => $leg)
                            @php
                                $isCurrent = $leg->id === $schedule->id;
                                $isPast = $leg->departure_datetime->isPast() && !$isCurrent;
                                
                                $nodeColor = $isCurrent ? 'bg-indigo-500 ring-indigo-200' : ($isPast ? 'bg-slate-300 ring-slate-100' : 'bg-emerald-500 ring-emerald-200');
                                if ($leg->delay_minutes > 0) $nodeColor = 'bg-rose-500 ring-rose-200';
                            @endphp

                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white {{ $nodeColor }} shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded-lg shadow border border-slate-100 {{ $isCurrent ? 'ring-2 ring-indigo-500' : '' }}">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-bold text-slate-800">{{ $leg->flight->flight_number }}</div>
                                        <time class="font-mono text-xs font-medium {{ $leg->delay_minutes > 0 ? 'text-rose-500' : 'text-emerald-500' }}">{{ $leg->departure_datetime->format('H:i') }}</time>
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        {{ $leg->flight->route->origin->iata_code }} &rarr; {{ $leg->flight->route->destination->iata_code }}
                                    </div>
                                    @if($leg->delay_minutes > 0)
                                        <div class="mt-2 text-xs bg-rose-50 text-rose-700 p-2 rounded border border-rose-100">
                                            <strong>Delayed +{{ $leg->delay_minutes }}m</strong>
                                            @if($leg->delay_reason) <br> <span class="opacity-75">Reason: {{ $leg->delay_reason }}</span> @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Operational Feed -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 h-full flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Operational Gate Feed</h3>
                    <span class="text-xs font-mono text-slate-400">Auto-refresh: 15s</span>
                </div>
                <div class="p-5 overflow-y-auto max-h-[600px]" id="feed-container">
                    <ul class="relative border-l border-slate-200 ml-3 space-y-6">
                        @forelse($feed as $log)
                            @php
                                $color = 'bg-slate-400';
                                if($log->level === 'danger') $color = 'bg-red-500';
                                if($log->level === 'warning') $color = 'bg-amber-500';
                                if($log->event_type === 'boarding_approved') $color = 'bg-emerald-500';
                                if($log->event_type === 'checked_in') $color = 'bg-blue-500';
                                if($log->event_type === 'seat_changed') $color = 'bg-purple-500';
                            @endphp
                            <li class="mb-2 ml-6">
                                <span class="absolute flex items-center justify-center w-4 h-4 rounded-full -left-2 ring-4 ring-white {{ $color }}"></span>
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="flex items-center text-sm font-bold text-slate-800">{{ str_replace('_', ' ', strtoupper($log->event_type)) }}</h3>
                                    <time class="mb-1 text-xs font-mono font-medium text-slate-400">{{ $log->created_at->format('H:i:s') }}</time>
                                </div>
                                <p class="mb-1 text-sm text-slate-600">{{ $log->description }}</p>
                                @if($log->event_payload)
                                    <div class="text-xs font-mono text-slate-500 bg-slate-50 p-2 rounded border border-slate-100 inline-block mt-1">
                                        @foreach($log->event_payload as $key => $val)
                                            <span class="mr-3"><span class="font-semibold">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="ml-6 text-sm text-slate-500 italic">No operational activities recorded yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Polling Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleId = {{ $schedule->id }};
            const pollInterval = 15000; // 15 seconds

            function fetchDashboardData() {
                fetch(`/api/operations/${scheduleId}/poll`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            updateMetrics(data.data.metrics, data.data.flight_status);
                            updateFeed(data.data.feed);
                            updateAlerts(data.data.alerts);
                        }
                    })
                    .catch(error => console.error('Error polling dashboard data:', error));
            }

            function updateMetrics(metrics, status) {
                document.getElementById('stat-status').innerText = status.replace('_', ' ').toUpperCase();
                document.getElementById('stat-checkedin').innerHTML = `${metrics.checked_in_count} <span class="text-sm font-normal text-slate-500">/ ${metrics.total_capacity}</span>`;
                document.getElementById('stat-boarded').innerHTML = `${metrics.boarded_count} <span class="text-sm font-normal text-slate-500">/ ${metrics.checked_in_count}</span>`;
                document.getElementById('stat-remaining').innerText = metrics.remaining_to_board;
                document.getElementById('stat-noshow').innerText = metrics.no_show_count;
                
                let percent = metrics.checked_in_count > 0 ? Math.round((metrics.boarded_count / metrics.checked_in_count) * 100) : 0;
                document.getElementById('progress-text').innerText = `${percent}%`;
                document.getElementById('progress-bar').style.width = `${percent}%`;
                
                document.getElementById('funnel-capacity').innerText = metrics.total_capacity;
                document.getElementById('funnel-booked').innerText = metrics.booked_count;
                document.getElementById('funnel-checkedin').innerText = metrics.checked_in_count;
                document.getElementById('funnel-boarded').innerText = metrics.boarded_count;
                
                if (metrics.checked_bags_count !== undefined) {
                    document.getElementById('stat-bags').innerText = metrics.checked_bags_count;
                    document.getElementById('stat-priority').innerText = metrics.priority_boarding_count;
                    document.getElementById('stat-revenue').innerText = 'Rp ' + parseInt(metrics.ancillary_revenue).toLocaleString('id-ID');
                }
            }

            function updateFeed(feed) {
                const container = document.querySelector('#feed-container ul');
                if(!feed || feed.length === 0) return;
                
                let html = '';
                feed.forEach(log => {
                    let color = 'bg-slate-400';
                    if(log.level === 'danger') color = 'bg-red-500';
                    if(log.level === 'warning') color = 'bg-amber-500';
                    if(log.event_type === 'boarding_approved') color = 'bg-emerald-500';
                    if(log.event_type === 'checked_in') color = 'bg-blue-500';
                    if(log.event_type === 'seat_changed') color = 'bg-purple-500';
                    
                    let time = new Date(log.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
                    
                    let payloadHtml = '';
                    if(log.event_payload) {
                        payloadHtml = `<div class="text-xs font-mono text-slate-500 bg-slate-50 p-2 rounded border border-slate-100 inline-block mt-1">`;
                        for(const [key, val] of Object.entries(log.event_payload)) {
                            let dispVal = typeof val === 'object' ? JSON.stringify(val) : val;
                            payloadHtml += `<span class="mr-3"><span class="font-semibold">${key}:</span> ${dispVal}</span>`;
                        }
                        payloadHtml += `</div>`;
                    }
                    
                    html += `
                    <li class="mb-2 ml-6">
                        <span class="absolute flex items-center justify-center w-4 h-4 rounded-full -left-2 ring-4 ring-white ${color}"></span>
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="flex items-center text-sm font-bold text-slate-800">${log.event_type.replace(/_/g, ' ').toUpperCase()}</h3>
                            <time class="mb-1 text-xs font-mono font-medium text-slate-400">${time}</time>
                        </div>
                        <p class="mb-1 text-sm text-slate-600">${log.description}</p>
                        ${payloadHtml}
                    </li>`;
                });
                container.innerHTML = html;
            }

            function updateAlerts(alerts) {
                const container = document.getElementById('alerts-container');
                if(!alerts || alerts.length === 0) {
                    container.innerHTML = '<div class="text-sm text-slate-500 text-center py-4 italic">No security alerts detected.</div>';
                    return;
                }
                
                let html = '';
                alerts.forEach(alert => {
                    let time = new Date(alert.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute:'2-digit', second:'2-digit'});
                    html += `
                    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">${alert.event_type.replace(/_/g, ' ').toUpperCase()}</h3>
                                <div class="mt-1 text-xs text-red-700">${alert.description}</div>
                                <div class="mt-1 text-xs text-red-500 font-mono">${time}</div>
                            </div>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
            }

            setInterval(fetchDashboardData, pollInterval);
        });
    </script>
</x-admin-layout>
