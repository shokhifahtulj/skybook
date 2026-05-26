@props(['status' => 'default', 'label' => null])

@php
    $statusMap = [
        'active' => 'success',
        'inactive' => 'danger',
        'scheduled' => 'info',
        'boarding' => 'warning',
        'departed' => 'primary',
        'arrived' => 'success',
        'cancelled' => 'danger',
        'delayed' => 'warning',
    ];

    $type = $statusMap[$status] ?? 'default';
    $display = $label ?? ucfirst((string) $status);
@endphp

<x-admin.badge :type="$type">{{ $display }}</x-admin.badge>
