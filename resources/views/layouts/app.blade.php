<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SkyBook') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="font-sans antialiased bg-skybook-accent text-slate-800">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        <x-admin.sidebar type="user" />

        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <x-admin.navbar :home-route="route('dashboard')" role-label="Passenger" />

            @if (session('success'))
                <script>
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: "{{ session('error') }}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                </script>
            @endif

            @isset($header)
                <div class="border-b border-slate-200 bg-white">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <main class="w-full grow p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>