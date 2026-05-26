@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-[24px] border border-slate-200 bg-white p-6 shadow-[0_2px_10px_rgba(0,0,0,0.05)] sm:p-8']) }}>
    @if($title)
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-[24px] font-bold text-slate-900">{{ $title }}</h3>
                @if($description)
                    <p class="text-sm text-slate-500">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</div>