<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f9f9ff]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Corporate Travel Management System') - {{ config('app.name', 'TravelSys') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS / Laravel Vite Directives -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full antialiased font-['Inter'] text-[#111c2d] bg-[#f9f9ff] flex flex-col min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Main Layout with Fixed Sidebar & Header -->
        <div class="flex flex-1">
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col lg:pl-64 transition-all duration-300">
                <!-- Top Header -->
                <header class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-[#c3c6d1]/30 shadow-[0_1px_8px_rgba(0,0,0,0.03)] flex items-center justify-between px-4 sm:px-6">
                    <!-- Left: Search & Mobile Hamburger -->
                    <div class="flex items-center gap-3 w-full max-w-md">
                        <button type="button" class="lg:hidden p-2 rounded-lg text-[#43474f] hover:bg-[#f0f3ff] transition-colors" onclick="document.getElementById('mobile-sidebar').classList.toggle('-translate-x-full')">
                            <span class="material-symbols-outlined text-[24px]">menu</span>
                        </button>

                        <form action="{{ route('trips.index') }}" method="GET" class="relative w-full">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#737780] text-[18px]">search</span>
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Traveler name, Trip ID (#TRV-...), or Destination"
                                class="w-full h-9 pl-9 pr-4 bg-[#f0f3ff] text-[#111c2d] placeholder:text-[#737780] text-xs rounded-lg outline-none border border-transparent focus:border-[#00677e] focus:bg-white transition-all"
                            />
                        </form>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-[#dfe8ff] rounded-full text-xs text-[#111c2d] font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#00677e]"></span>
                            Role: {{ auth()->user()->role ?? 'Travel Manager / Employee' }}
                        </span>

                        <!-- Notification Bell -->
                        <div class="relative">
                            <button class="relative p-2 text-[#43474f] hover:text-[#111c2d] hover:bg-[#f0f3ff] rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[22px]">notifications</span>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#ba1a1a] rounded-full ring-2 ring-white"></span>
                            </button>
                        </div>

                        <!-- User Profile Dropdown Trigger -->
                        <div class="flex items-center gap-2 pl-2 border-l border-[#c3c6d1]/40">
                            <div class="w-8 h-8 rounded-full bg-[#00254e] text-white flex items-center justify-center font-bold text-xs">
                                {{ substr(auth()->user()->name ?? 'Felix Gonelius', 0, 2) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-semibold text-[#111c2d]">{{ auth()->user()->name ?? 'Felix Gonelius' }}</div>
                                <div class="text-[10px] text-[#737780]">{{ auth()->user()->department ?? 'Technology' }}</div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content Injected Here -->
                <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto">
                    <!-- Session Status Alerts -->
                    @if (session('success'))
                        <div class="mb-4 p-4 rounded-lg bg-[#e8f5e9] border border-[#81c784] text-[#1b5e20] text-xs font-medium flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 rounded-lg bg-[#ffebee] border border-[#e57373] text-[#b71c1c] text-xs font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="mt-auto border-t border-[#c3c6d1]/20 bg-white px-6 py-4 text-center text-xs text-[#737780]">
                    &copy; {{ date('Y') }} TravelSys Corporate Mobility. Enterprise Travel Policy Enforcement & Clearance System.
                </footer>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
