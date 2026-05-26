<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Flight Timeline
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-100 p-6 sm:p-8 mb-8">
                <!-- Flight Header -->
                <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-1">
                            {{ $segment->schedule->flight->route->origin->iata_code }} ✈ {{ $segment->schedule->flight->route->destination->iata_code }}
                        </h3>
                        <p class="text-sm text-slate-500 font-medium">Flight {{ $segment->schedule->flight->flight_number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-bold tracking-wide
                            {{ $segment->schedule->status === 'DELAYED' ? 'bg-rose-100 text-rose-700' : 
                               ($segment->schedule->status === 'ON_TIME' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700') }}">
                            {{ str_replace('_', ' ', $segment->schedule->status) }}
                        </span>
                    </div>
                </div>

                <!-- Timeline Graphic -->
                <div class="relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-slate-200"></div>

                    <div class="space-y-8 relative">
                        
                        <!-- Flight Created -->
                        <div class="flex gap-6 relative">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border-4 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-400"></div>
                            </div>
                            <div class="pt-1 flex-1">
                                <p class="font-bold text-slate-800">Booking Confirmed</p>
                                <p class="text-sm text-slate-500 mt-1">Your reservation for this flight has been successfully processed.</p>
                                <p class="text-xs text-slate-400 mt-2 font-medium">{{ $segment->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <!-- Operational Notifications -->
                        @foreach($notifications->sortBy('created_at') as $notif)
                            @php
                                $iconColor = 'bg-sky-500';
                                $bgColor = 'bg-sky-100';
                                
                                if ($notif->event_type === 'GateChanged') {
                                    $iconColor = 'bg-amber-500';
                                    $bgColor = 'bg-amber-100';
                                } elseif ($notif->event_type === 'FlightDisrupted') {
                                    $iconColor = 'bg-rose-500';
                                    $bgColor = 'bg-rose-100';
                                } elseif ($notif->event_type === 'PassengerRebooked') {
                                    $iconColor = 'bg-indigo-500';
                                    $bgColor = 'bg-indigo-100';
                                }
                            @endphp
                            <div class="flex gap-6 relative">
                                <div class="w-8 h-8 rounded-full {{ $bgColor }} border-4 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $iconColor }}"></div>
                                </div>
                                <div class="pt-1 flex-1">
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                                        <p class="font-bold text-slate-800 mb-2">
                                            @if($notif->event_type === 'GateChanged') Gate Update
                                            @elseif($notif->event_type === 'FlightDisrupted') Flight Update
                                            @elseif($notif->event_type === 'PassengerRebooked') Rebooking Update
                                            @else Operational Notification
                                            @endif
                                        </p>
                                        <p class="text-sm text-slate-600">{{ $notif->message }}</p>
                                        
                                        <div class="mt-3 flex items-center justify-between">
                                            <p class="text-xs text-slate-400 font-medium">{{ $notif->created_at->format('H:i') }}</p>
                                            
                                            @if($notif->acknowledged_at)
                                                <span class="text-xs text-emerald-500 font-bold flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Acknowledged
                                                </span>
                                            @else
                                                <button onclick="acknowledgeNotification('{{ $notif->id }}')" class="text-xs bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 py-1 px-3 rounded-md transition font-medium">
                                                    Acknowledge
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Current State -->
                        <div class="flex gap-6 relative">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 border-4 border-white shadow-sm flex items-center justify-center shrink-0 z-10">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping absolute"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 relative"></div>
                            </div>
                            <div class="pt-1 flex-1">
                                <p class="font-bold text-emerald-700">Live Status</p>
                                <p class="text-sm text-slate-500 mt-1">We are actively monitoring your flight. Please ensure you are at the gate 40 minutes before departure.</p>
                            </div>
                        </div>

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
