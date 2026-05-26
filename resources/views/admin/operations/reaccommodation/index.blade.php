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

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Disrupted Flights Requiring Action</h3>
                    <p class="text-sm text-slate-500">Flights cancelled or severely delayed that still have stranded passengers.</p>
                </div>
            </div>

            @if($disruptedSchedules->isEmpty())
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-12 flex flex-col items-center justify-center text-slate-500">
                    <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                    <p class="text-lg font-bold text-slate-600">No Pending Reaccommodations</p>
                    <p class="text-sm">All passengers have been successfully rebooked or no disruptions detected.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($disruptedSchedules as $schedule)
                        @if($schedule->affected_passengers_count > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-rose-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-rose-100 bg-rose-50 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-rose-800 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Flight {{ $schedule->flight->flight_number }} - {{ strtoupper($schedule->status) }}
                                    </h3>
                                    <p class="text-sm text-rose-600 font-medium mt-1">
                                        {{ $schedule->flight->route->origin->iata_code }} &rarr; {{ $schedule->flight->route->destination->iata_code }} | 
                                        Original Dep: {{ $schedule->departure_datetime->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="bg-rose-100 text-rose-800 px-4 py-2 rounded-lg font-bold text-lg text-center">
                                    {{ $schedule->affected_passengers_count }}
                                    <div class="text-[10px] uppercase tracking-wider">Pax Stranded</div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="mb-6">
                                    <button onclick="openIntelligenceDrawer('{{ $schedule->id }}')" class="bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 font-bold py-2 px-4 rounded-lg transition shadow-sm flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                        Get Smart Recovery Recommendation
                                    </button>
                                </div>

                                <h4 class="font-bold text-slate-700 mb-4 text-sm uppercase tracking-wider">Suggested Recovery Flights</h4>
                                
                                @if($schedule->rebooking_candidates->isEmpty())
                                    <div class="p-4 bg-slate-50 text-slate-500 rounded border border-slate-200 text-center">
                                        No viable replacement flights found within 48 hours for this route.
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach($schedule->rebooking_candidates as $candidate)
                                            <div class="border border-slate-200 rounded-lg p-4 flex justify-between items-center hover:border-indigo-300 transition-colors">
                                                <div class="flex items-center gap-4">
                                                    <div class="bg-indigo-50 text-indigo-700 px-3 py-2 rounded font-bold text-center">
                                                        Score
                                                        <div class="text-xl">{{ $candidate->score }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-lg">{{ $candidate->schedule->flight->flight_number }}</div>
                                                        <div class="text-sm text-slate-600">Departs: {{ $candidate->schedule->departure_datetime->format('d M Y H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-6">
                                                    <div class="text-right">
                                                        <div class="text-sm text-slate-500">Available Seats</div>
                                                        <div class="font-bold text-lg text-emerald-600">{{ $candidate->schedule->available_seats }}</div>
                                                    </div>
                                                    
                                                    <form action="{{ route('admin.operations.reaccommodation.rebook', $schedule) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="new_schedule_id" value="{{ $candidate->schedule->id }}">
                                                        <x-admin.button type="submit" variant="primary">
                                                            <span>Rebook {{ min($schedule->affected_passengers_count, $candidate->schedule->available_seats) }} Pax</span>
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                        </x-admin.button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Passengers List -->
                                <div class="mt-8">
                                    <h4 class="font-bold text-slate-700 mb-4 text-sm uppercase tracking-wider">Stranded Passengers</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($schedule->seats as $seat)
                                            @foreach($seat->bookingSegmentPassengers as $bsp)
                                                <div class="border border-slate-200 rounded-lg p-3 flex justify-between items-center bg-white hover:bg-slate-50 transition cursor-pointer" onclick="openPassengerDrawer('{{ $bsp->id }}')">
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-sm">{{ $bsp->passenger->first_name }} {{ $bsp->passenger->last_name }}</div>
                                                        <div class="text-xs text-slate-500 font-mono">{{ $bsp->segment->booking->pnr }} &bull; Seat {{ $seat->seat->row }}{{ $seat->seat->letter }}</div>
                                                    </div>
                                                    <div class="text-slate-400">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <!-- Right Side Drawer for Passenger Timeline -->
    <div id="passengerDrawer" class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out border-l border-slate-200 flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Passenger Details</h3>
            <button onclick="closePassengerDrawer()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div id="passengerTimelineContent" class="p-6 overflow-y-auto flex-1">
            <!-- Content loaded via JS -->
        </div>
    </div>

    <!-- Right Side Drawer for Smart Recovery Intelligence -->
    <div id="intelligenceDrawer" class="fixed inset-y-0 right-0 w-full md:w-[500px] bg-slate-50 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out border-l border-slate-200 flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-white">
            <h3 class="font-bold text-indigo-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                Smart Recovery Recommendation
            </h3>
            <button onclick="closeIntelligenceDrawer()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div id="intelligenceContent" class="p-6 overflow-y-auto flex-1">
            <!-- Content loaded via JS -->
        </div>
    </div>

    <!-- Drawer Overlay -->
    <div id="drawerOverlay" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 hidden" onclick="closeAllDrawers()"></div>

    <script>
        function closeAllDrawers() {
            closePassengerDrawer();
            closeIntelligenceDrawer();
        }

        function openPassengerDrawer(passengerId) {
            document.getElementById('passengerDrawer').classList.remove('translate-x-full');
            document.getElementById('drawerOverlay').classList.remove('hidden');
            
            const contentDiv = document.getElementById('passengerTimelineContent');
            contentDiv.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400"><svg class="animate-spin w-8 h-8" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

            fetch(`/admin/operations/passengers/${passengerId}/timeline`)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(error => {
                    contentDiv.innerHTML = '<div class="p-4 bg-rose-50 text-rose-600 rounded">Failed to load timeline.</div>';
                });
        }

        function closePassengerDrawer() {
            document.getElementById('passengerDrawer').classList.add('translate-x-full');
            document.getElementById('drawerOverlay').classList.add('hidden');
        }

        function openIntelligenceDrawer(scheduleId) {
            document.getElementById('intelligenceDrawer').classList.remove('translate-x-full');
            document.getElementById('drawerOverlay').classList.remove('hidden');
            
            const contentDiv = document.getElementById('intelligenceContent');
            contentDiv.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-indigo-400"><svg class="animate-spin w-12 h-12 mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="font-bold text-sm">Evaluating strategies...</span></div>';

            fetch(`/admin/operations/reaccommodation/${scheduleId}/intelligence`)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(error => {
                    contentDiv.innerHTML = '<div class="p-4 bg-rose-50 text-rose-600 rounded">Failed to load AI recommendations.</div>';
                });
        }

        function closeIntelligenceDrawer() {
            document.getElementById('intelligenceDrawer').classList.add('translate-x-full');
            if(document.getElementById('passengerDrawer').classList.contains('translate-x-full')) {
                document.getElementById('drawerOverlay').classList.add('hidden');
            }
        }
    </script>
</x-admin-layout>
