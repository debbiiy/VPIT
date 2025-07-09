<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'VPIT System') }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=figtree:500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">

        <!-- Navbar -->
        <div class="flex items-center justify-between bg-white dark:bg-gray-800 px-10 py-5 shadow-md border-b border-gray-200 dark:border-gray-700">
            <!-- Logo + Menu -->
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity duration-200">
                    <img src="{{ asset('images/samuderaaa.png') }}" alt="Logo" class="h-12 w-auto object-contain" style="max-width: 48px;">
                    <span class="text-2xl font-bold text-gray-800 dark:text-white">VendorPortal</span>
                </a>
                <!-- Menu with underline active -->
                <div class="flex gap-10">
                    <div class="flex flex-col items-center">
                        <a href="{{ route('home') }}"
                        class="text-lg font-semibold {{ Route::is('home') ? 'text-blue-600' : 'text-gray-700 dark:text-white hover:text-blue-600' }} transition-colors duration-200">
                            Home
                        </a>
                        @if(Route::is('home'))
                            <div class="mt-1 w-6 h-1 bg-blue-600 rounded"></div>
                        @endif
                    </div>
                    @if(auth()->user()->role === 'admin')
                        <div class="flex flex-col items-center">
                            <a href="{{ route('dashboard-admin') }}"
                               class="text-lg font-semibold {{ Route::is('dashboard-admin') ? 'text-blue-600' : 'text-gray-700 dark:text-white hover:text-blue-600' }} transition-colors duration-200">
                                Document
                            </a>
                            @if(Route::is('dashboard-admin'))
                                <div class="mt-1 w-6 h-1 bg-blue-600 rounded"></div>
                            @endif
                        </div>

                        <div class="flex flex-col items-center">
                            <a href="{{ route('admin.fin.index') }}"
                               class="text-lg font-semibold {{ Route::is('admin.fin.index') ? 'text-blue-600' : 'text-gray-700 dark:text-white hover:text-blue-600' }} transition-colors duration-200">
                                Finance
                            </a>
                            @if(Route::is('admin.fin.index'))
                                <div class="mt-1 w-6 h-1 bg-blue-600 rounded"></div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center">
                            <a href="{{ route('user.dashboard') }}"
                               class="text-lg font-semibold {{ Route::is('user.dashboard') ? 'text-blue-600' : 'text-gray-700 dark:text-white hover:text-blue-600' }} transition-colors duration-200">
                                Document
                            </a>
                            @if(Route::is('user.dashboard'))
                                <div class="mt-1 w-6 h-1 bg-blue-600 rounded"></div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center">
                            <a href="{{ route('user.fin.index') }}"
                               class="text-lg font-semibold {{ Route::is('user.fin.index') ? 'text-blue-600' : 'text-gray-700 dark:text-white hover:text-blue-600' }} transition-colors duration-200">
                                Finance
                            </a>
                            @if(Route::is('user.fin.index'))
                                <div class="mt-1 w-6 h-1 bg-blue-600 rounded"></div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Profile Icon + Username -->
            <div class="relative z-[999]">
                <div class="flex items-center gap-2">
                    <span class="text-md font-semibold text-gray-700 dark:text-white">{{ Auth::user()->name }}</span>
                    <button onclick="toggleProfileDropdown()" class="focus:outline-none">
                        <i class="fas fa-user-circle text-3xl text-gray-700 dark:text-white hover:text-blue-600 transition-colors duration-200"></i>
                    </button>
                </div>

                <!-- Dropdown -->
                <div id="profileMenu" class="hidden absolute right-0 top-full mt-2 w-52 bg-white dark:bg-gray-700 rounded shadow-lg z-[9999]">
                    <a href="{{ route('profile.edit') }}"
                       class="block px-5 py-3 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-5 py-3 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="py-4 px-6">
            @yield('content')
        </main>
    </div>

    <!-- Profile Dropdown Script -->
    <script>
        function toggleProfileDropdown() {
            const menu = document.getElementById('profileMenu');
            menu.classList.toggle('hidden');
        }

        window.addEventListener('click', function (e) {
            if (!e.target.closest('#profileMenu') && !e.target.closest('button[onclick="toggleProfileDropdown()"]')) {
                document.getElementById('profileMenu')?.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
