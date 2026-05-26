@props([
    'name',
    'value' => '',
    'placeholder' => '',
    'type' => 'text',
])

<input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $name }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-[12px] border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}
/>
