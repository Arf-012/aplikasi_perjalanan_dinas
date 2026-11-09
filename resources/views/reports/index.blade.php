@extends('layouts.app')

@section('title', 'Laporan Audit & Kepatuhan Perjalanan Dinas')

@section('content')
<div class="space-y-6">
    <!-- Header with Breadcrumb & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-[#737780] mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-[#00254e] transition-colors">Dasbor</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#00254e] font-semibold">Laporan Audit &amp; Kepatuhan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight">
                Laporan Audit Kepatuhan &amp; Pengeluaran
            </h1>
            <p class="text-xs sm:text-sm text-[#43474f] mt-0.5">
                Rekapitulasi jejak audit digital perjalanan dinas, tingkat kepatuhan kebijakan (policy compliance), dan sertifikat verifikasi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('trips.export.csv') }}" class="h-10 px-4 bg-white border border-[#c3c6d1]/60 hover:bg-[#f0f3ff] text-[#00254e] text-xs font-bold rounded-xl shadow-2xs flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">download</span>
                <span>Ekspor CSV Laporan</span>
            </a>
            <a href="{{ route('trips.index') }}" class="h-10 px-4 bg-[#00254e] hover:bg-[#001833] text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">business_center</span>
                <span>Ruang Kerja Perjalanan</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Total Perjalanan Diaudit</span>
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">fact_check</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $totalTrips ?? 0 }}
            </div>
            <div class="text-[11px] text-[#737780] font-semibold mt-1">
                Tercatat di Basis Data
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Tingkat Kepatuhan Kebijakan</span>
                <span class="material-symbols-outlined text-[18px] text-emerald-500">verified</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-emerald-700">
                {{ $complianceRate ?? 100 }}%
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                {{ $compliantTrips ?? 0 }} Perjalanan Patuh Penuh
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Total Pengeluaran Dinas</span>
                <span class="material-symbols-outlined text-[18px] text-amber-500">payments</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#737780] font-semibold mt-1">
                Mata Uang IDR (Rupiah)
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Sertifikasi Audit Digital</span>
                <span class="material-symbols-outlined text-[18px] text-[#00254e]">lock</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e]">
                SHA-256
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                Kriptografis Terverifikasi
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-xl border border-[#c3c6d1]/40 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-[#c3c6d1]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                    Daftar Pengesahan Audit Perjalanan
                </h2>
                <p class="text-xs text-[#737780] mt-0.5">
                    Data pengajuan terintegrasi dengan validasi anggaran departemen dan matriks waterfall.
                </p>
            </div>
            <div>
                <span class="text-xs text-[#737780]">Total Data: <strong>{{ count($trips) }}</strong></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#f0f3ff]/60 border-b border-[#c3c6d1]/40 text-[11px] font-bold uppercase tracking-wider text-[#737780]">
                        <th class="py-3.5 px-4">KODE PENGAJUAN</th>
                        <th class="py-3.5 px-4">PEGAWAI</th>
                        <th class="py-3.5 px-4">DEPARTEMEN</th>
                        <th class="py-3.5 px-4">RUTE</th>
                        <th class="py-3.5 px-4">TOTAL BIAYA</th>
                        <th class="py-3.5 px-4">KEPATUHAN</th>
                        <th class="py-3.5 px-4">STATUS</th>
                        <th class="py-3.5 px-4 text-right">SERTIFIKAT AUDIT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c3c6d1]/20">
                    @forelse($trips as $t)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#00677e]">
                            <a href="{{ route('trips.show', $t->request_code ?? $t->id) }}" class="hover:underline">
                                #{{ $t->request_code ?? $t->id }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-[#111c2d]">
                            {{ optional($t->traveler)->name ?? 'Pegawai Perusahaan' }}
                        </td>
                        <td class="py-3.5 px-4 text-[#737780]">
                            {{ optional($t->traveler)->department ?? 'Operasional' }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-[#111c2d]">
                            {{ $t->origin_code ?? 'CGK' }} &rarr; {{ $t->dest_code ?? 'SUB' }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-[#00254e]">
                            Rp {{ number_format($t->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($t->policy_status === 'compliant')
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                    ✓ Patuh
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                    Dispensasi
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="text-xs font-semibold text-[#43474f]">
                                {{ $t->approval_stage }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('trips.certificate', $t->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#00254e] text-white text-[11px] font-bold hover:bg-[#00346e] transition-colors">
                                <span class="material-symbols-outlined text-[13px]">lock</span>
                                <span>Unduh SHA-256</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            Belum ada riwayat perjalanan yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
