<x-admin-layout>
    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">OTA Dashboard</h1>
            <p class="text-sm text-slate-500">Welcome back, Admin. Here's the latest summary of your travel data.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm border border-slate-200">
                <svg class="h-4 w-4 text-skybook-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ now()->format('l, d M Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1 -->
        <x-admin.stat-card 
            title="Total Airlines" 
            value="{{ $stats['totalAirlines'] }}" 
            icon="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" 
            trend="12" 
            trendDirection="up" />

        <!-- Card 2 -->
        <x-admin.stat-card 
            title="Total Airports" 
            value="{{ $stats['totalAirports'] }}" 
            icon="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" 
            trend="4" 
            trendDirection="up" />

        <!-- Card 3 -->
        <x-admin.stat-card 
            title="Active Routes" 
            value="{{ $stats['totalRoutes'] }}" 
            icon="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" 
            trend="18" 
            trendDirection="up" />

        <!-- Card 4 -->
        <x-admin.stat-card 
            title="Total Flights" 
            value="{{ $stats['totalFlights'] }}" 
            icon="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" 
            trend="2" 
            trendDirection="up" />
    </div>

    @if(isset($predictiveAlerts) && $predictiveAlerts->isNotEmpty())
    <!-- Network Foresight (Predictive Intelligence) Panel -->
    <div class="mb-8">
        <div class="bg-slate-900 rounded-xl shadow-lg border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
                <h3 class="font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    Network Foresight
                </h3>
                <span class="px-2 py-1 bg-indigo-500/20 text-indigo-300 text-xs font-bold rounded-full border border-indigo-500/30">
                    {{ $predictiveAlerts->count() }} Early Warnings
                </span>
            </div>
            <div class="p-0">
                <div class="divide-y divide-slate-800/50">
                    @foreach($predictiveAlerts as $alert)
                        @php
                            $isMitigated = $alert->status === 'MITIGATED';
                            $color = $isMitigated ? 'emerald' : 'slate';
                            $icon = $isMitigated ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                            
                            if (!$isMitigated) {
                                if ($alert->severity === 'CRITICAL') { $color = 'rose'; $icon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'; }
                                if ($alert->severity === 'HIGH') { $color = 'orange'; $icon = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'; }
                                if ($alert->severity === 'MEDIUM') { $color = 'amber'; }
                            }
                        @endphp
                        <div class="p-4 hover:bg-slate-800/30 transition flex flex-col md:flex-row md:items-start gap-4 {{ $isMitigated ? 'opacity-80' : '' }}">
                            <!-- Severity Badge -->
                            <div class="flex-shrink-0 w-32 mt-1">
                                <span class="px-2 py-1 rounded bg-{{ $color }}-500/20 text-{{ $color }}-400 border border-{{ $color }}-500/30 text-[10px] font-bold uppercase tracking-widest block text-center">
                                    {{ $isMitigated ? 'AUTO-MITIGATED' : $alert->severity }}
                                </span>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path></svg>
                                    <h4 class="font-bold text-slate-200 text-sm {{ $isMitigated ? 'line-through text-slate-500' : '' }}">
                                        {{ str_replace('_', ' ', $alert->prediction_type) }} &bull; {{ $alert->flightSchedule->flight->flight_number }}
                                    </h4>
                                    <span class="text-xs text-slate-500 font-mono ml-2">CONFIDENCE: {{ $alert->confidence_score }}%</span>
                                </div>
                                <p class="text-slate-400 text-sm mb-2 {{ $isMitigated ? 'line-through opacity-50' : '' }}">{{ $alert->description }}</p>
                                
                                @if($isMitigated && isset($alert->automation_payload))
                                    <div class="bg-emerald-900/20 border border-emerald-800/50 rounded p-3 text-xs font-mono text-emerald-300 mt-2">
                                        <div class="font-bold text-emerald-400 mb-1 border-b border-emerald-800/50 pb-1">SYSTEM_AUTO_RESOLVE EXECUTED</div>
                                        <div class="grid grid-cols-1 gap-1 mt-2">
                                            <div>> Action: <span class="text-white">{{ $alert->automation_payload['action_taken'] }}</span></div>
                                            <div>> Impact: <span class="text-emerald-400">{{ $alert->automation_payload['impact_summary'] }}</span></div>
                                            <div>> Reversible: <span class="text-white">{{ $alert->automation_payload['rollback_available'] ? 'YES' : 'NO' }}</span></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-col md:items-end gap-1 flex-shrink-0">
                                @if(!$isMitigated)
                                    <div class="text-xs text-slate-500 font-mono mb-1">Impact in: <span class="text-white font-bold">{{ floor($alert->forecast_window_minutes / 60) }}h {{ $alert->forecast_window_minutes % 60 }}m</span></div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.operations.simulation') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded border border-slate-700 transition">View Prediction</a>
                                        <a href="{{ route('admin.operations.reaccommodation') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded transition shadow shadow-indigo-900/50">Proactive Resolve</a>
                                    </div>
                                @else
                                    <div class="text-xs text-emerald-500 font-mono mb-1">Resolved at: <span class="font-bold">{{ $alert->resolved_at->format('H:i') }}</span></div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.operations.simulation') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-400 text-xs font-bold rounded border border-slate-700 transition">View Log</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Chart Area -->
        <x-admin.card title="Airlines Growth & Operations" class="lg:col-span-2">
            <div id="growthChart" class="w-full" style="min-height: 300px;"></div>
        </x-admin.card>

        <!-- Recent Airports -->
        <x-admin.card title="Bandara Baru">
            <div class="space-y-4">
                @forelse($recentAirports as $airport)
                    <div class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 transition hover:bg-slate-50">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-skybook-primary/10 text-skybook-primary font-bold">
                            {{ $airport->iata_code }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $airport->city }}</p>
                            <p class="text-xs text-slate-500">{{ $airport->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500">Belum ada bandara tercatat</div>
                @endforelse
            </div>
        </x-admin.card>
    </div>

    <!-- Recent Flights -->
    <x-admin.card title="Master Data Flights Terbaru">
        <x-admin.table :headers="['Penerbangan', 'Maskapai', 'Asal', 'Tujuan', 'Status']" emptyMessage="Belum ada rute penerbangan.">
            @foreach( $recentFlights as $flight)
                <tr class="hover:bg-slate-50 transition">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $flight->flight_number }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $flight->airline->name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $flight->route->origin->city ?? '-' }} ({{ $flight->route->origin->iata_code ?? '-' }})</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $flight->route->destination->city ?? '-' }} ({{ $flight->route->destination->iata_code ?? '-' }})</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <x-admin.badge type="success">Active</x-admin.badge>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
    </x-admin.card>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Routes Added',
                    data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
                }, {
                    name: 'Flights Scheduled',
                    data: [1, 15, 26, 20, 42, 68, 55, 84, 125]
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                colors: ['#00224f', '#0770e3'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [50, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                },
            };

            var chart = new ApexCharts(document.querySelector("#growthChart"), options);
            chart.render();
        });
    </script>
</x-admin-layout>
