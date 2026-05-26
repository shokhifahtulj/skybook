<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-[28px] font-bold text-slate-900">{{ __('My Travel') }}</h2>
            <p class="mt-1 text-sm text-slate-500">Ringkasan perjalanan, update operasional, dan booking aktif Anda.</p>
        </div>
    </x-slot>

    <div class="space-y-8">

            {{-- 1. Hero Section: Upcoming Flight --}}
            @if($upcomingFlights->isNotEmpty())
                @php
                    $nextFlightSegment = $upcomingFlights->first();
                    $schedule = $nextFlightSegment->schedule;
                    $flight = $schedule->flight;
                    $route = $flight->route;
                    $passengers = $nextFlightSegment->segmentPassengers;
                    
                    // Determine Status
                    $status = $schedule->status;
                    $statusColor = 'bg-emerald-500';
                    $statusText = 'ON TIME';
                    
                    if ($status === 'DELAYED') {
                        $statusColor = 'bg-rose-500';
                        $statusText = 'DELAYED';
                    } elseif ($status === 'BOARDING') {
                        $statusColor = 'bg-amber-500';
                        $statusText = 'BOARDING NOW';
                    } elseif ($status === 'GATE_OPEN') {
                        $statusColor = 'bg-indigo-500';
                        $statusText = 'GATE OPEN';
                    } elseif ($status === 'CANCELLED') {
                        $statusColor = 'bg-red-700';
                        $statusText = 'CANCELLED';
                    }
                @endphp
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-100">
                    <!-- Status Banner -->
                    <div class="{{ $statusColor }} px-6 py-3 flex justify-between items-center text-white">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              @if(in_array($status, ['BOARDING', 'GATE_OPEN', 'DELAYED']))
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                              @endif
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                            </span>
                            <span class="font-bold tracking-wider text-sm">{{ $statusText }}</span>
                        </div>
                        @if($status === 'DELAYED' && $schedule->actual_departure_time)
                            <div class="text-sm font-medium">New ETA: {{ \Carbon\Carbon::parse($schedule->actual_departure_time)->format('H:i') }}</div>
                        @endif
                    </div>
                    
                    <div class="p-6 sm:p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-3xl font-bold text-slate-900 mb-1">{{ $route->origin->iata_code }} <span class="text-slate-400 font-light mx-2">✈</span> {{ $route->destination->iata_code }}</h3>
                                <p class="text-slate-500">{{ $route->origin->city }} to {{ $route->destination->city }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-500 font-medium">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('D, d M Y') }}</p>
                                <p class="text-3xl font-bold text-slate-900">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}</p>
                            </div>
                        </div>

                        <!-- Flight Info Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-6 border-y border-slate-100 mb-6">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Flight</p>
                                <p class="font-bold text-slate-800">{{ $flight->flight_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Gate</p>
                                <p class="font-bold {{ $schedule->status === 'DELAYED' ? 'text-rose-600 animate-pulse' : 'text-slate-800' }} text-xl">
                                    {{ $schedule->gate ? $schedule->gate->gate_number : 'TBA' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Boarding Time</p>
                                <p class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($schedule->departure_time)->subMinutes(40)->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Aircraft</p>
                                <p class="font-bold text-slate-800">{{ $flight->aircraft->registration_number }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('passenger.boarding-pass', $nextFlightSegment->id) }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-bold py-3 px-6 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                View Boarding Pass
                            </a>
                            <a href="{{ route('passenger.timeline', $nextFlightSegment->id) }}" class="flex-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-center font-bold py-3 px-6 rounded-xl transition duration-200 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Flight Timeline
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Upcoming Flights -->
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 shadow-sm">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">No Upcoming Flights</h3>
                    <p class="text-slate-500 mb-6">You don't have any flights scheduled for the near future.</p>
                    <a href="{{ route('bookings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition">
                        Book a Flight
                    </a>
                </div>
            @endif

            {{-- 2. Smart Rebooking Alert (If Auto-Mitigated or Rebooked) --}}
            @php
                $hasRebooking = false;
                // We'll mock the check for rebooking here or check notifications
                $rebookingNotification = $notifications->where('event_type', 'PassengerRebooked')->first();
            @endphp
            @if($rebookingNotification)
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                    <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-sky-900 text-lg mb-1">Flight Protected Automatically</h4>
                        <p class="text-sky-700 text-sm mb-3">Due to operational disruptions, you have been automatically rebooked to a new flight. Our AI has selected the best available routing for you.</p>
                        <div class="bg-white/60 rounded-lg p-3 text-sm text-slate-800 border border-sky-100 font-medium">
                            {{ $rebookingNotification->message }}
                        </div>
                    </div>
                    <div class="shrink-0">
                        <button onclick="acknowledgeNotification('{{ $rebookingNotification->id }}')" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-6 rounded-lg transition shadow-sm w-full sm:w-auto">
                            Acknowledge
                        </button>
                    </div>
                </div>
            @endif

            {{-- 3. Notification Center & My Bookings --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Notification Center -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">Operational Updates</h3>
                        @if($notifications->count() > 0)
                            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full">{{ $notifications->count() }} New</span>
                        @endif
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        @if($notifications->isNotEmpty())
                            <div class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto no-scrollbar">
                                @foreach($notifications->take(5) as $notif)
                                    <div class="p-4 hover:bg-slate-50 transition">
                                        <div class="flex gap-3">
                                            <div class="mt-1">
                                                @if($notif->event_type === 'GateChanged')
                                                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                    </div>
                                                @elseif($notif->event_type === 'FlightDisrupted')
                                                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-800">{{ $notif->message }}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                                                    @if($notif->acknowledged_at)
                                                        <span class="text-xs text-emerald-500 font-medium">✓ Acknowledged</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <p class="text-slate-500 text-sm">No operational updates at this time.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- My Bookings List -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-800">My Bookings</h3>
                        <a href="{{ route('bookings.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">View All</a>
                    </div>

                    <div class="space-y-4">
                        @foreach($bookings->take(3) as $booking)
                            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-slate-100 px-3 py-1 rounded-lg text-sm font-bold text-slate-700 tracking-wider">
                                            PNR: {{ $booking->pnr }}
                                        </div>
                                        <span class="px-2 py-1 rounded-md text-xs font-bold 
                                            {{ $booking->booking_status === 'CONFIRMED' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $booking->booking_status }}
                                        </span>
                                    </div>
                                    <a href="{{ route('manage-booking.portal', $booking->pnr) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage</a>
                                </div>
                                
                                @foreach($booking->segments as $segment)
                                    <div class="flex items-center gap-4 py-2">
                                        <div class="text-center w-16">
                                            <p class="text-lg font-bold text-slate-800">{{ $segment->schedule->flight->route->origin->iata_code }}</p>
                                        </div>
                                        <div class="flex-1 border-t-2 border-dashed border-slate-200 relative">
                                            <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white px-2 text-slate-400">
                                                ✈
                                            </div>
                                        </div>
                                        <div class="text-center w-16">
                                            <p class="text-lg font-bold text-slate-800">{{ $segment->schedule->flight->route->destination->iata_code }}</p>
                                        </div>
                                        <div class="text-right w-32">
                                            <p class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($segment->schedule->departure_time)->format('d M') }}</p>
                                            <p class="text-xs text-slate-500">{{ $segment->schedule->flight->flight_number }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script for Acknowledge -->
    <script>
        function acknowledgeNotification(id) {
            fetch(`/api/notifications/${id}/acknowledge`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
              .then(data => {
                  window.location.reload();
              });
        }
    </script>
</x-app-layout>
