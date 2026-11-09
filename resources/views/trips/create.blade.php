@extends('layouts.app')

@section('title', 'Formulir Pengajuan Perjalanan Dinas — TravelSys')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div>
        <nav class="flex items-center gap-1.5 text-xs text-[#737780] mb-1.5">
            <a href="{{ route('dashboard') }}" class="hover:text-[#00254e] transition-colors">Dasbor</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('trips.index') }}" class="hover:text-[#00254e] transition-colors">Daftar Perjalanan</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-[#00254e] font-semibold">Buat Pengajuan</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight">
                    Pengajuan Perjalanan Dinas Baru
                </h1>
                <p class="text-xs text-[#43474f] mt-0.5">
                    Lengkapi data rute, jadwal, dan estimasi biaya. Sistem akan otomatis memvalidasi kebijakan dan menentukan alur persetujuan.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#43474f] hover:bg-[#f0f3ff] transition-all self-start sm:self-auto">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Clean Form -->
    <form action="{{ route('trips.store') }}" method="POST" id="tripForm" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">error</span>
                    <span>Terdapat data yang belum lengkap atau tidak sesuai:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Kiri: Detail Rute & Keperluan (7 cols) -->
            <div class="lg:col-span-7 space-y-5">
                <!-- 1. Data Pemohon -->
                <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-[#c3c6d1]/20">
                        <span class="text-xs font-bold text-[#00254e] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">badge</span>
                            <span>Data Pegawai Pemohon</span>
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-[#f0f3ff] text-[#00254e] border border-[#c3c6d1]/40">
                            {{ auth()->user()->band ?? 'Band 2 - Staf / Pelaksana' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#00254e] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                            {{ substr(auth()->user()->name ?? 'AP', 0, 2) }}
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#111c2d]">
                                {{ auth()->user()->name ?? 'Andi Pratama' }}
                            </div>
                            <div class="text-[11px] text-[#737780]">
                                {{ auth()->user()->department ?? 'Rekayasa & Teknologi' }} &bull; NIP: {{ auth()->user()->employee_code ?? 'EMP-8849' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Rute & Jadwal -->
                <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-4">
                    <div class="pb-2 border-b border-[#c3c6d1]/20">
                        <span class="text-xs font-bold text-[#00254e] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">flight</span>
                            <span>Rute &amp; Jadwal Perjalanan</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="origin_code" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Kota / Bandara Asal <span class="text-red-500">*</span>
                            </label>
                            <select name="origin_code" id="origin_code" required class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none cursor-pointer">
                                <option value="CGK" {{ old('origin_code') == 'CGK' ? 'selected' : '' }}>Jakarta (CGK - Soekarno-Hatta)</option>
                                <option value="HLP" {{ old('origin_code') == 'HLP' ? 'selected' : '' }}>Jakarta (HLP - Halim)</option>
                                <option value="SUB" {{ old('origin_code') == 'SUB' ? 'selected' : '' }}>Surabaya (SUB - Juanda)</option>
                                <option value="DPS" {{ old('origin_code') == 'DPS' ? 'selected' : '' }}>Bali (DPS - Ngurah Rai)</option>
                                <option value="BDO" {{ old('origin_code') == 'BDO' ? 'selected' : '' }}>Bandung (BDO - Husein Sastranegara)</option>
                                <option value="YIA" {{ old('origin_code') == 'YIA' ? 'selected' : '' }}>Yogyakarta (YIA - Kulon Progo)</option>
                                <option value="SRG" {{ old('origin_code') == 'SRG' ? 'selected' : '' }}>Semarang (SRG - Ahmad Yani)</option>
                                <option value="KNO" {{ old('origin_code') == 'KNO' ? 'selected' : '' }}>Medan (KNO - Kualanamu)</option>
                                <option value="BPN" {{ old('origin_code') == 'BPN' ? 'selected' : '' }}>Balikpapan (BPN - Sepinggan)</option>
                                <option value="UPG" {{ old('origin_code') == 'UPG' ? 'selected' : '' }}>Makassar (UPG - Hasanuddin)</option>
                            </select>
                        </div>

                        <div>
                            <label for="dest_code" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Kota / Bandara Tujuan <span class="text-red-500">*</span>
                            </label>
                            <select name="dest_code" id="dest_code" required class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none cursor-pointer">
                                <option value="SUB" {{ old('dest_code', 'SUB') == 'SUB' ? 'selected' : '' }}>Surabaya (SUB - Juanda)</option>
                                <option value="CGK" {{ old('dest_code') == 'CGK' ? 'selected' : '' }}>Jakarta (CGK - Soekarno-Hatta)</option>
                                <option value="DPS" {{ old('dest_code') == 'DPS' ? 'selected' : '' }}>Bali (DPS - Ngurah Rai)</option>
                                <option value="BDO" {{ old('dest_code') == 'BDO' ? 'selected' : '' }}>Bandung (BDO - Husein Sastranegara)</option>
                                <option value="YIA" {{ old('dest_code') == 'YIA' ? 'selected' : '' }}>Yogyakarta (YIA - Kulon Progo)</option>
                                <option value="SRG" {{ old('dest_code') == 'SRG' ? 'selected' : '' }}>Semarang (SRG - Ahmad Yani)</option>
                                <option value="KNO" {{ old('dest_code') == 'KNO' ? 'selected' : '' }}>Medan (KNO - Kualanamu)</option>
                                <option value="BPN" {{ old('dest_code') == 'BPN' ? 'selected' : '' }}>Balikpapan (BPN - Sepinggan)</option>
                                <option value="UPG" {{ old('dest_code') == 'UPG' ? 'selected' : '' }}>Makassar (UPG - Hasanuddin)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="departure_date" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Tanggal Keberangkatan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="departure_date"
                                name="departure_date"
                                required
                                value="{{ old('departure_date', date('Y-m-d', strtotime('+3 days'))) }}"
                                class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none"
                            >
                        </div>

                        <div>
                            <label for="return_date" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Tanggal Kepulangan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                id="return_date"
                                name="return_date"
                                required
                                value="{{ old('return_date', date('Y-m-d', strtotime('+6 days'))) }}"
                                class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- 3. Keperluan Dinas & Anggaran -->
                <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-4">
                    <div class="pb-2 border-b border-[#c3c6d1]/20">
                        <span class="text-xs font-bold text-[#00254e] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">business_center</span>
                            <span>Maksud &amp; Keperluan Dinas</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="purpose_type" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Jenis Keperluan <span class="text-red-500">*</span>
                            </label>
                            <select name="purpose_type" id="purpose_type" required class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none cursor-pointer">
                                <option value="Pertemuan Klien">Pertemuan Klien / Mitra</option>
                                <option value="Implementasi Teknis">Implementasi &amp; Penugasan Teknis</option>
                                <option value="Pelatihan / Workshop">Pelatihan / Workshop</option>
                                <option value="Koordinasi Cabang">Koordinasi Internal Cabang</option>
                                <option value="Audit & Tinjauan Lapangan">Audit &amp; Tinjauan Lapangan</option>
                            </select>
                        </div>

                        <div>
                            <label for="cost_center_id" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Pusat Biaya (Cost Center) <span class="text-red-500">*</span>
                            </label>
                            <select name="cost_center_id" id="cost_center_id" required class="w-full h-10 px-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none cursor-pointer">
                                @forelse($budgets as $b)
                                    <option value="{{ $b->cost_center }}" {{ old('cost_center_id') == $b->cost_center ? 'selected' : '' }}>
                                        {{ $b->cost_center }} &bull; {{ $b->department_name }}
                                    </option>
                                @empty
                                    <option value="CC-402">CC-402 &bull; Divisi Rekayasa &amp; Teknologi</option>
                                    <option value="CC-201">CC-201 &bull; Keuangan &amp; Akuntansi</option>
                                    <option value="CC-101">CC-101 &bull; Operasional Perusahaan</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="purpose" class="block text-xs font-bold text-[#111c2d] mb-1">
                            Uraian Lengkap Keperluan &amp; Instansi Tujuan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="purpose"
                            name="purpose"
                            rows="3"
                            required
                            placeholder="Contoh: Pertemuan teknis integrasi sistem fase 2 bersama PT Surabaya Digital Mandiri dan peninjauan instalasi infrastruktur cloud."
                            class="w-full p-3 text-xs bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none leading-relaxed"
                        >{{ old('purpose', 'Pertemuan koordinasi teknis integrasi sistem dan peninjauan langsung instalasi server bersama mitra kerja.') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Estimasi Biaya & Waterfall Otorisasi (5 cols) -->
            <div class="lg:col-span-5 space-y-5">
                <!-- Estimasi Pengeluaran -->
                <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-4">
                    <div class="pb-2 border-b border-[#c3c6d1]/20 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#00254e] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">payments</span>
                            <span>Estimasi Biaya Perjalanan</span>
                        </span>
                        <span class="text-[10px] font-bold text-[#737780]">Rupiah (IDR)</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="flight_cost" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Tiket Transportasi / Penerbangan (PP)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-[#737780]">Rp</span>
                                <input
                                    type="number"
                                    id="flight_cost"
                                    name="flight_cost"
                                    min="0"
                                    step="50000"
                                    value="{{ old('flight_cost', 1700000) }}"
                                    oninput="calculateTotal()"
                                    class="w-full h-10 pl-9 pr-3 text-xs font-bold bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none"
                                >
                            </div>
                            <div class="text-[10px] text-[#737780] mt-0.5">Batas pagu kelas ekonomi: Rp 2.000.000 / leg</div>
                        </div>

                        <div>
                            <label for="hotel_cost" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Akomodasi Hotel &amp; Penginapan
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-[#737780]">Rp</span>
                                <input
                                    type="number"
                                    id="hotel_cost"
                                    name="hotel_cost"
                                    min="0"
                                    step="50000"
                                    value="{{ old('hotel_cost', 1500000) }}"
                                    oninput="calculateTotal()"
                                    class="w-full h-10 pl-9 pr-3 text-xs font-bold bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none"
                                >
                            </div>
                            <div class="text-[10px] text-[#737780] mt-0.5">Batas hotel kota: Rp 500.000 / malam</div>
                        </div>

                        <div>
                            <label for="per_diem_cost" class="block text-xs font-bold text-[#111c2d] mb-1">
                                Uang Harian &amp; Transportasi Lokal
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs font-bold text-[#737780]">Rp</span>
                                <input
                                    type="number"
                                    id="per_diem_cost"
                                    name="per_diem_cost"
                                    min="0"
                                    step="25000"
                                    value="{{ old('per_diem_cost', 0) }}"
                                    oninput="calculateTotal()"
                                    class="w-full h-10 pl-9 pr-3 text-xs font-bold bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none"
                                >
                            </div>
                            <div class="text-[10px] text-[#737780] mt-0.5">Standar taksi bandara / uang makan harian</div>
                        </div>
                    </div>

                    <!-- Total Estimasi -->
                    <div class="p-3.5 rounded-xl bg-[#f0f3ff] border border-[#c3c6d1]/40 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-[#737780]">Total Estimasi Biaya</div>
                            <div class="text-lg font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e]" id="lblDisplayTotal">
                                Rp 3.200.000
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-[24px] text-[#00677e]">receipt_long</span>
                    </div>
                </div>

                <!-- Deteksi Alur Waterfall Otomatis -->
                <div class="bg-white p-5 rounded-2xl border border-[#c3c6d1]/40 shadow-xs space-y-3">
                    <div class="pb-2 border-b border-[#c3c6d1]/20 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#00254e] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] text-[#00677e]">account_tree</span>
                            <span>Alur Persetujuan (Waterfall)</span>
                        </span>
                        <span id="tierBadge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Tier 1 (1 Tahap)
                        </span>
                    </div>

                    <div class="text-xs text-[#43474f] leading-relaxed" id="tierDescription">
                        Biaya &le; Rp 5.000.000. Cukup persetujuan dari <strong>Atasan Langsung (Line Manager)</strong> untuk penerbitan tiket langsung.
                    </div>

                    <div class="space-y-2 pt-1 text-xs">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-[#f9f9ff] border border-[#c3c6d1]/20">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-[#00254e] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                <span class="font-semibold text-[#111c2d]">Atasan Langsung (Line Manager)</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700">&check; Wajib</span>
                        </div>

                        <div id="stepFinanceRow" class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-[#c3c6d1]/20 opacity-50">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center text-[10px] font-bold">2</span>
                                <span class="font-semibold text-[#737780]">Otorisasi Keuangan (Finance)</span>
                            </div>
                            <span id="stepFinanceStatus" class="text-[10px] text-[#737780]">Tidak Diperlukan</span>
                        </div>

                        <div id="stepDirectorRow" class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-[#c3c6d1]/20 opacity-50">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center text-[10px] font-bold">3</span>
                                <span class="font-semibold text-[#737780]">Persetujuan Direktur Utama</span>
                            </div>
                            <span id="stepDirectorStatus" class="text-[10px] text-[#737780]">Tidak Diperlukan</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Simpan & Kirim -->
                <div class="space-y-2 pt-2">
                    <button
                        type="submit"
                        name="is_draft"
                        value="0"
                        class="w-full h-11 bg-[#00254e] hover:bg-[#00346e] text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Kirim Pengajuan Perjalanan</span>
                    </button>

                    <button
                        type="submit"
                        name="is_draft"
                        value="1"
                        class="w-full h-10 bg-white hover:bg-[#f0f3ff] text-[#43474f] hover:text-[#00254e] text-xs font-semibold rounded-xl border border-[#c3c6d1]/60 transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px] text-[#737780]">bookmark</span>
                        <span>Simpan sebagai Draf</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function calculateTotal() {
    const flight = parseFloat(document.getElementById('flight_cost').value) || 0;
    const hotel = parseFloat(document.getElementById('hotel_cost').value) || 0;
    const transit = parseFloat(document.getElementById('per_diem_cost').value) || 0;
    const total = flight + hotel + transit;

    document.getElementById('lblDisplayTotal').innerText = formatRupiah(total);

    const tierBadge = document.getElementById('tierBadge');
    const tierDesc = document.getElementById('tierDescription');
    const stepFinRow = document.getElementById('stepFinanceRow');
    const stepFinStatus = document.getElementById('stepFinanceStatus');
    const stepDirRow = document.getElementById('stepDirectorRow');
    const stepDirStatus = document.getElementById('stepDirectorStatus');

    if (total <= 5000000) {
        tierBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200';
        tierBadge.innerText = 'Tier 1 (1 Tahap)';
        tierDesc.innerHTML = 'Biaya &le; Rp 5.000.000. Cukup persetujuan dari <strong>Atasan Langsung (Line Manager)</strong> untuk penerbitan tiket langsung.';
        
        stepFinRow.className = 'flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-[#c3c6d1]/20 opacity-50';
        stepFinStatus.innerText = 'Tidak Diperlukan';
        stepFinStatus.className = 'text-[10px] text-[#737780]';

        stepDirRow.className = 'flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-[#c3c6d1]/20 opacity-50';
        stepDirStatus.innerText = 'Tidak Diperlukan';
        stepDirStatus.className = 'text-[10px] text-[#737780]';
    } else if (total <= 15000000) {
        tierBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200';
        tierBadge.innerText = 'Tier 2 (2 Tahap)';
        tierDesc.innerHTML = 'Biaya Rp 5 Jt – Rp 15 Jt. Memerlukan persetujuan bertingkat dari <strong>Atasan Langsung</strong> dan <strong>Keuangan Perusahaan</strong>.';
        
        stepFinRow.className = 'flex items-center justify-between p-2 rounded-lg bg-blue-50/70 border border-blue-200';
        stepFinStatus.innerText = '✓ Wajib (Tier 2)';
        stepFinStatus.className = 'text-[10px] font-bold text-blue-700';

        stepDirRow.className = 'flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-[#c3c6d1]/20 opacity-50';
        stepDirStatus.innerText = 'Tidak Diperlukan';
        stepDirStatus.className = 'text-[10px] text-[#737780]';
    } else {
        tierBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200';
        tierBadge.innerText = 'Tier 3 (3 Tahap)';
        tierDesc.innerHTML = 'Biaya > Rp 15.000.000. Pengeluaran bernilai tinggi memerlukan eskalasi penuh hingga <strong>Direktur Utama</strong>.';
        
        stepFinRow.className = 'flex items-center justify-between p-2 rounded-lg bg-blue-50/70 border border-blue-200';
        stepFinStatus.innerText = '✓ Wajib';
        stepFinStatus.className = 'text-[10px] font-bold text-blue-700';

        stepDirRow.className = 'flex items-center justify-between p-2 rounded-lg bg-purple-50/70 border border-purple-200';
        stepDirStatus.innerText = '✓ Wajib (Tier 3)';
        stepDirStatus.className = 'text-[10px] font-bold text-purple-700';
    }
}

document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection
