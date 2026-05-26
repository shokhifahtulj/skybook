<div class="space-y-6">
    <!-- Passenger Header -->
    <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
        <div class="bg-indigo-100 text-indigo-700 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg">
            {{ substr($bsp->passenger->first_name, 0, 1) }}{{ substr($bsp->passenger->last_name, 0, 1) }}
        </div>
        <div>
            <h3 class="font-bold text-slate-800 text-lg">{{ $bsp->passenger->first_name }} {{ $bsp->passenger->last_name }}</h3>
            <p class="text-sm text-slate-500">Booking Ref: <span class="font-mono text-indigo-600 font-bold">{{ $bsp->segment->booking->pnr }}</span></p>
        </div>
    </div>

    <!-- Current State -->
    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 flex justify-between items-center">
        <div>
            <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Effective Flight</div>
            <div class="font-bold text-slate-800 text-lg">{{ $bsp->currentSchedule->flight->flight_number }}</div>
            <div class="text-sm text-slate-600">{{ $bsp->currentSchedule->departure_datetime->format('d M Y H:i') }}</div>
        </div>
        <div class="text-right">
            <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Status</div>
            @if($bsp->reassignments->count() > 0)
                <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-bold">REBOOKED</span>
            @else
                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-bold">ORIGINAL</span>
            @endif
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative pt-4 pl-4">
        <!-- Vertical Line -->
        <div class="absolute left-[27px] top-4 bottom-0 w-0.5 bg-slate-200"></div>

        <div class="space-y-8 relative">
            @foreach($timeline as $event)
                <div class="flex gap-4">
                    <!-- Icon -->
                    <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center shrink-0 
                        {{ $event['type'] === 'booking' ? 'bg-indigo-100 text-indigo-600 ring-4 ring-white' : '' }}
                        {{ $event['type'] === 'notification' ? 'bg-amber-100 text-amber-600 ring-4 ring-white' : '' }}
                        {{ $event['type'] === 'acknowledgement' ? 'bg-emerald-100 text-emerald-600 ring-4 ring-white' : '' }}
                        {{ $event['type'] === 'reassignment' ? 'bg-rose-100 text-rose-600 ring-4 ring-white' : '' }}
                        ">
                        
                        @if($event['icon'] === 'ticket')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        @elseif($event['icon'] === 'bell')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @elseif($event['icon'] === 'check')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @elseif($event['icon'] === 'refresh')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 pb-2">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-slate-800 text-sm">{{ $event['title'] }}</h4>
                            <span class="text-xs text-slate-400 font-mono">{{ $event['time']->format('H:i') }} <span class="text-[10px]">{{ $event['time']->format('d/m') }}</span></span>
                        </div>
                        <p class="text-sm text-slate-600">{{ $event['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
