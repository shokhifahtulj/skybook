@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'rows' => 4,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}>{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}>
            {{ $slot }}
        </select>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}>
    @endif

    @error($name)
        <p class="text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>
