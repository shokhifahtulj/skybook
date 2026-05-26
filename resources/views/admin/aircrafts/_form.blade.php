<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-admin.form-select name="airline_id" label="Maskapai">
            <option value="">Pilih Maskapai</option>
            @foreach($airlines as $airline)
                <option value="{{ $airline->id }}" {{ old('airline_id', $aircraft->airline_id ?? '') == $airline->id ? 'selected' : '' }}>
                    {{ $airline->name }} ({{ $airline->code }})
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div>
        <x-admin.form-input name="model" label="Model Pesawat" value="{{ old('model', $aircraft->model ?? '') }}" placeholder="e.g. Boeing 737-800" />
    </div>

    <div>
        <x-admin.form-input type="number" name="capacity" label="Kapasitas Kursi" value="{{ old('capacity', $aircraft->capacity ?? '') }}" placeholder="e.g. 189" />
    </div>

    <div>
        <x-admin.form-input name="seat_layout" label="Tata Letak Kursi" value="{{ old('seat_layout', $aircraft->seat_layout ?? '3-3') }}" placeholder="e.g. 3-3, 2-4-2" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
    <x-admin.button href="{{ route('admin.aircrafts.index') }}" variant="secondary">
        Batal
    </x-admin.button>
    <x-admin.button type="submit" variant="primary">
        Simpan Pesawat
    </x-admin.button>
</div>
