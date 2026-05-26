<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-skybook-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <h2 class="text-2xl font-bold text-slate-800">Executive Analytics</h2>
            </div>
            <p class="text-sm text-slate-500 ml-11">Daily Operations & Business Intelligence Snapshot</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                Data Cached (TTL: 15m)
            </span>
            <button onclick="window.location.reload()" class="p-1.5 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 text-slate-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <!-- Revenue Intelligence -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-emerald-100 to-transparent rounded-full opacity-50 pointer-events-none"></div>
            
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Revenue Intelligence
            </h3>
            
            <div class="mb-6">
                <div class="text-sm font-medium text-slate-500 mb-1">Total System Revenue</div>
                <div class="text-4xl font-black text-slate-800">Rp {{ number_format($snapshot['revenue']['total_revenue'], 0, ',', '.') }}</div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <div class="text-xs font-semibold text-slate-500 mb-1">Flight Revenue</div>
                    <div class="text-lg font-bold text-slate-700">Rp {{ number_format($snapshot['revenue']['flight_revenue'], 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-indigo-500 mb-1">Ancillary Revenue</div>
                    <div class="text-lg font-bold text-indigo-700">Rp {{ number_format($snapshot['revenue']['ancillary_revenue'], 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 mb-1">Avg. Rev per Flight</div>
                    <div class="text-lg font-bold text-slate-700">Rp {{ number_format($snapshot['revenue']['revenue_per_flight'], 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500 mb-1">Avg. Rev per Passenger</div>
                    <div class="text-lg font-bold text-slate-700">Rp {{ number_format($snapshot['revenue']['revenue_per_passenger'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Operational Health -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-blue-100 to-transparent rounded-full opacity-50 pointer-events-none"></div>
            
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Operational Health
            </h3>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-medium text-slate-500 mb-1">On-Time Performance</div>
                    <div class="flex items-baseline gap-2">
                        <div class="text-4xl font-black {{ $snapshot['operations']['otp_percentage'] >= 85 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $snapshot['operations']['otp_percentage'] }}%
                        </div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-slate-500 mb-1">Total Flights</div>
                    <div class="text-4xl font-black text-slate-800">{{ $snapshot['operations']['total_flights'] }}</div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 mt-6">
                <div>
                    <div class="text-xs font-semibold text-amber-500 mb-1">Delay Ratio</div>
                    <div class="text-lg font-bold text-amber-700">{{ $snapshot['operations']['delay_ratio'] }}% <span class="text-sm font-normal text-amber-600/70">({{ $snapshot['operations']['delayed_flights'] }} flights)</span></div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-rose-500 mb-1">Cancellation Ratio</div>
                    <div class="text-lg font-bold text-rose-700">{{ $snapshot['operations']['cancellation_ratio'] }}% <span class="text-sm font-normal text-rose-600/70">({{ $snapshot['operations']['cancelled_flights'] }} flights)</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Passenger Behaviors -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Passenger Behaviors
            </h3>

            <div class="space-y-5">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-slate-600">Check-in Conversion</span>
                        <span class="font-bold text-slate-800">{{ $snapshot['passenger']['checkin_conversion'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-sky-500 h-2 rounded-full" style="width: {{ $snapshot['passenger']['checkin_conversion'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-slate-600">Ancillary Attachment</span>
                        <span class="font-bold text-indigo-600">{{ $snapshot['passenger']['ancillary_attachment_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $snapshot['passenger']['ancillary_attachment_rate'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-slate-600">Priority Boarding Uptake</span>
                        <span class="font-bold text-purple-600">{{ $snapshot['passenger']['priority_boarding_adoption'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $snapshot['passenger']['priority_boarding_adoption'] }}%"></div>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-1">Average Baggage Purchase</div>
                    <div class="text-xl font-bold text-slate-800">Rp {{ number_format($snapshot['passenger']['average_baggage_purchase'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Security & Fraud Watch -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Security & Fraud Watch
                </h3>
                @if($snapshot['security_alerts']['total_security_events'] > 0)
                    <span class="px-2.5 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-full animate-pulse">
                        {{ $snapshot['security_alerts']['total_security_events'] }} Incidents Detected
                    </span>
                @else
                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                        System Secure
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 flex-1">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-1">Invalid Scans</div>
                    <div class="text-3xl font-black {{ $snapshot['security_alerts']['invalid_scans'] > 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $snapshot['security_alerts']['invalid_scans'] }}</div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-1">Duplicate Attempts</div>
                    <div class="text-3xl font-black {{ $snapshot['security_alerts']['duplicate_scans'] > 0 ? 'text-amber-600' : 'text-slate-700' }}">{{ $snapshot['security_alerts']['duplicate_scans'] }}</div>
                </div>
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-1">Superseded Passes</div>
                    <div class="text-3xl font-black {{ $snapshot['security_alerts']['superseded_scans'] > 0 ? 'text-indigo-600' : 'text-slate-700' }}">{{ $snapshot['security_alerts']['superseded_scans'] }}</div>
                </div>
            </div>

            <div class="bg-sky-50 border border-sky-100 rounded-lg p-4 text-sm text-sky-800">
                <div class="font-bold mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Analytics Pipeline Status
                </div>
                <p>Data aggregation is running in <span class="font-mono bg-sky-100 px-1 rounded">Live SQL + Cache</span> mode. Real-time metric computations are temporarily cached to ensure optimal OCC performance.</p>
            </div>
        </div>
    </div>
</x-admin-layout>
