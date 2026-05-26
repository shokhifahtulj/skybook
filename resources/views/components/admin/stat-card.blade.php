@props(['title', 'value', 'icon' => null, 'trend' => null])

<div class="flex items-center justify-between rounded-[24px] border border-slate-200 bg-white px-6 py-5 shadow-[0_2px_10px_rgba(0,0,0,0.05)]">
    <div>
        <div class="mb-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $title }}</div>
        <div class="text-3xl font-bold text-slate-900">{{ $value }}</div>
        @if($trend)
            <div class="mt-2 text-sm font-medium {{ str_starts_with($trend, '+') ? 'text-emerald-600' : 'text-rose-500' }}">
                {{ $trend }}
            </div>
        @endif
    </div>

    @if($icon)
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-skybook-accent text-skybook-secondary">
            {!! $icon !!}
        </div>
    @endif
</div>
