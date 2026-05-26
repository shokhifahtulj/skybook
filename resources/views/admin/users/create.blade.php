<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Tambah Pengguna</h2>
        <p class="text-sm text-slate-500">Buat akun baru untuk admin atau user.</p>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.input name="name" label="Nama Lengkap" :value="old('name')" required />
                <x-admin.input name="email" label="Email" type="email" :value="old('email')" required />
                <x-admin.input name="password" label="Password" type="password" required />
                <x-admin.input name="password_confirmation" label="Konfirmasi Password" type="password" required />
                <div class="md:col-span-2">
                    <x-admin.form-select name="role" label="Role" required :value="old('role')">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </x-admin.form-select>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-admin.button type="submit" variant="primary">Simpan Pengguna</x-admin.button>
                <x-admin.button href="{{ route('admin.users.index') }}" variant="secondary">Batal</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin-layout>
