<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-admin.form-input name="flight_number" label="Nomor Penerbangan" value="{{ old('flight_number', $flight->flight_number ?? '') }}" placeholder="e.g. GA-101" />
    </div>

    <div>
        <x-admin.form-select name="airline_id" label="Maskapai">
            <option value="">Pilih Maskapai</option>
            @foreach($airlines as $airline)
                <option value="{{ $airline->id }}" {{ old('airline_id', $flight->airline_id ?? '') == $airline->id ? 'selected' : '' }}>
                    {{ $airline->name }} ({{ $airline->code }})
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div class="md:col-span-2">
        <x-admin.form-select name="route_id" label="Rute">
            <option value="">Pilih Rute</option>
            @foreach($routes as $route)
                <option value="{{ $route->id }}" {{ old('route_id', $flight->route_id ?? '') == $route->id ? 'selected' : '' }}>
                    {{ $route->origin->city }} ({{ $route->origin->iata_code }}) &rarr; {{ $route->destination->city }} ({{ $route->destination->iata_code }})
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div class="md:col-span-2">
        <x-admin.form-select name="aircraft_id" label="Pesawat (Opsional)">
            <option value="">Pilih Pesawat</option>
            @foreach($aircrafts as $aircraft)
                <option value="{{ $aircraft->id }}" {{ old('aircraft_id', $flight->aircraft_id ?? '') == $aircraft->id ? 'selected' : '' }}>
                    {{ $aircraft->model }} - Kapasitas: {{ $aircraft->capacity }} (Maskapai: {{ $aircraft->airline->name }})
                </option>
            @endforeach
        </x-admin.form-select>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
    <x-admin.button href="{{ route('admin.flights.index') }}" variant="secondary">
        Batal
    </x-admin.button>
    <x-admin.button type="submit" variant="primary">
        Simpan Penerbangan
    </x-admin.button>
</div>
