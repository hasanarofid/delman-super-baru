<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monev Dapodik (BOSP)</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #1a202c;
        }
        .header h3 {
            margin: 0;
            font-size: 13px;
            font-weight: normal;
            color: #4a5568;
        }
        .filter-info {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .filter-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-info td {
            font-size: 11px;
            padding: 2px 4px;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-cards table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-cards td {
            width: 25%;
            padding: 8px;
            background: #edf2f7;
            border: 1px solid #cbd5e0;
            text-align: center;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #718096;
            font-weight: bold;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #2b6cb0;
            margin-top: 3px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2d3748;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 4px solid #3182ce;
            padding-left: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #2b6cb0;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #2b6cb0;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
        }
        .badge-success { background-color: #38a169; }
        .badge-warning { background-color: #d69e2e; }
        .badge-danger { background-color: #e53e3e; }
        .badge-info { background-color: #3182ce; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .page-break { page-break-before: always; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #718096;
        }
    </style>
</head>
<body>

    <!-- Header / Cop -->
    <div class="header">
        <h2>LAPORAN MONITORING DAN EVALUASI DAPODIK (BOSP)</h2>
        <h3>Dinas Pendidikan dan Kebudayaan Provinsi Banten — DELMAN SUPER</h3>
    </div>

    <!-- Info Filter -->
    <div class="filter-info">
        <table>
            <tr>
                <td width="15%"><strong>Tahun:</strong> {{ $year !== 'all' ? $year : 'Semua Tahun' }}</td>
                <td width="20%"><strong>Bulan:</strong> {{ $month !== 'all' ? $month : 'Semua Bulan' }}</td>
                <td width="35%"><strong>Kabupaten/Kota:</strong> {{ $selectedKabupatenName }}</td>
                <td width="30%"><strong>Jenjang Access:</strong> {{ $targetJenjang }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Metrics -->
    <div class="summary-cards">
        <table>
            <tr>
                <td width="33%">
                    <div class="summary-title">Keterlibatan Pengawas</div>
                    <div class="summary-value">{{ $pengawasSudahLaporCount }} / {{ $totalPengawas }}</div>
                    <div style="font-size: 9px; color: #4a5568;">{{ $persentasePengawasLapor }}% Lapor</div>
                </td>
                <td width="33%">
                    <div class="summary-title">Sekolah Ter-Monev</div>
                    <div class="summary-value">{{ $totalSekolahDimonev }} / {{ $totalSekolahBinaanWilayah }}</div>
                    <div style="font-size: 9px; color: #4a5568;">{{ $persentaseSekolahWilayah }}% Capaian</div>
                </td>
                <td width="34%">
                    <div class="summary-title">Total Siswa Riil</div>
                    <div class="summary-value">{{ number_format($totalSiswaRiil, 0, ',', '.') }}</div>
                    <div style="font-size: 9px; color: #4a5568;">Siswa Terdaftar</div>
                </td>
            </tr>
            <tr>
                <td style="margin-top: 5px; background: #fefcbf; border-color: #ecc94b;">
                    <div class="summary-title" style="color: #975a16;">Data Siswa Lebih (+)</div>
                    <div class="summary-value" style="color: #b7791f;">+{{ number_format($sekolahSelisihLebih, 0, ',', '.') }}</div>
                    <div style="font-size: 9px; color: #975a16;">{{ $countSekolahLebih }} Sekolah</div>
                </td>
                <td style="margin-top: 5px; background: #fed7d7; border-color: #feb2b2;">
                    <div class="summary-title" style="color: #9b2c2c;">Data Siswa Kurang (-)</div>
                    <div class="summary-value" style="color: #c53030;">-{{ number_format($sekolahSelisihKurang, 0, ',', '.') }}</div>
                    <div style="font-size: 9px; color: #9b2c2c;">{{ $countSekolahKurang }} Sekolah</div>
                </td>
                <td style="margin-top: 5px;">
                    <div class="summary-title">Total Realisasi BOSP</div>
                    <div class="summary-value" style="font-size: 12px;">Rp {{ number_format($totalRealisasiBosp, 0, ',', '.') }}</div>
                    <div style="font-size: 9px; color: #4a5568;">Realisasi Funds</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel 1: Rekap Keterlibatan Pengawas -->
    <div class="section-title">1. Rekapitulasi Keterlibatan Laporan Pengawas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="35%">Nama Pengawas</th>
                <th width="20%">NIP / Identitas</th>
                <th width="13%" class="text-center">Total Binaan</th>
                <th width="14%" class="text-center">Sudah Monev</th>
                <th width="14%" class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapKepatuhanPengawas as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $p['nama'] }}</strong></td>
                    <td>{{ $p['nip'] }}</td>
                    <td class="text-center">{{ $p['total_binaan'] }} Sekolah</td>
                    <td class="text-center">{{ $p['sudah_monev'] }} Sekolah</td>
                    <td class="text-center"><strong>{{ $p['persentase'] }}%</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pengawas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Page Break for Detail Monev -->
    <div class="page-break"></div>

    <div class="section-title">2. Detail Data Monev BOSP Sekolah</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="16%">Nama Sekolah</th>
                <th width="13%">Pengawas</th>
                <th width="11%">Kabupaten/Kota</th>
                <th width="6%" class="text-center">Kls 10</th>
                <th width="6%" class="text-center">Kls 11</th>
                <th width="6%" class="text-center">Kls 12</th>
                <th width="7%" class="text-center">Siswa Riil</th>
                <th width="7%" class="text-center">Siswa Dinas</th>
                <th width="9%">Status Data</th>
                <th width="16%">Catatan Monev</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monevList as $idx => $row)
                @php
                    $selisih = $row->total_siswa_riil - $row->siswa_dinas_bos;
                    $statusText = 'Sesuai';
                    $badgeClass = 'badge-success';
                    if ($selisih > 0) {
                        $statusText = 'Lebih (+' . $selisih . ')';
                        $badgeClass = 'badge-warning';
                    } elseif ($selisih < 0) {
                        $statusText = 'Kurang (' . $selisih . ')';
                        $badgeClass = 'badge-danger';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $row->sekolah->nama_sekolah ?? '-' }}</strong></td>
                    <td>{{ $row->pengawas->name ?? '-' }}</td>
                    <td>{{ $row->sekolah->kabupaten->nama_kabupaten ?? ($row->sekolah->kota ?? '-') }}</td>
                    <td class="text-center">{{ $row->siswa_kelas_10 }}</td>
                    <td class="text-center">{{ $row->siswa_kelas_11 }}</td>
                    <td class="text-center">{{ $row->siswa_kelas_12 }}</td>
                    <td class="text-center"><strong>{{ number_format($row->total_siswa_riil, 0, ',', '.') }}</strong></td>
                    <td class="text-center">{{ number_format($row->siswa_dinas_bos, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        {{ $row->catatan_observasi ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data monev BOSP.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem DELMAN SUPER pada: {{ date('d-m-Y H:i:s') }}
    </div>

</body>
</html>
