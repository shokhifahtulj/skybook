@props(['type' => 'admin'])

<!-- Sidebar Backdrop -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-slate-900/80 lg:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

<!-- Sidebar -->
<aside class="absolute left-0 top-0 z-50 flex h-screen w-72 flex-col overflow-y-hidden bg-skybook-primary text-white duration-300 ease-linear lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5 mt-4">
        <a href="{{ $type === 'user' ? route('dashboard') : route('admin.dashboard') }}" class="flex items-center gap-3">
            <svg class="h-8 w-8 text-skybook-secondary" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </svg>
            <span class="text-2xl font-bold text-white tracking-wide">SkyBook<span class="text-skybook-secondary">{{ $type === 'user' ? 'Passenger' : 'Admin' }}</span></span>
        </a>

        <button class="block lg:hidden text-slate-300 hover:text-white" @click.stop="sidebarOpen = false">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mt-5 py-4 px-4 lg:mt-9 lg:px-6">
            @if($type === 'admin')
                <div>
                    <h3 class="mb-4 ml-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">MENU UTAMA</h3>
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.dashboard') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.flights.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.flights.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Penerbangan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.schedules.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.schedules.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Jadwal Penerbangan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.users.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                Pengguna
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 ml-4 mt-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">INTELLIGENCE</h3>
                    <ul class="flex flex-col gap-1.5">
                        <li>
                            <a href="{{ route('admin.analytics.executive') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.analytics.*') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span class="truncate">Executive Analytics</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 ml-4 mt-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">FLIGHT OPERATIONS</h3>
                    <ul class="flex flex-col gap-1.5">
                        <li>
                            <a href="{{ route('admin.operations.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.*') && !request()->routeIs('admin.operations.baggage.*') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="truncate">Command Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.operations.baggage.drop') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.baggage.*') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <span class="truncate">Baggage Drop</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.operations.gates') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.gates') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="truncate">Gate Management</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.operations.reaccommodation') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.reaccommodation') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span class="truncate">Reaccommodation</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.operations.simulation') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.simulation') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                <span class="truncate">IROPS Simulator</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.operations.engineering') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.operations.engineering') ? 'bg-skybook-primary text-white shadow-lg shadow-skybook-primary/30' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="truncate">M&E Control</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 ml-4 mt-6 text-xs font-semibold text-slate-400 uppercase tracking-wider">MASTER DATA</h3>
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <li><a href="{{ route('admin.airlines.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.airlines.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">... </a></li>
                        <li><a href="{{ route('admin.airports.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.airports.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">... </a></li>
                        <li><a href="{{ route('admin.aircrafts.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.aircrafts.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">... </a></li>
                        <li><a href="{{ route('admin.routes.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('admin.routes.*') ? 'bg-skybook-secondary text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">... </a></li>
                    </ul>
                </div>
            @else
                <div>
                    <h3 class="mb-4 ml-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">TRAVEL HUB</h3>
                    <ul class="mb-6 flex flex-col gap-1.5">
                        <li><a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>Dashboard</a></li>
                        <li><a href="{{ route('flights.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('flights.*') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Search Flights</a></li>
                        <li><a href="{{ route('bookings.create') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('bookings.create') || request()->routeIs('bookings.passengers') || request()->routeIs('bookings.confirm') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" /></svg>Book Flight</a></li>
                        <li><a href="{{ route('bookings.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('bookings.index') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6m-6 4h6m-6 4h6m-6 4h6" /></svg>My Bookings</a></li>
                        <li><a href="{{ route('notifications.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('notifications.*') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>Notifications</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('profile.*') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>Profile</a></li>
                        <li><a href="{{ route('manage-booking.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('manage-booking.*') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17h2m-1-4V9m0-4h.01" /></svg>Manage Booking</a></li>
                        <li><a href="{{ route('checkin.index') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out {{ request()->routeIs('checkin.*') ? 'bg-skybook-secondary text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>Check-in</a></li>
                        @can('isAdmin')
                            <li><a href="{{ route('admin.dashboard') }}" class="group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium duration-300 ease-in-out text-slate-200 hover:bg-slate-800 hover:text-white"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>Admin Console</a></li>
                        @endcan
                    </ul>
                </div>
            @endif
        </nav>
    </div>
</aside>
