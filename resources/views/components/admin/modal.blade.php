@props(['id', 'title'])

<div x-data="{ show: false }" x-show="show" @open-modal.window="if ($event.detail === '{{ $id }}') show = true" @close-modal.window="if ($event.detail === '{{ $id }}') show = false" @keydown.escape.window="show = false" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/50 transition-opacity"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <!-- Modal Panel -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title">{{ $title }}</h3>
                        <div class="mt-4">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
            
            @isset($footer)
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>