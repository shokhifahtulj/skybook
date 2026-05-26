<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SkyBook') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="font-sans antialiased bg-skybook-accent text-slate-800">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Component -->
        <x-admin.sidebar />

        <!-- Main Content -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <!-- Navbar Component -->
            <x-admin.navbar />

            <!-- Content Area -->
            <main class="w-full grow p-6">
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

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
