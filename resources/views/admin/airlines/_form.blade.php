<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <x-admin.input name="code" label="Kode IATA (Misal: GA)" value="{{ isset($airline) ? $airline->code : '' }}" required />
    <x-admin.input name="name" label="Nama Maskapai" value="{{ isset($airline) ? $airline->name : '' }}" required />
    
    <div class="col-span-1 md:col-span-2">
        <x-admin.input type="file" name="logo" label="Logo Maskapai (Opsional)" value="{{ isset($airline) && $airline->logo ? Storage::url($airline->logo) : '' }}" />
    </div>

    <div class="mb-4 col-span-1 md:col-span-2">
        <label for="status" class="mb-2.5 block font-medium text-slate-800">Status Maskapai</label>
        <select name="status" id="status" class="w-full rounded-lg border border-slate-300 bg-transparent py-3 px-5 outline-none transition focus:border-skybook-secondary focus:ring-1 focus:ring-skybook-secondary">
            <option value="active" {{ (isset($airline) && $airline->status === 'active') || old('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ (isset($airline) && $airline->status === 'inactive') || old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-8 flex items-center gap-4">
    <x-admin.button type="submit" variant="primary">
        Simpan Data
    </x-admin.button>
    <x-admin.button href="{{ route('admin.airlines.index') }}" variant="secondary">
        Batal
    </x-admin.button>
</div>