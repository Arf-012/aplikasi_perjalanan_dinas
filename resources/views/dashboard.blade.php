@extends('layouts.app')

@section('title', 'Dasbor Perjalanan Dinas — TravelSys')

@section('content')
<div class="space-y-6">
    <!-- Top Greeting & Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-[11px] font-bold text-[#737780] uppercase tracking-wider">
                PORTAL PERUSAHAAN &bull; {{ optional(auth()->user())->department ? 'DIVISI ' . strtoupper(auth()->user()->department) : 'DIVISI REKAYASA & TEKNOLOGI' }}
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight mt-1">
                Selamat datang, {{ explode(' ', auth()->user()->name ?? 'Andi')[0] }}
            </h1>
            <p class="text-xs sm:text-sm text-[#43474f] mt-0.5">
                Sistem otorisasi perjalanan dinas, kepatuhan pagu kebijakan anggaran, dan pelaporan biaya riil terpadu.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('trips.index') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#111c2d] hover:bg-[#f0f3ff] transition-all shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-[#737780]">history</span>
                <span>Daftar Perjalanan</span>
            </a>
            <a href="{{ route('trips.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#00254e] text-white text-xs font-bold hover:bg-[#00346e] transition-all shadow-xs cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">add</span>
                <span>Ajukan Perjalanan</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Active Trips -->
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">TOTAL PERJALANAN</span>
                    <span class="material-symbols-outlined text-[18px] text-[#00677e]">flight_takeoff</span>
                </div>
                <div class="text-2xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d] mt-2">
                    {{ $totalTripsCount ?? count($recentTrips) }}
                </div>
                <div class="text-xs text-[#43474f] mt-1 truncate font-medium">
                    Tercatat di sistem
                </div>
            </div>
            <div class="pt-3 border-t border-[#c3c6d1]/20 mt-3 flex items-center justify-between text-[11px]">
                <span class="text-[#737780]">Aktif Saat Ini</span>
                <span class="font-bold text-[#00254e]">{{ $activeTripsCount ?? 1 }} Pengajuan</span>
            </div>
        </div>

        <!-- Card 2: Pending Approvals -->
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">MENUNGGU PERSETUJUAN</span>
                    <span class="material-symbols-outlined text-[18px] text-amber-500">pending_actions</span>
                </div>
                <div class="text-2xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d] mt-2">
                    {{ $pendingApprovalsCount ?? 0 }}
                </div>
                <div class="text-xs text-[#43474f] mt-1 font-medium">
                    Menunggu Otorisasi Waterfall
                </div>
            </div>
            <div class="pt-3 border-t border-[#c3c6d1]/20 mt-3 flex items-center justify-between text-[11px]">
                <span class="text-[#737780]">Status Alur</span>
                <span class="font-bold text-amber-600">SLA Berjalan</span>
            </div>
        </div>

        <!-- Card 3: Remaining Budget -->
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">REALISASI ANGGARAN</span>
                    <span class="material-symbols-outlined text-[18px] text-[#00254e]">payments</span>
                </div>
                <div class="text-xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d] mt-2 truncate">
                    Rp {{ number_format($totalCommittedCost ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs text-[#43474f] mt-1 font-medium">
                    Total Dana Dinas Teralokasi
                </div>
            </div>
            <div class="pt-3 border-t border-[#c3c6d1]/20 mt-3 flex items-center justify-between text-[11px]">
                <span class="text-[#737780]">Pusat Biaya</span>
                <span class="font-bold text-[#00254e]">Tervalidasi Database</span>
            </div>
        </div>

        <!-- Card 4: Policy Compliance -->
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">KEPATUHAN KEBIJAKAN</span>
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                </div>
                <div class="text-2xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d] mt-2">
                    {{ $policyComplianceRate ? number_format($policyComplianceRate, 0) . '%' : '100%' }}
                </div>
                <div class="text-xs text-emerald-700 font-medium mt-1 flex items-center gap-1">
                    <span>Sesuai Batas Kebijakan</span>
                </div>
            </div>
            <div class="pt-3 border-t border-[#c3c6d1]/20 mt-3 flex items-center justify-between text-[11px]">
                <span class="text-[#737780]">Validasi Sistem</span>
                <span class="font-bold text-emerald-700">100% Otomatis</span>
            </div>
        </div>
    </div>

    <!-- Spotlight Trip Card (Dinamic from DB) -->
    @if(isset($spotlightTrip) && $spotlightTrip)
    <div class="bg-white rounded-2xl border border-[#c3c6d1]/40 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#c3c6d1]/20">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#00677e]">fact_check</span>
                <h2 class="text-sm font-bold text-[#00254e]">Sorotan Pengajuan Perjalanan Terkini</h2>
            </div>
            <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-[#f0f3ff] text-[#00254e] border border-[#c3c6d1]/30">
                #{{ $spotlightTrip->request_code ?? $spotlightTrip->id }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="text-lg font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                    {{ $spotlightTrip->origin }} &rarr; {{ $spotlightTrip->destination }}
                </div>
                <div class="text-xs text-[#737780] mt-1">
                    {{ \Carbon\Carbon::parse($spotlightTrip->departure_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($spotlightTrip->return_date)->translatedFormat('d M Y') }} &bull;
                    <span class="text-[#111c2d] font-semibold">{{ $spotlightTrip->purpose_title ?? $spotlightTrip->purpose }}</span>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <div class="text-xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e]">
                    Rp {{ number_format($spotlightTrip->total_cost, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-[#737780]">Pusat Biaya: {{ $spotlightTrip->cost_center ?? 'CC-402' }}</div>
            </div>
        </div>

        <!-- Waterfall Status Tracker -->
        <div class="p-3.5 rounded-xl bg-[#f0f3ff]/70 border border-[#c3c6d1]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">account_tree</span>
                <div>
                    <span class="text-[#737780]">Tahap Alur Saat Ini:</span>
                    <strong class="text-[#00254e] ml-1">{{ $spotlightTrip->approval_stage }}</strong>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('trips.export', $spotlightTrip->request_code ?? $spotlightTrip->id) }}" class="px-3 py-1.5 rounded-lg bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#111c2d] hover:bg-[#dfe8ff] transition-all">
                    Unduh PDF
                </a>
                <a href="{{ route('trips.show', $spotlightTrip->request_code ?? $spotlightTrip->id) }}" class="px-4 py-1.5 rounded-lg bg-[#00254e] text-white text-xs font-bold hover:bg-[#00346e] transition-all">
                    Buka Ruang Kerja
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Trips & History Table (Dynamic from DB) -->
    <div class="bg-white rounded-2xl border border-[#c3c6d1]/40 shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-[#c3c6d1]/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#00677e]">history</span>
                <div>
                    <h3 class="text-sm font-bold text-[#00254e]">
                        Histori &amp; Pengajuan Perjalanan Terkini
                    </h3>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('trips.export.csv') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#00677e] hover:bg-[#f0f3ff] transition-all">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Ekspor CSV</span>
                </a>
                <a href="{{ route('trips.index') }}"
                    class="inline-flex items-center gap-1 text-xs font-bold text-[#00254e] hover:underline">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#f0f3ff] text-[#737780] uppercase tracking-wider text-[10px] font-bold border-b border-[#c3c6d1]/30">
                        <th class="py-3 px-4">KODE</th>
                        <th class="py-3 px-4">PEGAWAI</th>
                        <th class="py-3 px-4">RUTE</th>
                        <th class="py-3 px-4">TANGGAL</th>
                        <th class="py-3 px-4">TOTAL BIAYA</th>
                        <th class="py-3 px-4">STATUS</th>
                        <th class="py-3 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c3c6d1]/20">
                    @forelse($recentTrips as $trip)
                    <tr class="hover:bg-[#f0f3ff]/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#00254e]">
                            <a href="{{ route('trips.show', $trip->request_code ?? $trip->id) }}" class="hover:underline">
                                #{{ $trip->request_code ?? $trip->id }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-[#111c2d]">
                            {{ optional($trip->traveler)->name ?? 'Andi Pratama' }}
                        </td>
                        <td class="py-3.5 px-4 text-[#111c2d]">
                            {{ $trip->origin_code ?? 'CGK' }} &rarr; {{ $trip->dest_code ?? 'SUB' }}
                        </td>
                        <td class="py-3.5 px-4 text-[#43474f]">
                            {{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-[#00254e]">
                            Rp {{ number_format($trip->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($trip->overall_status === 'booking_ready' || str_contains($trip->approval_stage, 'Confirmed') || str_contains($trip->approval_stage, 'Issued'))
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Tiket Diterbitkan
                                </span>
                            @elseif($trip->overall_status === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai
                                </span>
                            @elseif($trip->overall_status === 'cancelled' || str_contains($trip->approval_stage, 'Ditolak') || str_contains($trip->approval_stage, 'Rejected'))
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                    Ditolak
                                </span>
                            @elseif($trip->overall_status === 'draft')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                    Draf
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('trips.show', $trip->request_code ?? $trip->id) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#00677e] hover:underline">
                                <span>Buka</span>
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">
                            Belum ada pengajuan perjalanan dinas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
