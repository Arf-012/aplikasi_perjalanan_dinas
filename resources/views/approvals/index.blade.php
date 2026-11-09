@extends('layouts.app')

@section('title', 'Pusat Persetujuan & Keputusan Dinas — TravelSys')

@section('content')
<div class="space-y-6">
    <!-- Top Breadcrumb & Controls Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-[#737780] mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-[#00254e]">Dasbor</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#00254e] font-semibold">Pusat Persetujuan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e] tracking-tight">
                Pusat Persetujuan &amp; Otorisasi Dinas
            </h1>
            <p class="text-xs sm:text-sm text-[#43474f] mt-0.5">
                Tinjau dan sahkan permohonan perjalanan dinas berdasarkan matriks batas akun (Waterfall Authority).
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#f0f3ff] text-[#00677e] border border-[#c3c6d1]/40">
                <span class="w-2 h-2 rounded-full bg-[#00677e] animate-pulse"></span>
                <span>Otorisasi Aktif: {{ auth()->user()->role_display_name }}</span>
            </span>

            <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white border border-[#c3c6d1]/60 text-xs font-semibold text-[#111c2d] hover:bg-[#f0f3ff] transition-all shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-[#737780]">history</span>
                <span>Daftar Perjalanan</span>
            </a>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>MENUNGGU PERSETUJUAN</span>
                <span class="material-symbols-outlined text-[18px] text-amber-500">pending_actions</span>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d]">{{ $pendingApprovalsCount ?? count($pendingRequests) }}</span>
                <span class="text-xs text-[#737780]">pengajuan aktif</span>
            </div>
            <div class="text-[11px] text-amber-600 font-semibold mt-1">
                Perlu tindakan peninjau
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>TIER 1 (ATASAN LANGSUNG)</span>
                <span class="material-symbols-outlined text-[18px] text-emerald-600">verified_user</span>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#111c2d]">{{ $tier1Count ?? 0 }}</span>
                <span class="text-xs text-[#737780]">biaya &le; Rp 5 Jt</span>
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                Otorisasi 1 pintu
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#c3c6d1]/40 shadow-xs">
            <div class="flex items-center justify-between text-[#737780] text-xs font-medium mb-1">
                <span>TIER 2 &amp; 3 (ESKALASI)</span>
                <span class="material-symbols-outlined text-[18px] text-[#00254e]">account_tree</span>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e]">{{ ($tier2Count ?? 0) + ($tier3Count ?? 0) }}</span>
                <span class="text-xs text-[#737780]">Keuangan &amp; Direktur</span>
            </div>
            <div class="text-[11px] text-[#737780] font-semibold mt-1">
                Biaya &gt; Rp 5 Jt / bernilai tinggi
            </div>
        </div>
    </div>

    @php
        $firstTrip = $pendingRequests->first();
    @endphp

    @if($pendingRequests->isEmpty())
        <div class="bg-white rounded-2xl border border-[#c3c6d1]/40 shadow-xs p-12 text-center space-y-3">
            <span class="material-symbols-outlined text-[48px] text-[#00677e]">task_alt</span>
            <h3 class="text-base font-bold text-[#111c2d]">Semua Pengajuan Telah Ditinjau</h3>
            <p class="text-xs text-[#737780] max-w-md mx-auto">
                Saat ini tidak ada permohonan perjalanan dinas yang menunggu tindakan Anda. Anda dapat melihat riwayat pengajuan di ruang kerja perjalanan.
            </p>
            <div class="pt-2">
                <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#00254e] text-white text-xs font-bold hover:bg-[#00346e] transition-all">
                    <span>Lihat Daftar Perjalanan</span>
                </a>
            </div>
        </div>
    @else
        <!-- Split Layout: Table on Left & Active Review on Right -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Table (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-[#c3c6d1]/40 shadow-xs overflow-hidden">
                <div class="p-3.5 border-b border-[#c3c6d1]/20 flex items-center justify-between text-xs">
                    <span class="font-bold text-[#00254e]">Daftar Antrean Permohonan</span>
                    <span class="text-[#737780]">{{ $pendingRequests->count() }} Permohonan</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-[#f0f3ff] text-[#737780] uppercase tracking-wider text-[10px] font-bold border-b border-[#c3c6d1]/30">
                                <th class="py-3 px-3.5">KARYAWAN</th>
                                <th class="py-3 px-3">RUTE</th>
                                <th class="py-3 px-3">TANGGAL</th>
                                <th class="py-3 px-3">BIAYA</th>
                                <th class="py-3 px-3">TAHAP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#c3c6d1]/20" id="approvals-table-body">
                            @foreach($pendingRequests as $index => $req)
                            <tr class="{{ $index === 0 ? 'bg-[#f0f3ff]/70 border-l-4 border-l-[#00254e]' : 'hover:bg-[#f8f9ff]' }} cursor-pointer transition-colors trip-item"
                                onclick="selectTrip(this)"
                                data-id="{{ $req->id }}"
                                data-name="{{ optional($req->traveler)->name ?? 'Pegawai' }}"
                                data-code="{{ $req->request_code ?? $req->id }}"
                                data-role="{{ optional($req->traveler)->job_title ?? 'Software Engineer' }}"
                                data-dept="{{ optional($req->traveler)->department ?? 'Engineering' }}"
                                data-route="{{ $req->origin }} &rarr; {{ $req->destination }}"
                                data-dates="{{ \Carbon\Carbon::parse($req->departure_date)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($req->return_date)->translatedFormat('d M Y') }}"
                                data-cost="Rp {{ number_format($req->total_cost, 0, ',', '.') }}"
                                data-purpose="{{ $req->purpose_description ?? $req->purpose }}"
                                data-policy="{{ $req->policy_status === 'compliant' ? 'Sesuai Kebijakan' : 'Dispensasi' }}"
                                data-stage="{{ $req->approval_stage }}"
                                data-tier="{{ $req->waterfall_tier['name'] ?? 'Tier 1' }}"
                            >
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-[#00254e]">{{ optional($req->traveler)->name ?? 'Pegawai' }}</div>
                                    <div class="text-[10px] text-[#737780]">{{ optional($req->traveler)->department ?? 'Divisi' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-[#111c2d]">{{ $req->origin_code }} &rarr; {{ $req->dest_code }}</div>
                                    <div class="text-[10px] font-mono text-[#737780]">#{{ $req->request_code ?? $req->id }}</div>
                                </td>
                                <td class="py-3 px-3 text-[#43474f]">
                                    {{ \Carbon\Carbon::parse($req->departure_date)->translatedFormat('d M') }}
                                </td>
                                <td class="py-3 px-3 font-bold text-[#111c2d]">
                                    Rp {{ number_format($req->total_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-[#43474f]">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#f0f3ff] text-[#00254e]">
                                        {{ $req->approval_stage }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Side: Active Decision Card (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-[#c3c6d1]/40 shadow-xs p-5 space-y-4">
                <!-- Header -->
                <div class="flex items-start justify-between pb-3 border-b border-[#c3c6d1]/20">
                    <div>
                        <h2 class="text-base font-bold font-['Plus_Jakarta_Sans'] text-[#111c2d] flex items-center gap-2">
                            <span id="panel-traveler-name">{{ optional($firstTrip->traveler)->name ?? 'Andi Pratama' }}</span>
                            <span id="panel-request-code" class="text-xs font-mono font-normal text-[#737780]">#{{ $firstTrip->request_code ?? $firstTrip->id }}</span>
                        </h2>
                        <p id="panel-role-dept" class="text-xs text-[#737780] mt-0.5">
                            {{ optional($firstTrip->traveler)->job_title ?? 'Pegawai' }} &bull; {{ optional($firstTrip->traveler)->department ?? 'Engineering' }}
                        </p>
                    </div>
                    <span id="panel-stage-badge" class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#f0f3ff] text-[#00254e] border border-[#c3c6d1]/40">
                        {{ $firstTrip->approval_stage }}
                    </span>
                </div>

                <!-- Route Summary Box -->
                <div class="p-4 rounded-xl bg-[#f0f3ff] border border-[#c3c6d1]/40 space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <div id="panel-route" class="text-xs font-bold text-[#00254e]">{{ $firstTrip->origin }} &rarr; {{ $firstTrip->destination }}</div>
                            <div id="panel-dates" class="text-[11px] text-[#737780] mt-0.5">
                                {{ \Carbon\Carbon::parse($firstTrip->departure_date)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($firstTrip->return_date)->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div id="panel-cost" class="text-base font-extrabold font-['Plus_Jakarta_Sans'] text-[#00254e]">
                                Rp {{ number_format($firstTrip->total_cost, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-[#737780]">Total Biaya</div>
                        </div>
                    </div>

                    <div id="panel-purpose" class="text-xs text-[#43474f] italic pt-1 border-t border-[#c3c6d1]/20">
                        &ldquo;{{ $firstTrip->purpose_description ?? $firstTrip->purpose }}&rdquo;
                    </div>
                </div>

                <!-- Decision Form (Wired to dynamic action) -->
                <form id="decision-form" action="{{ route('approvals.decision', $firstTrip->id) }}" method="POST" class="space-y-3 pt-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#111c2d] mb-1">
                            Catatan Persetujuan / Alasan <span class="font-normal text-[#737780]">(Opsional)</span>
                        </label>
                        <textarea
                            name="notes"
                            rows="2"
                            placeholder="Tuliskan catatan disposisi atau instruksi untuk pemohon..."
                            class="w-full p-2.5 bg-white border border-[#c3c6d1]/60 rounded-xl text-xs outline-none focus:border-[#00677e]"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <button
                            type="submit"
                            name="action"
                            value="reject"
                            onclick="return confirm('Apakah Anda yakin ingin menolak permohonan dinas ini?');"
                            class="w-full py-2.5 rounded-xl bg-white border border-red-200 text-xs font-semibold text-red-700 hover:bg-red-50 transition-all cursor-pointer"
                        >
                            Tolak
                        </button>
                        <button
                            type="submit"
                            name="action"
                            value="approve"
                            class="w-full py-2.5 rounded-xl bg-[#00254e] text-white text-xs font-bold hover:bg-[#00346e] transition-all shadow-xs cursor-pointer"
                        >
                            Setujui Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function selectTrip(row) {
    const rows = document.querySelectorAll('#approvals-table-body tr');
    rows.forEach(r => {
        r.classList.remove('bg-[#f0f3ff]/70', 'border-l-4', 'border-l-[#00254e]');
        r.classList.add('hover:bg-[#f8f9ff]');
    });
    row.classList.remove('hover:bg-[#f8f9ff]');
    row.classList.add('bg-[#f0f3ff]/70', 'border-l-4', 'border-l-[#00254e]');

    const id = row.dataset.id;
    document.getElementById('panel-traveler-name').textContent = row.dataset.name;
    document.getElementById('panel-request-code').textContent = '#' + row.dataset.code;
    document.getElementById('panel-role-dept').textContent = row.dataset.role + ' • ' + row.dataset.dept;
    document.getElementById('panel-stage-badge').textContent = row.dataset.stage;
    document.getElementById('panel-route').innerHTML = row.dataset.route;
    document.getElementById('panel-dates').innerHTML = row.dataset.dates;
    document.getElementById('panel-cost').textContent = row.dataset.cost;
    document.getElementById('panel-purpose').textContent = '“' + row.dataset.purpose + '”';

    const form = document.getElementById('decision-form');
    form.action = '/approvals/' + id + '/decision';
}
</script>
@endsection
