@props(['name', 'label', 'required' => false, 'value' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="mb-2.5 block text-sm font-semibold text-slate-700">
        {{ $label }} @if($required) <span class="text-rose-500">*</span> @endif
    </label>

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}>
        {{ $slot }}
    </select>

    @error($name)
        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>
