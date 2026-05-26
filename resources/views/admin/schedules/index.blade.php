<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Jadwal Penerbangan</h2>
            <p class="text-sm text-slate-500">Kelola jadwal, harga dinamis, dan status penerbangan SkyBook.</p>
        </div>
        <x-admin.button href="{{ route('admin.schedules.create') }}" variant="primary">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Jadwal
        </x-admin.button>
    </div>

    <!-- Filters Section -->
    <x-admin.card class="mb-6">
        <form action="{{ route('admin.schedules.index') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-sm font-medium text-slate-700">Pencarian Nomor Penerbangan</label>
                <x-admin.search-input name="search" :value="request('search')" placeholder="e.g. GA-101" />
            </div>
            <div class="flex-1">
                <x-admin.form-input type="date" name="date" label="Tanggal Keberangkatan" :value="request('date')" />
            </div>
            <div class="flex-1">
                <x-admin.form-select name="airline_id" label="Maskapai">
                    <option value="">Semua Maskapai</option>
                    @foreach($airlines as $airline)
                        <option value="{{ $airline->id }}" {{ request('airline_id') == $airline->id ? 'selected' : '' }}>{{ $airline->name }}</option>
                    @endforeach
                </x-admin.form-select>
            </div>
            <div class="flex-1">
                <x-admin.form-select name="status" label="Status">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                    <option value="boarding" {{ request('status') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                    <option value="departed" {{ request('status') == 'departed' ? 'selected' : '' }}>Departed</option>
                    <option value="arrived" {{ request('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </x-admin.form-select>
            </div>
            <div>
                <x-admin.button type="submit" variant="secondary">
                    Filter
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-slate-800 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Penerbangan</th>
                        <th class="px-6 py-4 font-semibold">Waktu & Rute</th>
                        <th class="px-6 py-4 font-semibold">Pesawat</th>
                        <th class="px-6 py-4 font-semibold">Status & Seat</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-skybook-primary">{{ $schedule->flight->flight_number }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 font-medium text-slate-800">
                                        {{ $schedule->flight->airline->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-slate-800">{{ $schedule->flight->route->origin->iata_code }}</span>
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                    <span class="font-medium text-slate-800">{{ $schedule->flight->route->destination->iata_code }}</span>
                                </div>
                                <div class="text-xs text-slate-500">
                                    Brgkt: {{ $schedule->departure_datetime->format('d M Y, H:i') }}<br>
                                    Tiba: {{ $schedule->arrival_datetime->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($schedule->flight->aircraft)
                                    <div class="font-medium text-slate-800">{{ $schedule->flight->aircraft->model }}</div>
                                    <div class="text-xs text-slate-500">Kapasitas: {{ $schedule->flight->aircraft->capacity }}</div>
                                @else
                                    <span class="text-slate-400 italic">Belum ditentukan</span>
                                @endif
                                <div class="mt-1 text-xs text-slate-500">
                                    Gate: {{ $schedule->gate ?? '-' }} | Terminal: {{ $schedule->terminal ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="mb-2">
                                    <x-admin.status-badge :status="$schedule->status" />
                                </div>
                                <div class="text-xs text-slate-500">
                                    Sisa Kursi: <span class="font-bold {{ $schedule->available_seats > 10 ? 'text-green-600' : 'text-red-600' }}">{{ $schedule->available_seats }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-skybook-secondary hover:text-skybook-primary" title="Edit">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada data jadwal penerbangan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    </x-admin.card>
</x-admin-layout>
