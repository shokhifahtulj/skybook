<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Edit Pengguna</h2>
        <p class="text-sm text-slate-500">Perbarui profil dan role pengguna.</p>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $currentRole = $user->hasRole('admin') ? 'admin' : 'user';
            @endphp
            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.input name="name" label="Nama Lengkap" :value="old('name', $user->name)" required />
                <x-admin.input name="email" label="Email" type="email" :value="old('email', $user->email)" required />
                <x-admin.input name="password" label="Password Baru" type="password" placeholder="Biarkan kosong jika tidak ingin mengubah" />
                <x-admin.input name="password_confirmation" label="Konfirmasi Password Baru" type="password" placeholder="Biarkan kosong jika tidak ingin mengubah" />
                <div class="md:col-span-2">
                    <x-admin.form-select name="role" label="Role" required :value="old('role', $currentRole)">
                        <option value="user" {{ old('role', $currentRole) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $currentRole) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </x-admin.form-select>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-admin.button type="submit" variant="primary">Perbarui Pengguna</x-admin.button>
                <x-admin.button href="{{ route('admin.users.index') }}" variant="secondary">Batal</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
