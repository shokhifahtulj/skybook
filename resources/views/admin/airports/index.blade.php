<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Manajemen Bandara</h2>
        <a href="{{ route('admin.airports.create') }}" class="inline-flex items-center justify-center rounded-lg bg-skybook-secondary px-5 py-2.5 text-center text-sm font-medium text-white transition hover:bg-skybook-secondary/90 focus:ring-4 focus:ring-skybook-secondary/30">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Bandara
        </a>
    </div>

    <x-admin.card>
        <x-admin.table :headers="['Kode IATA', 'Nama Bandara', 'Kota / Negara', 'Timezone', 'Aksi']" emptyMessage="Belum ada data bandara.">
            @foreach($airports as $airport)
                <tr class="hover:bg-slate-50 transition">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $airport->iata_code }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $airport->name }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $airport->city }}, {{ $airport->country }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $airport->timezone }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.airports.edit', $airport) }}" class="text-skybook-secondary hover:text-skybook-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.airports.destroy', $airport) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus bandara ini?');">
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
            {{ $airports->links() }}
        </div>
    </x-admin.card>
</x-admin-layout>