@props(['name', 'label', 'value' => '', 'required' => false, 'placeholder' => ''])

<x-admin.input
    type="textarea"
    :name="$name"
    :label="$label"
    :value="$value"
    :required="$required"
    :placeholder="$placeholder"
    {{ $attributes }}
/>
