<aside id="sidebar" class="fixed top-0 bottom-0 left-0 w-64 bg-white border-r border-[#c3c6d1]/30 shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
    <div class="flex flex-col">
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-[#c3c6d1]/20">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-left">
                <img src="{{ asset('images/Artwork.png') }}" alt="TravelSys Logo" class="h-6 w-auto object-contain shrink-0">
                <span class="font-['Plus_Jakarta_Sans'] font-extrabold text-[15px] tracking-wider text-[#00254e]">TRAVELSYS</span>
                <span class="text-[10px] font-semibold text-[#737780] bg-[#f0f3ff] px-1.5 py-0.5 rounded border border-[#c3c6d1]/40 ml-1">v2.4</span>
            </a>

            <button type="button" class="lg:hidden text-[#737780] hover:text-[#111c2d]" onclick="document.getElementById('sidebar').classList.add('-translate-x-full')">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Section Label -->
        <div class="pt-5 pb-2 px-5 text-[10px] font-bold uppercase tracking-wider text-[#737780]">
            OPERASIONAL &amp; KONTROL
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 space-y-1">
            <!-- Beranda / Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#737780]' }}">grid_view</span>
                <span>Beranda / Dashboard</span>
            </a>

            <!-- Pengajuan Perjalanan -->
            <a href="{{ route('trips.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('trips.create') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('trips.create') ? 'text-white' : 'text-[#737780]' }}">flight_takeoff</span>
                <span>Pengajuan Perjalanan</span>
            </a>

            <!-- Persetujuan: Hanya untuk peran yang berwenang (Manager, Finance, Travel Admin, Direktur) -->
            @if (auth()->check() && auth()->user()->canAccessApprovals())
                <a href="{{ route('approvals.index') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('approvals.*') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">

                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('approvals.*') ? 'text-white' : 'text-[#737780]' }}">check_circle</span>
                        <span>Persetujuan</span>
                    </div>

                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#00677e] text-white">
                        {{ $pendingApprovalsCount ?? 0 }}
                    </span>
                </a>
            @endif

            <!-- Ruang Kerja Perjalanan -->
            <a href="{{ route('trips.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('trips.index') || request()->routeIs('trips.show') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('trips.index') || request()->routeIs('trips.show') ? 'text-white' : 'text-[#737780]' }}">business_center</span>
                <span>Ruang Kerja Perjalanan</span>
            </a>

            <!-- Kebijakan Perjalanan -->
            <a href="{{ route('policies.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('policies.*') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('policies.*') ? 'text-white' : 'text-[#737780]' }}">verified_user</span>
                <span>Kebijakan Perjalanan</span>
            </a>

            <!-- Anggaran Departemen -->
            <a href="{{ route('budgets.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('budgets.*') ? 'bg-[#00254e] text-white shadow-xs' : 'text-[#43474f] hover:bg-[#f0f3ff] hover:text-[#111c2d]' }}">
                <span class="material-symbols-outlined text-[18px] {{ request()->routeIs('budgets.*') ? 'text-white' : 'text-[#737780]' }}">receipt_long</span>
                <span>Anggaran Departemen</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Widget: Audit Log & Sign Out -->
    <div class="p-3 space-y-2">
        <div class="p-3 rounded-xl bg-[#f0f3ff]/80 border border-[#c3c6d1]/40">
            <div class="flex items-center justify-between">
                <span class="text-[9px] font-bold uppercase tracking-wider text-[#737780]">LOG AUDIT</span>
                <span class="w-1.5 h-1.5 rounded-full bg-[#00677e]"></span>
            </div>
            <div class="text-[11px] font-bold text-[#00254e] mt-1">Kuota Perjalanan Q3: 82% terpakai</div>
        </div>

        <form method="POST" action="{{ route('logout') ?? '#' }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-1.5 text-[11px] font-medium text-[#737780] hover:text-[#ba1a1a] hover:bg-[#ffebee] rounded-lg transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
