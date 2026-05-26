@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<x-admin.input
    :name="$name"
    :label="$label"
    :type="$type"
    :value="$value"
    :required="$required"
    :placeholder="$placeholder"
    {{ $attributes }}
/>
