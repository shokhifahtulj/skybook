<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.operations.show', $schedule->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Fleet & Crew Assignment</h2>
            </div>
            <p class="text-sm text-slate-500 ml-9">Flight {{ $schedule->flight->flight_number }} | {{ $schedule->flight->route->origin->iata_code }} &rarr; {{ $schedule->flight->route->destination->iata_code }} | Departure: {{ $schedule->departure_datetime->format('d M Y H:i') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-sm">
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Aircraft Assignment Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Aircraft
                </h3>
            </div>
            
            <div class="p-5">
                @if($schedule->assignedAircraft)
                    <div class="mb-6 p-4 border border-emerald-200 bg-emerald-50 rounded-lg flex justify-between items-center">
                        <div>
                            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Currently Assigned</div>
                            <div class="text-lg font-bold text-slate-800">{{ $schedule->assignedAircraft->model }}</div>
                            <div class="text-sm text-slate-600">Capacity: {{ $schedule->assignedAircraft->capacity }} seats | Config: {{ $schedule->assignedAircraft->seat_layout }}</div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> {{ strtoupper($schedule->assignedAircraft->operational_status) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mb-6 p-4 border border-amber-200 bg-amber-50 rounded-lg text-amber-800 flex items-center gap-3">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <strong>No Aircraft Assigned. Flight cannot operate without an aircraft.</strong>
                    </div>
                @endif

                @if(isset($suggestedAircrafts) && $suggestedAircrafts->isNotEmpty())
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <h4 class="font-bold text-indigo-800 flex items-center gap-2 mb-3 text-sm uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Suggested Recovery Aircraft
                        </h4>
                        <div class="space-y-2">
                            @foreach($suggestedAircrafts as $suggestion)
                                <div class="bg-white border border-indigo-100 rounded p-3 flex justify-between items-center shadow-sm">
                                    <div>
                                        <div class="font-bold text-slate-800 flex items-center gap-2">
                                            {{ $suggestion->aircraft->model }}
                                            @if($loop->first)
                                                <span class="bg-emerald-100 text-emerald-700 text-[10px] px-2 py-0.5 rounded font-bold">BEST MATCH</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1 flex flex-col gap-0.5">
                                            @foreach($suggestion->reasons as $reason)
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ $reason }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.operations.assignment.aircraft', $schedule) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="aircraft_id" value="{{ $suggestion->aircraft->id }}">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs font-bold transition-colors shadow-sm">
                                            1-Click Assign
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.operations.assignment.aircraft', $schedule->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Assign New Aircraft</label>
                        <select name="aircraft_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="">-- Select Available Aircraft --</option>
                            @foreach($availableAircrafts as $aircraft)
                                <option value="{{ $aircraft->id }}">{{ $aircraft->model }} (Cap: {{ $aircraft->capacity }}) - {{ $aircraft->operational_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition w-full">
                        {{ $schedule->assignedAircraft ? 'Swap Aircraft' : 'Assign Aircraft' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Crew Assignment Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Crew Manifest
                </h3>
            </div>
            
            <div class="p-5">
                @php
                    $crewList = $schedule->crewAssignments()->where('status', 'assigned')->with(['crewMember', 'role'])->get();
                    $hasPic = $crewList->contains(fn($c) => $c->role->code === 'PIC');
                    $hasCabin = $crewList->contains(fn($c) => $c->role->type === 'cabin');
                @endphp
                
                @if(!$hasPic || !$hasCabin)
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <strong>Incomplete Manifest</strong><br>
                            Minimum requirement: 1 Pilot in Command (PIC) and Cabin Crew members.
                        </div>
                    </div>
                @endif

                <!-- Assigned Crew List -->
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3 border-b border-slate-100 pb-2">Currently Assigned</h4>
                    @if($crewList->isEmpty())
                        <p class="text-sm text-slate-500 italic">No crew assigned yet.</p>
                    @else
                        <ul class="space-y-3">
                            @foreach($crewList as $assignment)
                                <li class="flex justify-between items-center p-3 border border-slate-200 rounded-lg hover:border-indigo-300 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-indigo-100 text-indigo-800 font-bold px-2 py-1 rounded text-xs w-12 text-center">{{ $assignment->role->code }}</div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm">{{ $assignment->crewMember->first_name }} {{ $assignment->crewMember->last_name }}</div>
                                            <div class="text-xs text-slate-500 font-mono">{{ $assignment->crewMember->crew_code }}</div>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('admin.operations.assignment.crew.remove', [$schedule->id, $assignment->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 bg-rose-50 p-2 rounded-lg transition" title="Remove Crew" onclick="return confirm('Remove this crew member from the manifest?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                @if(isset($suggestedCrews) && $suggestedCrews->isNotEmpty())
                    <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <h4 class="font-bold text-indigo-800 flex items-center gap-2 mb-3 text-sm uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Suggested Reserve Crew
                        </h4>
                        <div class="space-y-2">
                            @foreach($suggestedCrews->take(3) as $suggestion)
                                <div class="bg-white border border-indigo-100 rounded p-3 flex justify-between items-center shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-indigo-100 text-indigo-800 font-bold px-2 py-1 rounded text-xs w-12 text-center">{{ $suggestion->crew->role->code }}</div>
                                        <div>
                                            <div class="font-bold text-slate-800 flex items-center gap-2">
                                                {{ $suggestion->crew->first_name }} {{ $suggestion->crew->last_name }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1 flex flex-col gap-0.5">
                                                @foreach($suggestion->reasons as $reason)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        {{ $reason }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.operations.assignment.crew', $schedule) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="crew_member_id" value="{{ $suggestion->crew->id }}">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-xs font-bold transition-colors shadow-sm">
                                            1-Click Assign
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add Crew Form -->
                <form action="{{ route('admin.operations.assignment.crew', $schedule->id) }}" method="POST" class="border-t border-slate-100 pt-5 mt-5">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Add Crew Member</label>
                        <select name="crew_member_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="">-- Select Available Crew --</option>
                            @foreach($availableCrews as $crew)
                                <option value="{{ $crew->id }}">[{{ $crew->role->code }}] {{ $crew->first_name }} {{ $crew->last_name }} ({{ $crew->crew_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition w-full">
                        Add to Manifest
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
