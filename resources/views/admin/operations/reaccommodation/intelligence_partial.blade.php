<div class="space-y-6 pb-20">
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
        <h4 class="font-bold text-indigo-900 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Operational Memory
        </h4>
        <p class="text-sm text-indigo-700">Flight {{ $schedule->flight->flight_number }} is currently {{ strtoupper($schedule->status) }}. System has evaluated {{ count($recommendation->recommendation_payload) }} viable recovery strategies based on heuristic operational risk scores.</p>
    </div>

    <div class="space-y-4">
        @foreach($recommendation->recommendation_payload as $index => $strat)
            <div class="bg-white border {{ $index === 0 ? 'border-indigo-500 shadow-md relative overflow-hidden' : 'border-slate-200' }} rounded-xl p-5">
                
                @if($index === 0)
                    <div class="absolute top-0 right-0 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 uppercase tracking-widest rounded-bl-lg">Recommended</div>
                @endif

                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Rank #{{ $index + 1 }}</div>
                        <h4 class="font-bold text-slate-800 text-lg">{{ $strat['title'] }}</h4>
                        <p class="text-sm text-slate-600 mt-1">{{ $strat['description'] }}</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-center min-w-[80px]">
                        <div class="text-[10px] text-slate-500 font-bold uppercase">Total Score</div>
                        <div class="text-2xl font-black {{ $strat['total_score'] > 0 ? 'text-emerald-600' : 'text-slate-800' }}">{{ $strat['total_score'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    
                    <!-- Breakdown Component -->
                    <div class="bg-slate-50 rounded-lg p-4 font-mono text-xs">
                        <div class="font-bold text-slate-700 mb-2 border-b border-slate-200 pb-2 font-sans uppercase tracking-wider text-[10px]">Strategy Breakdown</div>
                        <div class="space-y-2">
                            @foreach($strat['breakdown'] as $key => $val)
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="{{ $val > 0 ? 'text-emerald-600' : ($val < 0 ? 'text-rose-600' : 'text-slate-500') }} font-bold">
                                        {{ $val > 0 ? '+' : '' }}{{ $val }}
                                    </span>
                                </div>
                            @endforeach
                            <div class="pt-2 border-t border-slate-200 flex justify-between items-center font-bold text-slate-800 mt-2">
                                <span>TOTAL SCORE</span>
                                <span>{{ $strat['total_score'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reasoning Component -->
                    <div>
                        <div class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[10px]">Reasoning</div>
                        <ul class="space-y-2 text-sm">
                            @foreach($strat['reasoning'] as $reason)
                                @php
                                    $isPos = str_starts_with($reason, '[+]');
                                @endphp
                                <li class="flex items-start gap-2">
                                    @if($isPos)
                                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-emerald-800">{{ substr($reason, 4) }}</span>
                                    @else
                                        <svg class="w-4 h-4 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-rose-800">{{ substr($reason, 4) }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            </div>
        @endforeach
    </div>
</div>
