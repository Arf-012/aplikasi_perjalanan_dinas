<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f9f9ff]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem Manajemen Perjalanan Dinas') - {{ config('app.name', 'TravelSys') }}</title>

    <!-- Brand Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Artwork.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS / Laravel Vite Directives -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00254e',
                        secondary: '#00677e',
                    }
                }
            }
        }
    </script>
    <style>
        /* Grid system fallbacks for guaranteed layout stability */
        .grid { display: grid !important; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        .grid-cols-6 { grid-template-columns: repeat(6, minmax(0, 1fr)) !important; }
        .grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }

        @media (min-width: 640px) {
            .sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .sm\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
            .sm\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }

        @media (min-width: 768px) {
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
            .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }

        @media (min-width: 1024px) {
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
            .lg\:grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
            .lg\:col-span-7 { grid-column: span 7 / span 7 !important; }
            .lg\:col-span-5 { grid-column: span 5 / span 5 !important; }
            .lg\:col-span-8 { grid-column: span 8 / span 8 !important; }
            .lg\:col-span-4 { grid-column: span 4 / span 4 !important; }
        }

        /* Prevent unwanted flex/grid child collapse */
        .grid > * { min-width: 0; }
    </style>
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
                        <button type="button" class="lg:hidden p-2 rounded-lg text-[#43474f] hover:bg-[#f0f3ff] transition-colors" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                            <span class="material-symbols-outlined text-[24px]">menu</span>
                        </button>

                        <form action="{{ route('trips.index') }}" method="GET" class="relative w-full">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#737780] text-[18px]">search</span>
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Cari nama karyawan, ID perjalanan (#TRV-...), atau destinasi..."
                                class="w-full h-9 pl-9 pr-4 bg-[#f0f3ff] text-[#111c2d] placeholder:text-[#737780] text-xs rounded-lg outline-none border border-transparent focus:border-[#00677e] focus:bg-white transition-all"
                            />
                        </form>
                    </div>

                    <!-- Right Controls -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-[#f0f3ff] rounded-full text-xs text-[#00254e] font-semibold border border-[#c3c6d1]/30">
                            <span class="w-2 h-2 rounded-full bg-[#00677e]"></span>
                            <span>Peran: {{ auth()->user()->role_display_name }}</span>
                        </span>

                        <!-- Notification Bell & Role-Based Dropdown -->
                        <div class="relative inline-block" id="notifications-dropdown-container">
                            <button
                                type="button"
                                id="notifications-bell-btn"
                                onclick="document.getElementById('notifications-dropdown-menu').classList.toggle('hidden')"
                                class="relative flex items-center justify-center rounded-xl text-[#43474f] hover:text-[#111c2d] hover:bg-[#f0f3ff] transition-all cursor-pointer"
                                style="width: 36px; height: 36px;"
                                title="Notifikasi"
                                aria-label="Notifikasi"
                            >
                                <span class="material-symbols-outlined text-[22px]">notifications</span>
                                @if(($unreadNotificationsCount ?? 0) > 0)
                                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#ba1a1a] rounded-full ring-2 ring-white"></span>
                                @endif
                            </button>

                            <!-- Dropdown Menu (Solid & Spacious Dimensions: 420px width, 480px height) -->
                            <div
                                id="notifications-dropdown-menu"
                                class="hidden absolute right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-[#c3c6d1]/50 flex flex-col z-50 overflow-hidden"
                                style="width: 420px; max-width: calc(100vw - 1.5rem); height: 480px;"
                            >
                                <!-- Dropdown Header (Spacious 60px height) -->
                                <div class="shrink-0 border-b border-[#c3c6d1]/20 flex items-center justify-between bg-white" style="height: 60px; padding: 0 20px;">
                                    <div class="flex items-center gap-2.5">
                                        <span class="font-['Plus_Jakarta_Sans'] font-bold text-[15px] text-[#00254e]">Notifikasi</span>
                                        @if(($unreadNotificationsCount ?? 0) > 0)
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#ba1a1a]/10 text-[#ba1a1a] whitespace-nowrap">
                                                {{ $unreadNotificationsCount }} baru
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 whitespace-nowrap">
                                                Semua dibaca
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wide uppercase whitespace-nowrap {{ auth()->user()->canAccessApprovals() ? 'bg-[#00254e] text-white' : 'bg-[#e0f2fe] text-[#0369a1]' }}">
                                            {{ auth()->user()->canAccessApprovals() ? 'Penyetuju' : 'Karyawan' }}
                                        </span>
                                        @if(($unreadNotificationsCount ?? 0) > 0)
                                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-[#00677e] hover:underline whitespace-nowrap cursor-pointer">
                                                    Tandai semua dibaca
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- Notifications List (Fixed Scrollable Body with Generous Padding) -->
                                <div class="flex-1 overflow-y-auto divide-y divide-[#c3c6d1]/15 bg-white">
                                    @forelse($userNotifications ?? [] as $notification)
                                        @php
                                            $data = $notification->data;
                                            $isUnread = is_null($notification->read_at);
                                        @endphp
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="block m-0 p-0">
                                            @csrf
                                            <button type="submit" class="w-full text-left hover:bg-[#f0f3ff]/70 transition-all flex items-start cursor-pointer {{ $isUnread ? 'bg-[#f0f3ff]/35' : '' }}" style="padding: 16px 20px; gap: 14px;">
                                                <div class="rounded-xl flex items-center justify-center shrink-0 {{ $data['icon_color'] ?? 'text-[#00677e] bg-[#f0f3ff]' }}" style="width: 40px; height: 40px;">
                                                    <span class="material-symbols-outlined" style="font-size: 21px;">{{ $data['icon'] ?? 'notifications' }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="text-[13px] font-bold truncate {{ $isUnread ? 'text-[#00254e]' : 'text-[#333740]' }}">
                                                            {{ $data['title'] ?? 'Notification' }}
                                                        </span>
                                                        <span class="text-[11px] text-[#737780] shrink-0 font-medium whitespace-nowrap">
                                                            {{ $notification->created_at->diffForHumans(null, true, true) }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[12px] text-[#43474f] line-clamp-2 mt-1 leading-relaxed">
                                                        {{ $data['message'] ?? '' }}
                                                    </p>
                                                    <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                                                        @if(isset($data['request_code']))
                                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-[#f0f3ff] text-[#00254e] border border-[#d2e0ff]">
                                                                #{{ $data['request_code'] }}
                                                            </span>
                                                        @endif
                                                        @if(isset($data['amount']) && $data['amount'] > 0)
                                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-[#e8f5e9] text-[#2e7d32] border border-[#c8e6c9]">
                                                                Rp {{ number_format($data['amount'], 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($isUnread)
                                                    <span style="width: 9px; height: 9px; border-radius: 9999px; background-color: #00677e; box-shadow: 0 0 0 3px rgba(0, 103, 126, 0.18); margin-top: 6px; flex-shrink: 0;" title="Unread"></span>
                                                @endif
                                            </button>
                                        </form>
                                    @empty
                                        <div class="h-full flex flex-col items-center justify-center p-8 text-center">
                                            <div class="w-14 h-14 rounded-2xl bg-[#f0f3ff] flex items-center justify-center text-[#737780] mb-3">
                                                <span class="material-symbols-outlined text-[32px] text-[#c3c6d1]">notifications_off</span>
                                            </div>
                                            <p class="text-sm font-bold text-[#111c2d]">Belum ada notifikasi</p>
                                            <p class="text-xs text-[#737780] mt-1 max-w-[240px] leading-relaxed">
                                                Semua pembaruan pengajuan perjalanan dan otorisasi telah terpantau.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Dropdown Footer (Spacious 44px height) -->
                                <div class="shrink-0 border-t border-[#c3c6d1]/20 bg-[#f9f9ff] flex items-center justify-between text-xs text-[#737780]" style="height: 44px; padding: 0 20px;">
                                    <span class="flex items-center gap-2">
                                        <span style="width: 7px; height: 7px; border-radius: 9999px;" class="{{ ($unreadNotificationsCount ?? 0) > 0 ? 'bg-[#ba1a1a]' : 'bg-emerald-500' }}"></span>
                                        <span class="font-medium">{{ $unreadNotificationsCount ?? 0 }} belum dibaca &bull; {{ $totalNotificationsCount ?? count($userNotifications ?? []) }} total</span>
                                    </span>
                                    <a href="{{ route('trips.index') }}" class="font-semibold text-[#00254e] hover:underline flex items-center gap-1.5 transition-colors">
                                        <span>Ruang Kerja Perjalanan</span>
                                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pl-2 border-l border-[#c3c6d1]/40">
                            <div class="w-8 h-8 rounded-full bg-[#00254e] text-white flex items-center justify-center font-bold text-xs">
                                {{ substr(auth()->user()->name ?? 'Andi Pratama', 0, 2) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-semibold text-[#111c2d]">{{ auth()->user()->name ?? 'Andi Pratama' }}</div>
                                <div class="text-[10px] text-[#737780]">{{ auth()->user()->department ?? 'Engineering' }}</div>
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
                    &copy; {{ date('Y') }} TravelSys &bull; Sistem Manajemen Perjalanan Dinas &bull; SMK Plus Pelita Nusantara &bull; Kelompok 6 RPL
                </footer>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notifications-dropdown-container');
            const menu = document.getElementById('notifications-dropdown-menu');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
