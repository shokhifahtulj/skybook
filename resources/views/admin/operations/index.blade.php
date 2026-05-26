<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Operations Control Center</h2>
            <p class="text-sm text-slate-500">Pilih jadwal penerbangan aktif untuk dimonitor</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($activeSchedules as $schedule)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Flight</div>
                            <div class="text-xl font-bold text-indigo-600">{{ $schedule->flight->flight_number }}</div>
                        </div>
                        <div class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $schedule->status === 'boarding' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ str_replace('_', ' ', $schedule->status) }}
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-1">
                            <div class="text-xs font-medium text-slate-500 uppercase">Origin</div>
                            <div class="text-lg font-bold text-slate-800">{{ $schedule->flight->route->origin->iata_code }}</div>
                        </div>
                        <div class="text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <div class="flex-1 text-right">
                            <div class="text-xs font-medium text-slate-500 uppercase">Dest</div>
                            <div class="text-lg font-bold text-slate-800">{{ $schedule->flight->route->destination->iata_code }}</div>
                        </div>
                    </div>
                    
                    <div class="text-sm text-slate-600 mb-6">
                        <div class="flex justify-between mb-1">
                            <span>Departure:</span>
                            <span class="font-semibold">{{ $schedule->departure_datetime->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.operations.show', $schedule) }}" class="block w-full text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold py-2 px-4 rounded-lg transition-colors">
                        Open Dashboard
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center text-slate-500">
                    Tidak ada jadwal penerbangan aktif saat ini.
                </div>
            </div>
        @endforelse
    </div>
</x-admin-layout>
