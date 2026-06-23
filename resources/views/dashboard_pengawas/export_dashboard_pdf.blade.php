<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kinerja Pengawas Sekolah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            padding-top: 10px;
        }
        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            color: #000;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .profile-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .profile-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .profile-section td {
            padding: 4px;
            vertical-align: top;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 8px;
            font-weight: bold;
            font-size: 13px;
            border-left: 4px solid #d9534f;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .content-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .chart-container {
            text-align: center;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .chart-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #eee;
        }
        .chart-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
            margin: 10px 0;
            font-weight: bold;
            color: #999;
            border: 1px dashed #ccc;
            line-height: 60px;
        }
        .page-break {
            page-break-after: always;
        }
        .summary-box {
            display: inline-block;
            width: 30%;
            border: 1px solid #ddd;
            padding: 10px;
            margin-right: 2%;
            background: #fff;
            text-align: center;
        }
        .summary-box h3 {
            margin: 0;
            font-size: 16px;
            color: #d9534f;
        }
        .summary-box p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = public_path('delmansupernew.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoBase64 = 'data:image/png;base64,' . $logoData;
            }
        @endphp
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo">
        @endif
        <h2>Dashboard Kinerja Pengawas Sekolah</h2>
        <p>Periode: {{ $bulan }} {{ $tahun }}</p>
        <p style="font-size: 10px;">Dicetak pada: {{ $generateDate }}</p>
    </div>

    <div class="profile-section">
        <table>
            <tr>
                <td width="20%"><strong>Nama Lengkap</strong></td>
                <td width="2%">:</td>
                <td width="38%">{{ $user->name }}</td>
                <td width="15%"><strong>Jabatan</strong></td>
                <td width="2%">:</td>
                <td width="23%">{{ $user->jenjang_jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIP</strong></td>
                <td>:</td>
                <td>{{ $user->nip }}</td>
                <td><strong>Golongan</strong></td>
                <td>:</td>
                <td>{{ $user->pangkat ?? '-' }} - {{ $user->gol_ruang ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>No HP</strong></td>
                <td>:</td>
                <td colspan="4">{{ $user->no_telp ?? ($user->profile->no_telp ?? '-') }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 20px; text-align: center;">
        <div class="summary-box">
            <h3>{{ $totalRencankerja }}</h3>
            <p>Total Rencana Kerja</p>
        </div>
        <div class="summary-box">
            <h3>{{ $sekolahdilayani }}</h3>
            <p>Sekolah Dilayani</p>
        </div>
    </div>

    <div class="section-title">1. Analisis Perencanaan & Umpan Balik</div>
    
    <div class="chart-container">
        <div class="chart-title">Grafik Jumlah Rencana 6 Bulan Terakhir</div>
        @if($chart_rencana)
            <img src="{{ $chart_rencana }}" class="chart-image">
        @else
            <p style="color: #999; font-style: italic;">Grafik tidak tersedia</p>
        @endif
    </div>

    <div class="chart-container">
        <div class="chart-title">Grafik Umpan Balik per Rencana Kerja</div>
        @if($chart_umpanbalik)
            <img src="{{ $chart_umpanbalik }}" class="chart-image">
        @else
            <p style="color: #999; font-style: italic;">Grafik tidak tersedia</p>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section-title">2. Profil Kompetensi & Realisasi</div>

    <div style="width: 100%;">
        <div style="width: 48%; float: left;" class="chart-container">
            <div class="chart-title">Profil Kompetensi Pengawas</div>
            @if($chart_kompetensi)
                <img src="{{ $chart_kompetensi }}" class="chart-image" style="max-height: 250px;">
            @endif
        </div>
        <div style="width: 48%; float: right;" class="chart-container">
            <div class="chart-title">Realisasi Pelaksanaan Pengawasan</div>
            @if($chart_realisasi)
                <img src="{{ $chart_realisasi }}" class="chart-image" style="max-height: 250px;">
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="chart-container">
        <div class="chart-title">Grafik Pengawasan Terkonfirmasi 6 Bulan Terakhir</div>
        @if($chart_terkonfirmasi)
            <img src="{{ $chart_terkonfirmasi }}" class="chart-image">
        @endif
    </div>

    <div class="chart-container">
        <div class="chart-title">Grafik Rencana Kerja per Raport Pendidikan</div>
        @if($chart_raport)
            <img src="{{ $chart_raport }}" class="chart-image">
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section-title">3. Analisis Kualitas Umpan Balik</div>

    <div style="width: 100%;">
        <div style="width: 32%; float: left;" class="chart-container">
            <div class="chart-title">Pengembangan Profesional</div>
            @if($chart_q1)
                <img src="{{ $chart_q1 }}" class="chart-image">
            @endif
        </div>
        <div style="width: 32%; float: left; margin-left: 2%;" class="chart-container">
            <div class="chart-title">Aspek Kompetensi</div>
            @if($chart_q2)
                <img src="{{ $chart_q2 }}" class="chart-image">
            @endif
        </div>
        <div style="width: 32%; float: right;" class="chart-container">
            <div class="chart-title">Kebermanfaatan</div>
            @if($chart_q4)
                <img src="{{ $chart_q4 }}" class="chart-image">
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="section-title">4. Detail Rencana Kerja & Sekolah Binaan</div>
    
    <div class="chart-title">Rencana Kerja Bulan Ini ({{ $bulan }})</div>
    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kategori</th>
                <th>Nama Program</th>
                <th width="20%">Jumlah Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listsekolahdilayani as $index => $item)
                @php
                    $sekolahCount = count(explode(',', $item->sekolah_id));
                    $kategori = $item->kategoriprogram ? $item->kategoriprogram->nama : '-';
                    if (preg_match('/RHK\s*\d+/i', $kategori, $matches)) {
                        $kategoriDisplay = strtoupper($matches[0]);
                    } else {
                        $kategoriDisplay = $kategori;
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $kategoriDisplay }}</td>
                    <td>{{ $item->nama_program_kerja }}</td>
                    <td>{{ $sekolahCount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="chart-title">Daftar Sekolah Binaan</div>
    <table class="content-table">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th>Nama Sekolah</th>
                <th>Alamat</th>
                <th width="20%">Jumlah Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listSekolahBinaan as $index => $sekolah)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sekolah->nama_sekolah }}</td>
                    <td>{{ $sekolah->alamat ?? '-' }}</td>
                    <td>{{ $schoolKegiatanCount[$sekolah->id] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section" style="margin-top: 60px;">
        <div class="signature-box" style="float: right; width: 300px; text-align: center;">
            <p style="margin-bottom: 10px;">Pengawas Sekolah,</p>
            <div style="font-size: 24px; font-weight: bold; margin: 40px 0;">#</div>
            <p style="margin-bottom: 0;"><strong><u>{{ $user->name }}</u></strong></p>
            <p style="margin-top: 5px;">NIP. {{ $user->nip }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Laporan ini digenerate secara otomatis melalui sistem Delman Super. Kebenaran data adalah tanggung jawab pengawas sekolah bersangkutan.
    </div>
</body>
</html>
