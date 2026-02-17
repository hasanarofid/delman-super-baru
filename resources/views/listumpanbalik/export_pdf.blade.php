<!DOCTYPE html>
<html>
<head>
    <title>Daftar Umpan Balik</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
        }
        .logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
        }
        .profile-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .profile-section table {
            width: 100%;
        }
        .profile-section td {
            padding: 2px 5px;
        }
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.main-table th, table.main-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        table.main-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer table {
            width: 100%;
        }
        .signature-section {
            margin-top: 50px;
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = public_path('delmansupernew.png');
            $logoBase64 = null;
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($logoData);
            }
        @endphp
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo">
        @endif
        <h2>Daftar Umpan Balik Rencana Kerja Pengawas Sekolah</h2>
        <p>Periode: {{ $bln != 'all' ? $bln : 'Semua Bulan' }} {{ $tahun != 'all' ? $tahun : 'Semua Tahun' }}</p>
    </div>

    @if($pengawasProfile)
    <div class="profile-section">
        <table>
            <tr>
                <td width="15%"><strong>Nama</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $pengawasProfile->name }}</td>
                <td width="15%"><strong>Jabatan</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $pengawasProfile->jenjang_jabatan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>NIP</strong></td>
                <td>:</td>
                <td>{{ $pengawasProfile->nip }}</td>
                <td><strong>Golongan</strong></td>
                <td>:</td>
                <td>{{ $pengawasProfile->gol_ruang ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>No HP</strong></td>
                <td>:</td>
                <td colspan="4">{{ $pengawasProfile->profile->no_telp ?? ($pengawasProfile->no_telp ?? '-') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Sekolah</th>
                <th width="15%">Kepala Sekolah</th>
                <th width="20%">Program Kerja</th>
                <th width="10%">Kategori</th>
                <th width="20%">Catatan RTL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            @php
                $cariguru = \App\GuruM::find($row->id_user);
                $nama_sekolah = '-';
                if ($row->id_user == 0 && $row->id_user_pengawas != 0) {
                    $nama_sekolah = 'Mandiri (Refleksi Pengawas)';
                } elseif ($cariguru) {
                    $sekolahs = \App\SekolahM::find($cariguru->sekolah_id);
                    $nama_sekolah = $sekolahs ? $sekolahs->nama_sekolah : '-';
                }

                $kepala_sekolah = '-';
                if ($row->id_user == 0 && $row->id_user_pengawas != 0) {
                    $kepala_sekolah = $row->user_pengawas->name ?? '-';
                } elseif ($cariguru) {
                    $kepala_sekolah = $cariguru->nama;
                }

                $kategori_program = '-';
                if ($row->rencanakerja && $row->rencanakerja->kategoriprogram) {
                    $kategori_program = $row->rencanakerja->kategoriprogram->name;
                }
            @endphp
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $row->created_at->format('d-m-Y') }}</td>
                <td>{{ $nama_sekolah }}</td>
                <td>{{ $kepala_sekolah }}</td>
                <td>{{ $row->rencanakerja->nama_program_kerja ?? '-' }}</td>
                <td>{{ $kategori_program }}</td>
                <td>{{ $row->catatan_rtl ?? 'Belum Ada RTL' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: left;">
            <p>{{ now()->format('d F Y') }}</p>
            <p>Pengawas Sekolah,</p>
            <br>
            <p style="font-size: 24px; font-weight: bold; color: #000; margin: 0;">#</p>
            <br>
            <p><strong><span style="text-decoration: underline;">{{ $pengawasProfile ? $pengawasProfile->name : '..........................................' }}</span></strong></p>
            <p>NIP. {{ $pengawasProfile ? $pengawasProfile->nip : '..........................................' }}</p>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <table>
            <tr>
                <td>Dokumen ini dibuat oleh <strong>Delman Super</strong></td>
                <td align="right">Digital Generate: {{ $generateDate }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
