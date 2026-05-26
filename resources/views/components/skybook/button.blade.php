@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
$base = 'inline-flex items-center justify-center gap-2 rounded-[16px] px-5 py-3 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

$variants = [
    'primary' => 'bg-skybook-secondary text-white hover:bg-skybook-secondary/90 focus:ring-skybook-secondary/30',
    'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus:ring-slate-200',
    'danger' => 'bg-rose-500 text-white hover:bg-rose-600 focus:ring-rose-200',
    'ghost' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-200',
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $base.' '.$variants[$variant] ]) }}>
    {{ $slot }}
</button>
