<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-admin.form-select name="origin_airport_id" label="Bandara Asal (Origin)">
            <option value="">Pilih Bandara Asal</option>
            @foreach($airports as $airport)
                <option value="{{ $airport->id }}" {{ old('origin_airport_id', $route->origin_airport_id ?? '') == $airport->id ? 'selected' : '' }}>
                    {{ $airport->city }} ({{ $airport->iata_code }}) - {{ $airport->name }}
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div>
        <x-admin.form-select name="destination_airport_id" label="Bandara Tujuan (Destination)">
            <option value="">Pilih Bandara Tujuan</option>
            @foreach($airports as $airport)
                <option value="{{ $airport->id }}" {{ old('destination_airport_id', $route->destination_airport_id ?? '') == $airport->id ? 'selected' : '' }}>
                    {{ $airport->city }} ({{ $airport->iata_code }}) - {{ $airport->name }}
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div>
        <x-admin.form-input type="number" name="distance" label="Jarak (km)" value="{{ old('distance', $route->distance ?? '') }}" placeholder="e.g. 500" />
    </div>

    <div>
        <x-admin.form-input type="number" name="estimated_duration" label="Durasi Penerbangan (Menit)" value="{{ old('estimated_duration', $route->estimated_duration ?? '') }}" placeholder="e.g. 120" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
    <x-admin.button href="{{ route('admin.routes.index') }}" variant="secondary">
        Batal
    </x-admin.button>
    <x-admin.button type="submit" variant="primary">
        Simpan Rute
    </x-admin.button>
</div>
