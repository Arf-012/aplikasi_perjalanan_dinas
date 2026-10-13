@extends('layouts.app')

@php
    $isDeclined = ($trip->approval_stage === 'Rejected' || $trip->approval_stage === 'Ditolak' || $trip->overall_status === 'cancelled' || $trip->approval_stage === 'Dibatalkan');
    $isCompleted = ($trip->overall_status === 'completed' || str_contains($trip->approval_stage, 'Selesai'));
    $tripCode = $trip->request_code ?? ('TRV-' . ($trip->id ?? '10291'));
    $totalCost = (float)($trip->total_cost ?? 3200000);
    $flightCost = (float)($trip->flight_cost ?? 1700000);
    $hotelCost = (float)($trip->hotel_cost ?? 1500000);
    $transitCost = (float)($trip->transit_cost ?? 0);
    $nightCount = max(1, \Carbon\Carbon::parse($trip->departure_date)->diffInDays(\Carbon\Carbon::parse($trip->return_date)));
@endphp

@section('title', 'Ruang Kerja Perjalanan #' . $tripCode . ' — TravelSys')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <nav class="flex items-center gap-1.5 text-[#737780]">
            <a href="{{ route('dashboard') }}" class="hover:text-[#00254e]">Dasbor</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('trips.index') }}" class="hover:text-[#00254e]">Daftar Perjalanan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-[#00254e] font-semibold">#{{ $tripCode }}</span>
        </nav>

        <div class="flex items-center gap-2 text-[#737780] font-mono text-[11px]">
            <span class="material-symbols-outlined text-[15px] text-[#00677e]">verified_user</span>
            <span>Audit Otomatis Aktif &bull; Segel Hash: AUTH-{{ strtoupper(substr(md5($trip->id . $trip->created_at), 0, 6)) }}-ID</span>
        </div>
    </div>

    <!-- Main Header Card -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-[#c3c6d1]/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e]">
                    {{ $trip->origin ?? 'Jakarta (CGK)' }} &rarr; {{ $trip->destination ?? 'Surabaya (SUB)' }}
                </h1>

                @if($isDeclined)
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        <span>Ditolak / Dibatalkan</span>
                    </span>
                @elseif($isCompleted)
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span>Perjalanan Selesai &amp; Direkonsiliasi</span>
                    </span>
                @elseif($trip->overall_status === 'booking_ready' || str_contains($trip->approval_stage, 'Confirmed') || str_contains($trip->approval_stage, 'Issued'))
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span>Tiket Diterbitkan (Booking Ready)</span>
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Menunggu Persetujuan</span>
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3 text-xs text-[#737780] mt-1.5 flex-wrap">
                <span>Kode: <strong class="text-[#111c2d]">#{{ $tripCode }}</strong></span>
                <span>&bull;</span>
                <span>Jadwal: <strong class="text-[#111c2d]">{{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($trip->return_date)->translatedFormat('d M Y') }}</strong></span>
                <span>&bull;</span>
                <span>Pemohon: <strong class="text-[#111c2d]">{{ optional($trip->traveler)->name ?? 'Andi Pratama' }}</strong></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('trips.export', $trip->id) }}" class="px-3.5 py-2 rounded-xl bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#111c2d] hover:bg-[#f0f3ff] transition-all flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-[#00677e]">print</span>
                <span>Surat Tugas (PDF)</span>
            </a>

            <a href="{{ route('trips.certificate', $trip->id) }}" class="px-3.5 py-2 rounded-xl bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#111c2d] hover:bg-[#f0f3ff] transition-all flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-[#00677e]">lock</span>
                <span>Sertifikat Audit</span>
            </a>

            @if(! $isDeclined && ! $isCompleted)
                <form action="{{ route('trips.cancel', $trip->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan perjalanan dinas ini?');" class="inline m-0">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-white border border-red-200 text-xs font-semibold text-red-700 hover:bg-red-50 transition-all flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        <span>Batalkan</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($isDeclined)
        <!-- Banner Khusus Status Ditolak / Dibatalkan -->
        <div class="p-4 sm:p-5 rounded-2xl bg-red-50 border border-red-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[24px] text-red-600 mt-0.5">error</span>
                <div>
                    <h3 class="text-sm font-bold text-red-900">Pengajuan Perjalanan Ini Telah Ditolak atau Dibatalkan</h3>
                    <p class="text-xs text-red-700 mt-0.5">
                        Anda dapat membuka kembali permohonan ini untuk merevisi data atau mengajukan permohonan dispensasi kebijakan kepada Direksi.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <form action="{{ route('trips.reopen', $trip->id) }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#00254e] text-white text-xs font-bold hover:bg-[#00346e] transition-all cursor-pointer">
                        Buka Kembali untuk Revisi
                    </button>
                </form>

                <form action="{{ route('trips.remedy', $trip->id) }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-white border border-red-300 text-xs font-semibold text-red-800 hover:bg-red-100 transition-all cursor-pointer">
                        Sesuaikan Sesuai Pagu Standar
                    </button>
                </form>

                <form action="{{ route('trips.exception', $trip->id) }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-white border border-red-300 text-xs font-semibold text-red-800 hover:bg-red-100 transition-all cursor-pointer">
                        Ajukan Dispensasi Direksi
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- 1. Alur Persetujuan Bertingkat (Waterfall Stepper) -->
    <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-[#c3c6d1]/20">
            <h2 class="text-xs font-bold text-[#00254e] uppercase tracking-wider flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">account_tree</span>
                <span>Status Alur Persetujuan Bertingkat (Waterfall Matrix)</span>
            </h2>
            <span class="text-[11px] font-semibold text-[#00677e]">
                Otorisasi Berdasarkan Pagu Biaya
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
            <!-- Tahap 1: Pengajuan -->
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Tahap 1</span>
                    <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                </div>
                <div class="font-bold text-[#111c2d]">Pengajuan Masuk</div>
                <div class="text-[10px] text-[#737780] mt-0.5">Pemohon: {{ optional($trip->traveler)->name ?? 'Andi Pratama' }}</div>
            </div>

            <!-- Tahap 2: Atasan Langsung -->
            @php
                $isManagerDone = !str_contains($trip->approval_stage, 'Line Manager') && !str_contains($trip->approval_stage, 'Draft');
            @endphp
            <div class="p-3.5 rounded-xl {{ $isManagerDone ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-[#f0f3ff] border border-[#00254e]/30' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold {{ $isManagerDone ? 'text-emerald-800' : 'text-[#00254e]' }} uppercase tracking-wider">Tahap 2</span>
                    @if($isManagerDone)
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-[#00677e] animate-pulse"></span>
                    @endif
                </div>
                <div class="font-bold text-[#111c2d]">Atasan Langsung</div>
                <div class="text-[10px] text-[#737780] mt-0.5">Batas Pagu &le; Rp 5.000.000</div>
            </div>

            <!-- Tahap 3: Keuangan -->
            @php
                $needsFinance = ($totalCost > 5000000);
                $isFinanceDone = ($totalCost <= 5000000) || str_contains($trip->approval_stage, 'Director') || str_contains($trip->approval_stage, 'Confirmed') || str_contains($trip->approval_stage, 'Issued') || $isCompleted;
                $isFinanceActive = str_contains($trip->approval_stage, 'Finance');
            @endphp
            <div class="p-3.5 rounded-xl {{ $isFinanceDone ? 'bg-emerald-50/70 border border-emerald-200' : ($isFinanceActive ? 'bg-[#f0f3ff] border border-[#00254e]/30' : 'bg-gray-50 border border-gray-200 opacity-60') }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">Tahap 3</span>
                    @if($isFinanceDone)
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                    @elseif($isFinanceActive)
                        <span class="w-2 h-2 rounded-full bg-[#00677e] animate-pulse"></span>
                    @else
                        <span class="text-[10px] text-[#737780]">{{ $needsFinance ? 'Menunggu' : 'Dilewati' }}</span>
                    @endif
                </div>
                <div class="font-bold text-[#111c2d]">Otorisasi Keuangan</div>
                <div class="text-[10px] text-[#737780] mt-0.5">{{ $needsFinance ? 'Pagu ≤ Rp 15.000.000' : 'Otomatis Selesai (≤ Rp 5 Jt)' }}</div>
            </div>

            <!-- Tahap 4: Tiket Diterbitkan -->
            @php
                $isTicketReady = ($trip->overall_status === 'booking_ready' || str_contains($trip->approval_stage, 'Confirmed') || str_contains($trip->approval_stage, 'Issued') || $isCompleted);
            @endphp
            <div class="p-3.5 rounded-xl {{ $isTicketReady ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-gray-50 border border-gray-200 opacity-60' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold {{ $isTicketReady ? 'text-emerald-800' : 'text-[#737780]' }} uppercase tracking-wider">Tahap 4</span>
                    @if($isTicketReady)
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                    @else
                        <span class="material-symbols-outlined text-[16px] text-gray-400">schedule</span>
                    @endif
                </div>
                <div class="font-bold text-[#111c2d]">Tiket Diterbitkan</div>
                <div class="text-[10px] text-[#737780] mt-0.5">{{ $isTicketReady ? 'Izin Perjalanan Aktif' : 'Menunggu Otorisasi Final' }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Rencana & Modul Reimbursement -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Kolom Kiri: Transportasi & Hotel (7 cols) -->
        <div class="lg:col-span-7 space-y-5">
            <!-- Kartu Penerbangan -->
            <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-[#c3c6d1]/20">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#00677e]">flight</span>
                        <h3 class="text-xs font-bold text-[#00254e] uppercase tracking-wider">Transportasi Penerbangan (PP)</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        &check; Sesuai Kebijakan
                    </span>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div>
                        <div class="text-sm font-bold text-[#111c2d]">Garuda Indonesia &bull; Kelas Ekonomi</div>
                        <div class="text-xs text-[#737780] mt-0.5">
                            Rute: {{ $trip->origin }} ({{ $trip->origin_code }}) &harr; {{ $trip->destination }} ({{ $trip->dest_code }})
                        </div>
                        <div class="text-[11px] text-[#737780] mt-1">
                            Pergi: 07:45 WIB &bull; Pulang: 19:20 WIB
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-extrabold text-[#00254e]">Rp {{ number_format($flightCost, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-[#737780]">Termasuk Bagasi 20kg</div>
                    </div>
                </div>
            </div>

            <!-- Kartu Penginapan Hotel -->
            <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-[#c3c6d1]/20">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#00677e]">hotel</span>
                        <h3 class="text-xs font-bold text-[#00254e] uppercase tracking-wider">Akomodasi Penginapan</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        &check; Sesuai Kebijakan
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                    <div>
                        <div class="text-sm font-bold text-[#111c2d]">Hotel Santika Premiere &bull; Kamar Standar</div>
                        <div class="text-xs text-[#737780] mt-0.5">
                            Lokasi: {{ $trip->destination }} &bull; Durasi: {{ $nightCount }} Malam
                        </div>
                        <div class="text-[11px] text-[#737780] mt-1">
                            Pagu: Rp 500.000 / malam &times; {{ $nightCount }} malam (Termasuk Sarapan)
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="text-base font-extrabold text-[#00254e]">Rp {{ number_format($hotelCost, 0, ',', '.') }}</div>
                        <button type="button" onclick="document.getElementById('editCostModal').classList.remove('hidden')" class="mt-1 px-3 py-1 rounded-lg bg-[#f0f3ff] hover:bg-[#dfe8ff] text-[#00254e] text-xs font-bold transition-all border border-[#c3c6d1]/50 cursor-pointer">
                            Sesuaikan Biaya
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kartu Maksud Dinas -->
            <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-2">
                <span class="text-[10px] font-bold text-[#737780] uppercase tracking-wider">Maksud &amp; Keperluan Dinas</span>
                <div class="text-xs font-bold text-[#111c2d]">{{ $trip->purpose_type ?? 'Pertemuan Klien' }}</div>
                <p class="text-xs text-[#43474f] leading-relaxed">
                    {{ $trip->purpose_description ?? $trip->purpose ?? 'Pelaksanaan koordinasi teknis dan pertemuan penugasan dinas resmi.' }}
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Finansial & Modul Reimbursement (5 cols) -->
        <div class="lg:col-span-5 space-y-5">
            <!-- Ringkasan Anggaran -->
            <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-[#c3c6d1]/20">
                    <h3 class="text-xs font-bold text-[#00254e] uppercase tracking-wider">Ringkasan Finansial</h3>
                    <span class="text-[10px] font-bold text-[#737780]">Pusat Biaya: {{ $trip->cost_center ?? 'CC-402' }}</span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[#737780]">Tiket Transportasi</span>
                        <span class="font-semibold text-[#111c2d]">Rp {{ number_format($flightCost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#737780]">Hotel &amp; Penginapan</span>
                        <span class="font-semibold text-[#111c2d]">Rp {{ number_format($hotelCost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[#737780]">Transport Lokal / Uang Harian</span>
                        <span class="font-semibold text-[#111c2d]">Rp {{ number_format($transitCost, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-[#c3c6d1]/20 flex items-center justify-between">
                        <span class="font-bold text-[#00254e]">Total Uang Muka / Pagu</span>
                        <span class="text-base font-extrabold text-[#00254e]">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Modul Pelaporan Biaya Riil & Reimbursement (Proposal Modul 3) -->
            <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-4">
                <div class="pb-2 border-b border-[#c3c6d1]/20 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-[#00254e] uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">receipt_long</span>
                            <span>Pelaporan Biaya Riil (Reimbursement)</span>
                        </h3>
                        <p class="text-[11px] text-[#737780] mt-0.5">Proposal Modul 3: Rekonsiliasi &amp; Pengembalian Kas</p>
                    </div>
                    @if($isCompleted)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Direkonsiliasi
                        </span>
                    @endif
                </div>

                <form action="{{ route('trips.reimbursement', $trip->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-[#111c2d] mb-1">Biaya Riil Tiket Pesawat / Kereta (Rp)</label>
                        <input
                            type="number"
                            name="real_flight_cost"
                            id="real_flight_cost"
                            value="{{ old('real_flight_cost', $flightCost) }}"
                            oninput="calcReimbursement()"
                            required
                            class="w-full h-9 px-3 bg-[#f9f9ff] border border-[#c3c6d1]/60 rounded-xl focus:border-[#00677e] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#111c2d] mb-1">Biaya Riil Hotel / Penginapan (Rp)</label>
                        <input
                            type="number"
                            name="real_hotel_cost"
                            id="real_hotel_cost"
                            value="{{ old('real_hotel_cost', $hotelCost) }}"
                            oninput="calcReimbursement()"
                            required
                            class="w-full h-9 px-3 bg-[#f9f9ff] border border-[#c3c6d1]/60 rounded-xl focus:border-[#00677e] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#111c2d] mb-1">Biaya Transportasi Lokal / Lainnya (Rp)</label>
                        <input
                            type="number"
                            name="real_other_cost"
                            id="real_other_cost"
                            value="{{ old('real_other_cost', 0) }}"
                            oninput="calcReimbursement()"
                            class="w-full h-9 px-3 bg-[#f9f9ff] border border-[#c3c6d1]/60 rounded-xl focus:border-[#00677e] focus:bg-white focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#111c2d] mb-1">Unggah Nota / Struk Bukti Transaksi</label>
                        <input
                            type="file"
                            name="receipt_file"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full text-[11px] text-[#43474f] file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-[#00254e] file:text-white"
                        >
                    </div>

                    <!-- Live Calculation Preview -->
                    <div class="p-3 rounded-xl bg-[#f0f3ff] border border-[#c3c6d1]/30 space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-[#737780]">Total Biaya Riil:</span>
                            <span class="font-bold text-[#111c2d]" id="lblRealTotal">Rp 3.200.000</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-[#737780]">Selisih (Uang Muka - Riil):</span>
                            <span class="font-bold text-emerald-700" id="lblRealVariance">Rp 0 (Sesuai)</span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full h-10 bg-[#00254e] hover:bg-[#00346e] text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span>Simpan Laporan Realisasi Biaya</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sesuaikan Biaya -->
<div id="editCostModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-[#c3c6d1]/40 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#c3c6d1]/20">
            <h3 class="text-sm font-bold text-[#00254e]">Sesuaikan Biaya &amp; Rincian Perjalanan</h3>
            <button type="button" onclick="document.getElementById('editCostModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('trips.update', $trip->id) }}" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-[#111c2d] mb-1">Biaya Tiket Pesawat (Rp)</label>
                <input type="number" name="flight_cost" value="{{ $flightCost }}" required class="w-full h-10 px-3 border border-[#c3c6d1]/60 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#111c2d] mb-1">Biaya Hotel / Penginapan (Rp)</label>
                <input type="number" name="hotel_cost" value="{{ $hotelCost }}" required class="w-full h-10 px-3 border border-[#c3c6d1]/60 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#111c2d] mb-1">Biaya Transport Lokal / Uang Harian (Rp)</label>
                <input type="number" name="transit_cost" value="{{ $transitCost }}" class="w-full h-10 px-3 border border-[#c3c6d1]/60 rounded-xl">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editCostModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#00254e] hover:bg-[#00346e] text-white text-xs font-bold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calcReimbursement() {
    const flight = parseFloat(document.getElementById('real_flight_cost').value) || 0;
    const hotel = parseFloat(document.getElementById('real_hotel_cost').value) || 0;
    const other = parseFloat(document.getElementById('real_other_cost').value) || 0;
    const totalReal = flight + hotel + other;
    const advance = {{ $totalCost }};
    const variance = advance - totalReal;

    document.getElementById('lblRealTotal').innerText = 'Rp ' + totalReal.toLocaleString('id-ID');

    const lblVar = document.getElementById('lblRealVariance');
    if (variance > 0) {
        lblVar.innerText = '+Rp ' + variance.toLocaleString('id-ID') + ' (Lebih bayar, kembali ke kas)';
        lblVar.className = 'font-bold text-emerald-700';
    } else if (variance < 0) {
        lblVar.innerText = '-Rp ' + Math.abs(variance).toLocaleString('id-ID') + ' (Reimbursement tambahan)';
        lblVar.className = 'font-bold text-blue-700';
    } else {
        lblVar.innerText = 'Rp 0 (Sesuai dengan uang muka)';
        lblVar.className = 'font-bold text-gray-700';
    }
}

document.addEventListener('DOMContentLoaded', calcReimbursement);
</script>
@endsection
