<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Digital Boarding Pass
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            
            @foreach($segment->segmentPassengers as $segmentPassenger)
                @php
                    $schedule = $segment->schedule;
                    $flight = $schedule->flight;
                    $route = $flight->route;
                    $passenger = $segmentPassenger->passenger;
                    $seat = $segmentPassenger->seat;
                    
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
                <!-- Boarding Pass Card -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-100 mb-8 relative">
                    <!-- Status Header -->
                    <div class="{{ $statusColor }} px-6 py-2 text-white text-center font-bold tracking-widest text-xs">
                        @if(in_array($status, ['BOARDING', 'GATE_OPEN']))
                            <span class="animate-pulse">{{ $statusText }}</span>
                        @else
                            {{ $statusText }}
                        @endif
                    </div>
                    
                    <!-- Top Section -->
                    <div class="p-6 bg-slate-900 text-white rounded-t-3xl mt-[-10px] relative z-10">
                        <div class="flex justify-between items-center mb-6">
                            <div class="font-bold text-xl tracking-wider">{{ $flight->airline->name }}</div>
                            <div class="bg-white/20 px-3 py-1 rounded-full text-xs font-bold tracking-wider">{{ $flight->flight_number }}</div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-center">
                                <p class="text-4xl font-bold mb-1">{{ $route->origin->iata_code }}</p>
                                <p class="text-xs text-slate-400 font-medium">{{ $route->origin->city }}</p>
                            </div>
                            <div class="flex-1 px-4 relative">
                                <div class="border-t-2 border-dashed border-slate-600 absolute top-1/2 left-4 right-4 transform -translate-y-1/2"></div>
                                <svg class="w-6 h-6 text-white absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </div>
                            <div class="text-center">
                                <p class="text-4xl font-bold mb-1">{{ $route->destination->iata_code }}</p>
                                <p class="text-xs text-slate-400 font-medium">{{ $route->destination->city }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Middle Section -->
                    <div class="p-6 bg-white relative">
                        <!-- Perforated Line -->
                        <div class="absolute top-0 left-0 right-0 border-t-2 border-dashed border-slate-200"></div>
                        <div class="w-6 h-6 bg-slate-50 rounded-full absolute -top-3 -left-3"></div>
                        <div class="w-6 h-6 bg-slate-50 rounded-full absolute -top-3 -right-3"></div>
                        
                        <div class="mb-6">
                            <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Passenger</p>
                            <p class="font-bold text-slate-800 text-lg">{{ $passenger->first_name }} {{ $passenger->last_name }}</p>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Date</p>
                                <p class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('d M') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Time</p>
                                <p class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Boarding</p>
                                <p class="font-bold text-rose-600">{{ \Carbon\Carbon::parse($schedule->departure_time)->subMinutes(40)->format('H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 pb-6 border-b border-slate-100">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Gate</p>
                                <p class="font-bold text-3xl {{ $schedule->status === 'DELAYED' ? 'text-amber-500 animate-pulse' : 'text-slate-800' }}">
                                    {{ $schedule->gate ? $schedule->gate->gate_number : 'TBA' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Terminal</p>
                                <p class="font-bold text-3xl text-slate-800">{{ $schedule->gate ? $schedule->gate->terminal : 'TBA' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Seat</p>
                                <p class="font-bold text-3xl text-indigo-600">{{ $seat ? $seat->aircraftSeat->seat_number : 'TBA' }}</p>
                            </div>
                        </div>
                        
                        <!-- QR Section -->
                        <div class="pt-6 text-center">
                            <div class="inline-block p-4 bg-white border border-slate-200 rounded-xl shadow-sm mb-3">
                                <!-- Dummy QR Code Graphic -->
                                <svg class="w-32 h-32 text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 15h6v6H3v-6zm2 2v2h2v-2H5zm14-4h2v2h-2v-2zm-2 2h2v2h-2v-2zm-2-2h2v2h-2v-2zm-4 4h2v2h-2v-2zm-2-2h2v2h-2v-2zm-2-2h2v2h-2v-2zm10 2h2v2h-2v-2zm0-4h2v2h-2v-2zm0 8h2v2h-2v-2zm-2-2h2v2h-2v-2zm-2 2h2v2h-2v-2zm-4-4h2v2h-2v-2zm6-4h2v2h-2v-2z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-slate-400 tracking-widest uppercase">E-Ticket: {{ $segmentPassenger->ticket ? $segmentPassenger->ticket->ticket_number : 'PENDING' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
            
        </div>
    </div>
</x-app-layout>
