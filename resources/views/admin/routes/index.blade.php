<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Rute</h2>
            <p class="text-sm text-slate-500">Cari, urutkan, dan kelola data rute penerbangan SkyBook.</p>
        </div>
        <x-admin.button href="{{ route('admin.routes.create') }}" variant="primary">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Rute
        </x-admin.button>
    </div>

    <x-admin.card class="mb-6">
        <form action="{{ route('admin.routes.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-sm font-medium text-slate-700">Pencarian</label>
                <x-admin.search-input name="search" :value="request('search')" placeholder="Cari kota atau kode bandara" />
            </div>
            <div class="md:w-64">
                <label class="mb-1 block text-sm font-medium text-slate-700">Urutkan</label>
                <select name="sort" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary">
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Terbaru</option>
                    <option value="distance" {{ request('sort') === 'distance' ? 'selected' : '' }}>Jarak</option>
                    <option value="estimated_duration" {{ request('sort') === 'estimated_duration' ? 'selected' : '' }}>Durasi</option>
                </select>
            </div>
            <div class="md:w-48">
                <label class="mb-1 block text-sm font-medium text-slate-700">Arah</label>
                <select name="direction" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary">
                    <option value="desc" {{ request('direction') !== 'asc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Asc</option>
                </select>
            </div>
            <div>
                <x-admin.button type="submit" variant="secondary">
                    Filter
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card>
        <x-admin.table :headers="['Asal (Origin)', 'Tujuan (Destination)', 'Jarak', 'Durasi', 'Aksi']" emptyMessage="Belum ada data rute.">
            @foreach($routes as $route)
                <tr class="hover:bg-slate-50 transition">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                        {{ $route->origin->city ?? '-' }} ({{ $route->origin->iata_code ?? '-' }})
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                        {{ $route->destination->city ?? '-' }} ({{ $route->destination->iata_code ?? '-' }})
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $route->distance ?? 0 }} km</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $route->estimated_duration ?? 0 }} menit</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.routes.edit', $route) }}" class="text-skybook-secondary hover:text-skybook-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus rute ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-4">
            {{ $routes->links() }}
        </div>
    </x-admin.card>
</x-admin-layout>
