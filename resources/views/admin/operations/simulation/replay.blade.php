<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[24px] font-bold text-slate-900">Event Replay: {{ $session->name }}</h2>
                <p class="text-sm text-slate-500">Review the IROPS simulation timeline and recovery events.</p>
            </div>
            <a href="{{ route('admin.operations.simulation') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-skybook-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Sandbox
            </a>
        </div>

        <div class="bg-slate-900 rounded-[24px] border border-slate-800 p-6 flex justify-between items-center text-white">
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Scenario Seed</div>
                    <div class="text-xl font-mono">{{ $session->scenario_seed }}</div>
                </div>
                <div class="text-right">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Time Window</div>
                    <div class="font-mono text-sm">
                        {{ $session->started_at->format('H:i:s') }} &rarr; 
                        {{ $session->ended_at ? $session->ended_at->format('H:i:s') : 'LIVE' }}
                    </div>
                </div>
            </div>

            <!-- Replay Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
                <div class="relative pl-8 border-l-2 border-slate-200 space-y-8">
                    @forelse($logs as $log)
                        @php
                            $icon = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                            $color = 'bg-slate-100 text-slate-500';
                            $title = $log->event_type;

                            if(str_contains($log->event_type, 'SCENARIO')) {
                                $icon = 'M13 10V3L4 14h7v7l9-11h-7z'; // Lightning
                                $color = 'bg-rose-100 text-rose-600';
                            } elseif (str_contains($log->event_type, 'GATE') || str_contains($log->event_type, 'CONFLICT')) {
                                $icon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'; // Alert
                                $color = 'bg-amber-100 text-amber-600';
                            } elseif (str_contains($log->event_type, 'RESTORED')) {
                                $icon = 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'; // Undo
                                $color = 'bg-indigo-100 text-indigo-600';
                            }
                        @endphp

                        <div class="relative">
                            <div class="absolute -left-[45px] {{ $color }} w-8 h-8 rounded-full flex items-center justify-center ring-4 ring-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path></svg>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-100 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono text-xs text-slate-400">#{{ str_pad($log->sequence, 4, '0', STR_PAD_LEFT) }}</span>
                                        <h4 class="font-bold text-slate-800">{{ $title }}</h4>
                                    </div>
                                    <span class="text-xs text-slate-500 font-mono">{{ $log->created_at->format('H:i:s.u') }}</span>
                                </div>
                                <p class="text-sm text-slate-600 font-mono bg-white border border-slate-200 p-2 rounded">
                                    {{ $log->payload['message'] ?? json_encode($log->payload) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 py-8">
                            No events recorded in this session.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
