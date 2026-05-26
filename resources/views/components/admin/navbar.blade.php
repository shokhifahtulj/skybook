@props(['roleLabel' => 'Admin', 'homeRoute' => route('admin.dashboard')])

<header class="sticky top-0 z-30 flex w-full bg-white shadow-sm drop-shadow-sm border-b border-slate-200">
    <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">

        <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
            <button class="z-50 block rounded-sm border border-slate-200 bg-white p-1.5 shadow-sm lg:hidden" @click.stop="sidebarOpen = !sidebarOpen">
                <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a class="block flex-shrink-0 lg:hidden text-skybook-primary font-bold text-xl" href="{{ $homeRoute }}">
                SkyBook
            </a>
        </div>

        <div class="hidden sm:block">
            <div class="text-sm font-semibold text-slate-500">{{ $roleLabel }} workspace</div>
        </div>

        <div class="flex items-center gap-3 2xsm:gap-7">
            <div x-data="{ dropdownOpen: false }" class="relative" @click.outside="dropdownOpen = false">
                <a class="flex items-center gap-4 cursor-pointer" @click.prevent="dropdownOpen = !dropdownOpen">
                    <span class="hidden text-right lg:block">
                        <span class="block text-sm font-medium text-slate-800">{{ auth()->user()?->name ?? 'Administrator' }}</span>
                        <span class="block text-xs text-slate-500">{{ $roleLabel }}</span>
                    </span>
                    <span class="h-10 w-10 rounded-full bg-skybook-secondary flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                    </span>
                    <svg class="hidden fill-current sm:block text-slate-500" :class="dropdownOpen ? 'rotate-180' : ''" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z" fill=""/>
                    </svg>
                </a>

                <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-4 flex w-62.5 flex-col rounded-xl border border-slate-200 bg-white shadow-lg z-50">
                    <ul class="flex flex-col gap-5 border-b border-slate-200 px-6 py-5">
                        <li>
                            <a href="{{ $homeRoute }}" class="flex items-center gap-3.5 text-sm font-medium duration-300 ease-in-out hover:text-skybook-secondary text-slate-700">
                                {{ $roleLabel === 'Passenger' ? 'Go to dashboard' : 'Kembali ke Website' }}
                            </a>
                        </li>
                    </ul>
                    <form method="POST" action="{{ route('logout') }}" class="p-4">
                        @csrf
                        <button class="flex items-center gap-3.5 px-2 text-sm font-medium duration-300 ease-in-out hover:text-red-500 text-slate-700 w-full text-left">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
