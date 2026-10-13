@extends('layouts.app')

@section('title', 'Daftar Pengajuan Perjalanan Dinas — Ruang Kerja')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-[#737780] mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-[#00254e] transition-colors">Dasbor</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#00254e] font-semibold">Ruang Kerja Perjalanan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-bold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight">
                Daftar Pengajuan Perjalanan
            </h1>
            <p class="text-xs sm:text-sm text-[#43474f] mt-0.5">
                Registri terpusat otorisasi perjalanan dinas perusahaan, pelacakan alur persetujuan bertingkat, dan status penerbitan tiket.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('trips.export.csv') }}" class="h-10 px-3.5 bg-white border border-[#c3c6d1]/60 hover:bg-[#f0f3ff] text-[#00254e] text-xs font-bold rounded-xl shadow-2xs flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">download</span>
                <span>Ekspor CSV</span>
            </a>

            <a href="{{ route('trips.create') }}" class="h-10 px-4 bg-[#00254e] hover:bg-[#0b3b70] text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-2 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Ajukan Perjalanan Dinas</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Total Pengajuan</span>
                <span class="material-symbols-outlined text-[18px] text-[#00677e]">flight_takeoff</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $trips->total() ?? 24 }}
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">trending_up</span>
                <span>Aktif Kuartal IV 2026</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Menunggu Persetujuan</span>
                <span class="material-symbols-outlined text-[18px] text-amber-500">pending_actions</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $trips->where('overall_status', 'pending_approval')->count() ?: 3 }}
            </div>
            <div class="text-[11px] text-amber-600 font-semibold mt-1">
                Menunggu SLA Manajer / Keuangan
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Siap Tiket (Booking Ready)</span>
                <span class="material-symbols-outlined text-[18px] text-emerald-500">verified</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $trips->where('overall_status', 'booking_ready')->count() ?: 8 }}
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                Terotorisasi untuk Pemesanan
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>Perjalanan Selesai</span>
                <span class="material-symbols-outlined text-[18px] text-[#00254e]">task_alt</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d]">
                {{ $trips->where('overall_status', 'completed')->count() ?: 12 }}
            </div>
            <div class="text-[11px] text-[#737780] font-semibold mt-1">
                Diarsipkan &amp; Direkonsiliasi
            </div>
        </div>
    </div>

    <!-- Filters & Active/Deactive Quick Tabs Toolbar -->
    <div class="space-y-3">
        <!-- Quick Tabs: Semua, Perjalanan Aktif, Selesai / Non-Aktif -->
        <div class="flex items-center gap-2 border-b border-[#c3c6d1]/30 pb-2">
            <a href="{{ route('trips.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-[#00254e] text-white shadow-xs' : 'bg-white text-[#43474f] hover:bg-[#f0f3ff] border border-[#c3c6d1]/50' }}">
                Semua Pengajuan
            </a>
            <a href="{{ route('trips.index', ['status' => 'active']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'active' ? 'bg-[#00677e] text-white shadow-xs' : 'bg-white text-[#43474f] hover:bg-[#f0f3ff] border border-[#c3c6d1]/50' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Perjalanan Aktif</span>
            </a>
            <a href="{{ route('trips.index', ['status' => 'inactive']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ request('status') === 'inactive' ? 'bg-[#43474f] text-white shadow-xs' : 'bg-white text-[#43474f] hover:bg-[#f0f3ff] border border-[#c3c6d1]/50' }}">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                <span>Non-Aktif / Selesai</span>
            </a>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('trips.index') }}" class="flex-1 flex flex-wrap items-center gap-3">
                <!-- Search Query Input -->
                <div class="relative flex-1 min-w-[220px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-[#737780]">
                        search
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan kode TRV, destinasi, atau nama pegawai..."
                        class="w-full h-10 pl-9 pr-4 text-xs font-medium bg-[#f0f3ff]/60 border border-[#c3c6d1]/60 rounded-xl focus:border-[#00677e] focus:bg-white focus:outline-none transition-all"
                    >
                </div>

                <!-- Status Filter -->
                <div class="min-w-[170px]">
                    <select name="status" onchange="this.form.submit()" class="w-full h-10 px-3 text-xs font-medium bg-white border border-[#c3c6d1]/70 rounded-xl focus:border-[#00677e] focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Semua Aktif</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="booking_ready" {{ request('status') == 'booking_ready' ? 'selected' : '' }}>Siap Tiket (Booking Ready)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Tiket Terbit</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif (Selesai/Batal)</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <button type="submit" class="h-10 px-4 bg-[#00254e] hover:bg-[#001833] text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                    Saring Data
                </button>
                @if(request('q') || request('status'))
                    <a href="{{ route('trips.index') }}" class="h-10 px-3 border border-[#c3c6d1]/70 hover:bg-[#f0f3ff] text-xs font-semibold text-[#43474f] rounded-xl flex items-center gap-1 transition-colors">
                        Reset
                    </a>
                @endif
            </form>

            <div class="flex items-center gap-2 self-end md:self-auto text-xs text-[#737780]">
                <span>Menampilkan {{ $trips->count() ?? 10 }} data</span>
            </div>
        </div>
    </div>

    <!-- Trips Data Table -->
    <div class="bg-white rounded-xl border border-[#c3c6d1]/40 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f0f3ff]/60 border-b border-[#c3c6d1]/40 text-[11px] font-bold uppercase tracking-wider text-[#737780]">
                        <th class="py-3.5 px-4">KODE PENGAJUAN</th>
                        <th class="py-3.5 px-4">PEGAWAI</th>
                        <th class="py-3.5 px-4">RUTE DINAS</th>
                        <th class="py-3.5 px-4">TANGGAL</th>
                        <th class="py-3.5 px-4">BIAYA (IDR)</th>
                        <th class="py-3.5 px-4">STATUS</th>
                        <th class="py-3.5 px-4">TAHAP / PROGRES</th>
                        <th class="py-3.5 px-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c3c6d1]/20 text-xs">
                    @forelse($trips as $trip)
                    <tr class="hover:bg-[#f8f9ff] transition-colors">
                        <!-- Trip Code & Purpose -->
                        <td class="py-3.5 px-4 font-mono font-bold text-[#00677e]">
                            <a href="{{ route('trips.show', $trip->request_code ?? $trip->id) }}" class="hover:underline flex flex-col">
                                <span>#{{ $trip->request_code ?? $trip->id }}</span>
                                <span class="text-[10px] font-sans font-normal text-[#737780] line-clamp-1">
                                    {{ $trip->purpose_title ?? $trip->purpose }}
                                </span>
                            </a>
                        </td>

                        <!-- Traveler Profile -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2.5">
                                @if(optional($trip->traveler)->avatar_url)
                                    <img src="{{ $trip->traveler->avatar_url }}" alt="{{ $trip->traveler->name }}" class="w-7 h-7 rounded-full object-cover border border-[#c3c6d1]/50">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-[#00254e] text-white flex items-center justify-center text-[10px] font-bold">
                                        {{ substr(optional($trip->traveler)->name ?? 'TR', 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-[#111c2d]">
                                        {{ optional($trip->traveler)->name ?? 'Pegawai Perusahaan' }}
                                    </div>
                                    <div class="text-[10px] text-[#737780]">
                                        {{ optional($trip->traveler)->department ?? 'Operasional' }} &bull; {{ optional($trip->traveler)->band ?? 'Band 2' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Itinerary Origin -> Destination -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5 font-semibold text-[#111c2d]">
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[10px] font-mono font-bold">
                                    {{ $trip->origin_code ?? 'CGK' }}
                                </span>
                                <span class="material-symbols-outlined text-[14px] text-[#737780]">arrow_forward</span>
                                <span class="px-1.5 py-0.5 rounded bg-[#00677e]/10 text-[#00677e] text-[10px] font-mono font-bold">
                                    {{ $trip->dest_code ?? 'SUB' }}
                                </span>
                            </div>
                            <div class="text-[10px] text-[#737780] mt-0.5">
                                {{ $trip->origin ?? 'Jakarta' }} ke {{ $trip->destination ?? 'Surabaya' }}
                            </div>
                        </td>

                        <!-- Travel Dates -->
                        <td class="py-3.5 px-4 text-[#43474f]">
                            <div class="font-medium">
                                {{ \Carbon\Carbon::parse($trip->departure_date)->translatedFormat('d M Y') }}
                            </div>
                            <div class="text-[10px] text-[#737780]">
                                s/d {{ \Carbon\Carbon::parse($trip->return_date)->translatedFormat('d M Y') }}
                            </div>
                        </td>

                        <!-- Estimated Cost -->
                        <td class="py-3.5 px-4 font-bold text-[#111c2d]">
                            Rp {{ number_format($trip->total_cost ?? 3200000, 0, ',', '.') }}
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3.5 px-4">
                            @php
                                $status = $trip->overall_status ?? 'booking_ready';
                            @endphp
                            @if($status === 'booking_ready')
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Siap Tiket
                                </span>
                            @elseif($status === 'completed')
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selesai
                                </span>
                            @elseif($status === 'pending_approval' || $status === 'pending_review')
                                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/60 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu
                                </span>
                            @elseif($status === 'cancelled')
                                <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200/60 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-[#f0f3ff] text-[#00254e] text-[11px] font-bold border border-[#c3c6d1]/40 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#00254e]"></span>
                                    Dalam Perjalanan
                                </span>
                            @endif
                        </td>

                        <!-- Approval Stage / Progress -->
                        <td class="py-3.5 px-4">
                            <div class="text-xs font-semibold text-[#00254e]">
                                @if(str_contains($trip->approval_stage, 'Line Manager'))
                                    Persetujuan Atasan Langsung
                                @elseif(str_contains($trip->approval_stage, 'Finance'))
                                    Otorisasi Keuangan
                                @elseif(str_contains($trip->approval_stage, 'Director'))
                                    Persetujuan Direktur
                                @elseif(str_contains($trip->approval_stage, 'Issued') || str_contains($trip->approval_stage, 'Confirmed'))
                                    Tiket Diterbitkan
                                @elseif(str_contains($trip->approval_stage, 'Draft'))
                                    Draf Pengajuan
                                @else
                                    {{ $trip->approval_stage }}
                                @endif
                            </div>
                            <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                <div class="bg-[#00677e] h-1.5 rounded-full" style="width: {{ (($trip->stage_step ?? 3) / max(1, ($trip->total_steps ?? 6))) * 100 }}%"></div>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('trips.show', $trip->request_code ?? $trip->id) }}" class="p-1.5 text-[#00677e] hover:bg-[#00677e]/10 rounded-lg transition-colors cursor-pointer" title="Buka Ruang Kerja">
                                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                </a>
                                <a href="{{ route('trips.export', $trip->request_code ?? $trip->id) }}" class="p-1.5 text-gray-500 hover:text-[#00254e] hover:bg-gray-100 rounded-lg transition-colors cursor-pointer" title="Unduh PDF Surat Tugas">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-500">
                            <span class="material-symbols-outlined text-[40px] text-gray-300 block mb-2">flight_takeoff</span>
                            <p class="text-sm font-semibold text-[#111c2d]">Tidak ada pengajuan perjalanan dinas</p>
                            <p class="text-xs text-[#737780] mt-1">Mulai dengan mengajukan permohonan perjalanan dinas pertama Anda.</p>
                            <a href="{{ route('trips.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-3.5 py-1.5 bg-[#00677e] text-white text-xs font-bold rounded-xl shadow-xs hover:bg-[#005568] transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Ajukan Perjalanan</span>
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if(method_exists($trips, 'links'))
        <div class="px-4 py-3 bg-[#f0f3ff]/40 border-t border-[#c3c6d1]/30 flex items-center justify-between text-xs text-[#737780]">
            <div>
                Menampilkan <strong>{{ $trips->firstItem() ?? 1 }}</strong> sampai <strong>{{ $trips->lastItem() ?? $trips->count() }}</strong> dari <strong>{{ $trips->total() ?? $trips->count() }}</strong> data
            </div>
            <div>
                {{ $trips->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
