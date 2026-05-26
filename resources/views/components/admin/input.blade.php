@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="mb-2.5 block text-sm font-semibold text-slate-700">
        {{ $label }} @if($required) <span class="text-rose-500">*</span> @endif
    </label>

    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }}>{{ old($name, $value) }}</textarea>
    @elseif($type === 'file')
        <input type="file" name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-[12px] border border-slate-300 bg-white px-4 py-2 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }} />
        @if($value)
            <div class="mt-2 text-sm text-slate-500">File saat ini: {{ $value }}</div>
        @endif
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'h-[52px] w-full rounded-[12px] border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-skybook-secondary focus:ring-2 focus:ring-skybook-secondary/20']) }} />
    @endif

    @error($name)
        <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>