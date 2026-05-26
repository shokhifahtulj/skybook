@props([
    'title' => null,
    'description' => null,
    'action' => null,
])

<div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
    <div class="max-w-2xl space-y-2">
        @if($title)
            <h1 class="skybook-page-title">{{ $title }}</h1>
        @endif
        @if($description)
            <p class="skybook-page-subtitle">{{ $description }}</p>
        @endif
    </div>

    @if($action)
        <div class="w-full lg:max-w-sm">
            {{ $action }}
        </div>
    @endif
</div>
