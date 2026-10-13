@extends('layouts.app')

@section('title', 'Kebijakan Perjalanan Dinas & Mesin Kepatuhan Otomatis')

@section('content')
<div class="space-y-6">
    <!-- Header with Breadcrumb & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-[#737780] mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-[#00254e] transition-colors">Dasbor</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#00254e] font-semibold">Kebijakan Perjalanan</span>
            </nav>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight">
                    Kebijakan Perjalanan Dinas
                </h1>
                <span class="px-2.5 py-0.5 rounded-full bg-[#00677e]/10 text-[#00677e] text-xs font-bold border border-[#00677e]/20 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    Mesin Kebijakan Dinamis (TravelSys Engine)
                </span>
            </div>
            <p class="text-xs sm:text-sm text-[#43474f] mt-0.5">
                Batas tarif otomatis, hak kelas kabin, pagu hotel dinamis, dan sistem reward penghematan biaya pegawai.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('budgets.index') }}" class="h-10 px-3.5 bg-white border border-[#c3c6d1]/60 hover:bg-[#f0f3ff] text-xs font-semibold text-[#111c2d] rounded-xl shadow-2xs flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">account_balance_wallet</span>
                <span>Anggaran Departemen</span>
            </a>
            <a href="{{ route('trips.index') }}" class="h-10 px-3.5 bg-[#00254e] hover:bg-[#001833] text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">business_center</span>
                <span>Daftar Perjalanan</span>
            </a>
        </div>
    </div>

    <!-- Feature Highlight Banner -->
    <div class="p-4 rounded-xl bg-linear-to-r from-[#00254e] to-[#004e60] text-white shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                <span class="material-symbols-outlined text-[24px] text-amber-300">loyalty</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold font-['Plus_Jakarta_Sans']">Sistem Reward Penghematan Biaya Pegawai Aktif</h3>
                    <span class="bg-emerald-500/20 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-400/30">
                        Pegawai Menerima 10% dari Penghematan
                    </span>
                </div>
                <p class="text-xs text-white/80 mt-0.5 max-w-2xl leading-relaxed">
                    Pegawai yang memesan tiket penerbangan atau hotel di bawah pagu batas harga dinamis perusahaan memperoleh poin insentif yang dapat ditukarkan untuk voucer belanja atau donasi.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="text-right">
                <div class="text-xs text-white/70">Penghematan Pegawai Q3</div>
                <div class="text-base font-bold text-amber-300">Rp 48.500.000</div>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Aturan Kebijakan Aktif</span>
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">verified_user</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $activeRules ?? count($policies) }} <span class="text-xs font-normal text-gray-500">/ {{ $totalRules ?? count($policies) }}</span>
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Kepatuhan Sistem 100%</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Golongan Karyawan (Band)</span>
                <span class="material-symbols-outlined text-[18px] text-[#00254e]">badge</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                5 Tingkatan
            </div>
            <div class="text-[11px] text-[#737780] font-semibold mt-1">
                Band 1 (Staf) hingga Band 5 (Direksi)
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Waktu Pemesanan Awal</span>
                <span class="material-symbols-outlined text-[18px] text-amber-500">calendar_month</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                14 Hari
            </div>
            <div class="text-[11px] text-amber-600 font-semibold mt-1">
                Wajib untuk Tarif Terbaik
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Blokir Otomatis Pelanggaran</span>
                <span class="material-symbols-outlined text-[18px] text-red-500">block</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                Aktif
            </div>
            <div class="text-[11px] text-red-600 font-semibold mt-1">
                Memerlukan Otorisasi Khusus Direksi
            </div>
        </div>
    </div>

    <!-- Policy Rules Catalog by Category -->
    <div class="bg-white rounded-xl border border-[#c3c6d1]/40 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-[#c3c6d1]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                    Matriks Aturan Kebijakan Terkonfigurasi
                </h2>
                <p class="text-xs text-[#737780] mt-0.5">
                    Mengatur validasi kepatuhan rencana perjalanan dinas dan perutean persetujuan bertingkat (waterfall).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Penegakan Real-time
                </span>
            </div>
        </div>

        <div class="divide-y divide-[#c3c6d1]/20">
            @forelse($policies as $policy)
                @php
                    $isFlight = $policy->rule_type === 'flight_limit';
                    $isHotel = $policy->rule_type === 'hotel_limit';
                    $isClass = $policy->rule_type === 'travel_class';
                    $isPerDiem = $policy->rule_type === 'per_diem';

                    if ($isFlight) {
                        $icon = 'flight';
                        $iconBg = 'bg-[#00677e]/10 text-[#00677e]';
                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                        $typeLabel = 'Batas Tiket Pesawat';
                    } elseif ($isHotel) {
                        $icon = 'hotel';
                        $iconBg = 'bg-amber-500/10 text-amber-600';
                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                        $typeLabel = 'Batas Hotel';
                    } elseif ($isClass) {
                        $icon = 'flight_class';
                        $iconBg = 'bg-purple-500/10 text-purple-600';
                        $badgeBg = 'bg-purple-50 text-purple-700 border-purple-200';
                        $typeLabel = 'Kelas Kabin';
                    } else {
                        $icon = 'payments';
                        $iconBg = 'bg-emerald-500/10 text-emerald-600';
                        $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        $typeLabel = 'Uang Harian (Per-Diem)';
                    }
                @endphp
                <div class="p-5 hover:bg-[#f8f9ff] transition-colors">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-[#111c2d]">{{ $policy->name }}</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeBg }}">
                                        {{ $typeLabel }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">
                                        {{ $policy->applicable_band }} &bull; {{ ucfirst($policy->scope) }}
                                    </span>
                                    @if($policy->is_active)
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold flex items-center gap-1">
                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-500 text-[10px] font-bold">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#43474f] mt-1 leading-relaxed">
                                    {{ $policy->description }}
                                </p>
                                <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-[#737780]">
                                    @if($policy->amount_limit > 0)
                                        <span><strong>Pagu:</strong> Rp {{ number_format($policy->amount_limit, 0, ',', '.') }}</span>
                                    @endif
                                    @if($policy->allowed_class)
                                        <span><strong>Standar Layanan:</strong> {{ $policy->allowed_class }}</span>
                                    @endif
                                    <span><strong>Cakupan:</strong> {{ ucfirst($policy->scope) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if($policy->amount_limit > 0)
                                <span class="text-base font-bold font-['Plus_Jakarta_Sans'] text-[#00254e]">Rp {{ number_format($policy->amount_limit, 0, ',', '.') }}</span>
                                <div class="text-[10px] text-[#737780]">Batas Maksimum</div>
                            @else
                                <span class="text-sm font-bold font-['Plus_Jakarta_Sans'] text-[#00254e]">{{ $policy->allowed_class ?? 'Sesuai Kebijakan' }}</span>
                                <div class="text-[10px] text-[#737780]">Standar Otorisasi</div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    Belum ada kebijakan perjalanan dinas yang terdaftar.
                </div>
            @endforelse
        </div>

        <!-- Footer Policy Guidelines -->
        <div class="p-4 bg-[#f0f3ff]/50 border-t border-[#c3c6d1]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-[#737780]">
            <div>
                Kebijakan disinkronkan dengan <strong>Pedoman SDM &amp; Keuangan Perusahaan TravelSys TA 2026/2027</strong>.
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] text-[#00677e]">sync</span>
                <span>Divalidasi otomatis pada setiap pengajuan perjalanan</span>
            </div>
        </div>
    </div>
</div>
@endsection
