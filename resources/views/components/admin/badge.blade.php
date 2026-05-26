@props(['type' => 'default'])

@php
    $colors = [
        'success' => 'bg-emerald-100 text-emerald-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info' => 'bg-sky-100 text-sky-800',
        'default' => 'bg-slate-100 text-slate-800',
        'primary' => 'bg-skybook-secondary/20 text-skybook-secondary',
    ];
    $colorClass = $colors[$type] ?? $colors['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium $colorClass"]) }}>
    {{ $slot }}
</span>