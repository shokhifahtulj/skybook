@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-[12px] px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2';
    $variants = [
        'primary' => 'bg-skybook-secondary text-white hover:bg-skybook-primary focus:ring-skybook-secondary/30',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 focus:ring-slate-200',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-200',
        'ghost' => 'text-skybook-secondary hover:bg-skybook-accent focus:ring-skybook-secondary/20',
    ];
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
