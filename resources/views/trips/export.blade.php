
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Travel Authorization Pass</title>
    <style>
        @page {
            margin: 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #263238;
            font-size: 11px;
            line-height: 1.6;
        }

        .header {
            border-bottom: 3px solid #344f46;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #344f46;
        }

        .muted {
            color: #66756e;
            font-size: 10px;
        }

        h2 {
            margin-bottom: 4px;
        }

        .section {
            margin-top: 22px;
            font-size: 13px;
            font-weight: bold;
            color: #344f46;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #dce3df;
            padding: 9px;
            text-align: left;
            vertical-align: top;
        }

        th {
            width: 34%;
            background: #f0f4f1;
        }

        .total {
            margin-top: 18px;
            padding: 14px;
            border-left: 4px solid #344f46;
            background: #f0f4f1;
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #344f46;
        }

        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #dce3df;
            font-size: 9px;
            color: #66756e;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">TRAVELSYS</div>
        <div>Nusantara Technology Group</div>
        <div class="muted">Travel Authorization Pass</div>
    </div>

    <h2>Dokumen Perjalanan Dinas</h2>
    <div class="muted">
        Nomor pengajuan: {{ $trip->request_code ?? $trip->id }}
    </div>

    <div class="section">Informasi Karyawan</div>
    <table>
        <tr>
            <th>Nama</th>
            <td>{{ $trip->traveler->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Departemen</th>
            <td>{{ $trip->traveler->department ?? '-' }}</td>
        </tr>
        <tr>
            <th>Cost Center</th>
            <td>{{ $trip->cost_center ?? '-' }}</td>
        </tr>
        <tr>
            <th>Keperluan</th>
            <td>{{ $trip->purpose_title ?? '-' }}</td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td>{{ $trip->purpose_description ?? '-' }}</td>
        </tr>
    </table>

    <div class="section">Rute dan Jadwal</div>
    <table>
        <tr>
            <th>Kota Asal</th>
            <td>{{ $trip->origin ?? $trip->origin_code ?? '-' }}</td>
        </tr>
        <tr>
            <th>Kota Tujuan</th>
            <td>{{ $trip->destination ?? $trip->dest_code ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Berangkat</th>
            <td>{{ $trip->departure_date ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Kembali</th>
            <td>{{ $trip->return_date ?? '-' }}</td>
        </tr>
    </table>

    <div class="section">Rincian Biaya</div>
    <table>
        <tr>
            <th>Tiket Pesawat</th>
            <td>Rp {{ number_format((float) $trip->flight_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Hotel</th>
            <td>Rp {{ number_format((float) $trip->hotel_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Transportasi / Biaya Lain</th>
            <td>Rp {{ number_format((float) $trip->transit_cost, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="total">
        <div class="muted">TOTAL ESTIMASI BIAYA</div>
        <div class="amount">
            Rp {{ number_format((float) $trip->total_cost, 0, ',', '.') }}
        </div>
    </div>

    <div class="section">Status Pengajuan</div>
    <table>
        <tr>
            <th>Status</th>
            <td>{{ strtoupper(str_replace('_', ' ', $trip->overall_status ?? '-')) }}</td>
        </tr>
        <tr>
            <th>Tahap Persetujuan</th>
            <td>{{ $trip->approval_stage ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status Kebijakan</th>
            <td>{{ $trip->policy_status ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Dokumen dibuat secara elektronik oleh TravelSys.
        Status persetujuan mengikuti data aktual sistem.
        Dokumen ini tidak menggantikan tanda tangan atau
        sertifikasi digital yang terverifikasi.
    </div>
</body>
</html>
