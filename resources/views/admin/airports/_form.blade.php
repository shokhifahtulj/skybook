<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <x-admin.input name="iata_code" label="Kode IATA (Misal: CGK)" value="{{ isset($airport) ? $airport->iata_code : '' }}" required />
    <x-admin.input name="name" label="Nama Bandara" value="{{ isset($airport) ? $airport->name : '' }}" required />
    <x-admin.input name="city" label="Kota" value="{{ isset($airport) ? $airport->city : '' }}" required />
    <x-admin.input name="country" label="Negara" value="{{ isset($airport) ? $airport->country : 'Indonesia' }}" required />
    <x-admin.input name="timezone" label="Timezone" value="{{ isset($airport) ? $airport->timezone : 'Asia/Jakarta' }}" required />
</div>

<div class="mt-8 flex items-center gap-4">
    <x-admin.button type="submit" variant="primary">
        Simpan Data
    </x-admin.button>
    <x-admin.button href="{{ route('admin.airports.index') }}" variant="secondary">
        Batal
    </x-admin.button>
</div>