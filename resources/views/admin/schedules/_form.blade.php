<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-admin.form-select name="flight_id" label="Penerbangan">
            <option value="">Pilih Penerbangan</option>
            @foreach($flights as $flight)
                <option value="{{ $flight->id }}" {{ old('flight_id', $schedule->flight_id ?? '') == $flight->id ? 'selected' : '' }}>
                    {{ $flight->flight_number }} - {{ $flight->airline->name }} ({{ $flight->route->origin->iata_code }} &rarr; {{ $flight->route->destination->iata_code }})
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Waktu Keberangkatan</label>
        <input type="datetime-local" name="departure_datetime" value="{{ old('departure_datetime', isset($schedule) ? $schedule->departure_datetime->format('Y-m-d\TH:i') : '') }}" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" required>
        @error('departure_datetime') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Waktu Kedatangan</label>
        <input type="datetime-local" name="arrival_datetime" value="{{ old('arrival_datetime', isset($schedule) ? $schedule->arrival_datetime->format('Y-m-d\TH:i') : '') }}" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" required>
        @error('arrival_datetime') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
    </div>

    <div>
        <x-admin.form-input name="terminal" label="Terminal (Opsional)" value="{{ old('terminal', $schedule->terminal ?? '') }}" placeholder="e.g. T3" />
    </div>

    <div>
        <x-admin.form-input name="gate" label="Gate (Opsional)" value="{{ old('gate', $schedule->gate ?? '') }}" placeholder="e.g. G12" />
    </div>

    <div class="md:col-span-2">
        <x-admin.form-select name="status" label="Status Penerbangan">
            @php $statuses = ['scheduled', 'boarding', 'departed', 'arrived', 'delayed', 'cancelled']; @endphp
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ old('status', $schedule->status ?? 'scheduled') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </x-admin.form-select>
    </div>

    <!-- Pricing Section -->
    <div class="md:col-span-2 mt-4">
        <h3 class="mb-4 text-lg font-bold text-slate-800 border-b pb-2">Pengaturan Harga & Kuota (Dynamic Pricing)</h3>
        
        @if($errors->has('prices'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                {{ $errors->first('prices') }}
            </div>
        @endif

        <div id="pricing-container" class="space-y-4">
            @php
                $oldPrices = old('prices', isset($schedule) ? $schedule->prices->toArray() : []);
                if (empty($oldPrices)) {
                    $oldPrices = [['cabin_class' => 'economy', 'price' => '', 'quota' => '']];
                }
            @endphp

            @foreach($oldPrices as $index => $priceData)
            <div class="pricing-row flex flex-col md:flex-row gap-4 items-end bg-slate-50 p-4 rounded-lg border border-slate-200 relative">
                <div class="flex-1 w-full">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kelas Kabin</label>
                    <select name="prices[{{ $index }}][cabin_class]" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" required>
                        <option value="economy" {{ ($priceData['cabin_class'] ?? '') == 'economy' ? 'selected' : '' }}>Economy</option>
                        <option value="premium_economy" {{ ($priceData['cabin_class'] ?? '') == 'premium_economy' ? 'selected' : '' }}>Premium Economy</option>
                        <option value="business" {{ ($priceData['cabin_class'] ?? '') == 'business' ? 'selected' : '' }}>Business</option>
                        <option value="first" {{ ($priceData['cabin_class'] ?? '') == 'first' ? 'selected' : '' }}>First Class</option>
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Harga (Rp)</label>
                    <input type="number" name="prices[{{ $index }}][price]" value="{{ $priceData['price'] ?? '' }}" min="0" step="0.01" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" placeholder="e.g. 1500000" required>
                </div>
                <div class="flex-1 w-full">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kuota Kursi</label>
                    <input type="number" name="prices[{{ $index }}][quota]" value="{{ $priceData['quota'] ?? '' }}" min="1" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" placeholder="e.g. 150" required>
                </div>
                <div>
                    <button type="button" class="remove-price-btn inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            <button type="button" id="add-price-btn" class="inline-flex items-center text-sm font-medium text-skybook-primary hover:text-skybook-secondary">
                <svg class="mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Tambah Kelas Kabin
            </button>
        </div>
    </div>
</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
    <a href="{{ route('admin.schedules.index') }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
        Batal
    </a>
    <button type="submit" class="rounded-lg bg-skybook-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-skybook-primary/90 focus:ring-4 focus:ring-skybook-primary/30">
        Simpan Jadwal
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('pricing-container');
    const addButton = document.getElementById('add-price-btn');
    let index = {{ count($oldPrices) }};

    addButton.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'pricing-row flex flex-col md:flex-row gap-4 items-end bg-slate-50 p-4 rounded-lg border border-slate-200 relative mt-4';
        row.innerHTML = `
            <div class="flex-1 w-full">
                <label class="mb-1 block text-sm font-medium text-slate-700">Kelas Kabin</label>
                <select name="prices[${index}][cabin_class]" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" required>
                    <option value="economy">Economy</option>
                    <option value="premium_economy">Premium Economy</option>
                    <option value="business">Business</option>
                    <option value="first">First Class</option>
                </select>
            </div>
            <div class="flex-1 w-full">
                <label class="mb-1 block text-sm font-medium text-slate-700">Harga (Rp)</label>
                <input type="number" name="prices[${index}][price]" min="0" step="0.01" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" placeholder="e.g. 1500000" required>
            </div>
            <div class="flex-1 w-full">
                <label class="mb-1 block text-sm font-medium text-slate-700">Kuota Kursi</label>
                <input type="number" name="prices[${index}][quota]" min="1" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-skybook-primary focus:ring-skybook-primary" placeholder="e.g. 150" required>
            </div>
            <div>
                <button type="button" class="remove-price-btn inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        index++;
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-price-btn')) {
            const rows = container.querySelectorAll('.pricing-row');
            if (rows.length > 1) {
                e.target.closest('.pricing-row').remove();
            } else {
                alert('Minimal harus ada satu kelas kabin.');
            }
        }
    });
});
</script>
