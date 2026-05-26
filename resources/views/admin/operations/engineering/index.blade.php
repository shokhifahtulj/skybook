<x-admin-layout>
    <div class="space-y-6">
        @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Active Maintenances -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Active Maintenance Events (Grounded/AOG)
                    </h3>
                </div>
                
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Aircraft</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Severity</th>
                                <th class="px-6 py-3">Start</th>
                                <th class="px-6 py-3">Est. End</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($activeMaintenances as $maintenance)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800">{{ $maintenance->aircraft->model }}</div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $maintenance->aircraft->id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ str_replace('_', ' ', $maintenance->maintenance_type) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $sevColor = [
                                                'minor' => 'bg-amber-100 text-amber-700',
                                                'major' => 'bg-rose-100 text-rose-700',
                                                'critical' => 'bg-red-600 text-white',
                                            ][$maintenance->severity] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="{{ $sevColor }} px-2 py-1 rounded text-xs font-bold uppercase">{{ $maintenance->severity }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ $maintenance->start_at->format('d M H:i') }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs">
                                        {{ $maintenance->end_at->format('d M H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-xs font-bold uppercase">{{ str_replace('_', ' ', $maintenance->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <x-admin.button type="button" variant="secondary" onclick="document.getElementById('releaseModal{{ $maintenance->id }}').classList.remove('hidden')">
                                            Engineering Release
                                        </x-admin.button>

                                        <!-- Release Modal -->
                                        <div id="releaseModal{{ $maintenance->id }}" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                                            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                                                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                                    <h3 class="font-bold text-slate-800">Engineering Release: {{ $maintenance->aircraft->model }}</h3>
                                                    <button onclick="document.getElementById('releaseModal{{ $maintenance->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.operations.engineering.release', $maintenance) }}" method="POST" class="p-6">
                                                    @csrf
                                                    <div class="mb-4 text-sm text-slate-600 whitespace-normal text-left">
                                                        You are about to release this aircraft from maintenance. This will change its status back to <strong>AVAILABLE</strong>, allowing OCC to dispatch it.
                                                    </div>
                                                    
                                                    <div class="space-y-4 mb-6">
                                                        <div>
                                                            <label class="block text-sm font-medium text-slate-700 mb-1">Resolution / Release Notes</label>
                                                            <textarea name="resolution" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="e.g. Hydraulic pump replaced. Tested OK."></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" onclick="document.getElementById('releaseModal{{ $maintenance->id }}').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Cancel</button>
                                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                                                            Sign Dispatch Release
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <p>No active aircraft maintenance.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Fleet Status -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-800">Fleet Overview</h3>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Aircraft</th>
                                    <th class="px-6 py-3">Type</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($aircrafts as $ac)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-3">
                                            <div class="font-bold text-slate-800">{{ $ac->model }}</div>
                                            <div class="text-xs text-slate-500">{{ $ac->airline->name ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="text-xs text-slate-500 font-mono">{{ $ac->capacity }} seats</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            @php
                                                $statusColor = [
                                                    'available' => 'bg-emerald-100 text-emerald-700',
                                                    'assigned' => 'bg-blue-100 text-blue-700',
                                                    'maintenance' => 'bg-rose-100 text-rose-700',
                                                    'grounded' => 'bg-red-600 text-white',
                                                    'delayed_rotation' => 'bg-amber-100 text-amber-700',
                                                ][$ac->operational_status] ?? 'bg-slate-100 text-slate-700';
                                            @endphp
                                            <span class="{{ $statusColor }} px-2 py-1 rounded text-xs font-bold uppercase">{{ str_replace('_', ' ', $ac->operational_status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Schedule Maintenance Form -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 pb-2 border-b border-slate-100">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Schedule / Ground Aircraft
                    </h3>

                    <form action="{{ route('admin.operations.engineering.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Select Aircraft</label>
                            <select name="aircraft_id" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                                <option value="">Select Aircraft...</option>
                                @foreach($aircrafts as $ac)
                                    <option value="{{ $ac->id }}">{{ $ac->model }} ({{ str_replace('_', ' ', $ac->operational_status) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                                <select name="maintenance_type" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="unscheduled">Unscheduled</option>
                                    <option value="aog">AOG (Aircraft On Ground)</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="line_maintenance">Line Maintenance</option>
                                    <option value="heavy_maintenance">Heavy Maintenance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Severity</label>
                                <select name="severity" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                                    <option value="minor">Minor</option>
                                    <option value="major" selected>Major</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                                <option value="planned">Planned (Future)</option>
                                <option value="in_progress">In Progress (Immediate Grounding)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Start</label>
                                <input type="datetime-local" name="start_at" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Est. End</label>
                                <input type="datetime-local" name="end_at" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required value="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Notes / Defect Log</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" placeholder="e.g. Hydraulic leak reported on inbound flight"></textarea>
                        </div>

                        <x-admin.button type="submit" variant="danger" class="w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Submit Engineering Order
                        </x-admin.button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
