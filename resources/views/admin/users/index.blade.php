<x-admin-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-500">Kelola akun admin dan user yang dapat mengakses SkyBook.</p>
        </div>
        <x-admin.button href="{{ route('admin.users.create') }}" variant="primary">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Pengguna
        </x-admin.button>
    </div>

    <x-admin.card class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-sm font-medium text-slate-700">Pencarian</label>
                <x-admin.search-input name="search" :value="request('search')" placeholder="Nama / email / role" />
            </div>
            <div class="md:w-56">
                <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                <select name="role" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary">
                    <option value="">Semua role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div>
                <x-admin.button type="submit" variant="secondary">Filter</x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card>
        <x-admin.table :headers="['Nama', 'Email', 'Role', 'Terakhir Diperbarui', 'Aksi']" emptyMessage="Belum ada pengguna.">
            @foreach($users as $user)
                @php
                    $displayRole = $user->hasRole('admin') ? 'admin' : 'user';
                @endphp
                <tr class="hover:bg-slate-50 transition">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                    <td class="whitespace-nowrap px-6 py-4">{{ $user->email }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $user->hasRole('admin') ? 'bg-emerald-100 text-emerald-700' : 'bg-skybook-accent text-skybook-primary' }}">
                            {{ $displayRole }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">{{ $user->updated_at->format('d M Y H:i') }}</td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-skybook-secondary hover:text-skybook-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-admin.card>
</x-admin-layout>
