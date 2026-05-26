<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-[28px] font-bold text-slate-900">{{ __('Profile') }}</h2>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi akun, keamanan, dan preferensi profil Anda.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-admin.card title="Informasi Profil" description="Perbarui nama, email, dan detail akun Anda.">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-admin.card>

        <x-admin.card title="Keamanan Akun" description="Ganti kata sandi dan lakukan pembaruan keamanan akun.">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-admin.card>

        <x-admin.card title="Kelola Akun" description="Hapus akun Anda jika diperlukan.">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-admin.card>
    </div>
</x-app-layout>
