<!DOCTYPE html>
<html>
<head>
    <title>Rencana Kerja Pengawas Sekolah</title>
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
            border-left: 4px solid #7367f0;
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
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            color: #fff;
        }
        .bg-primary { background-color: #7367f0; }
        .bg-success { background-color: #28c76f; }
        .bg-info { background-color: #00cfe8; }
        .bg-danger { background-color: #ea5455; }
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
        <h2>Daftar Rencana Kerja Pengawas Sekolah</h2>
        <p style="font-size: 12px; font-weight: bold; margin-top: 5px;">Periode: {{ $periode }}</p>
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

    <div class="section-title">Detail Rencana Kerja</div>
    
    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Bulan - Tahun</th>
                <th width="20%">Program Kerja</th>
                <th width="20%">Jenis / Aspek</th>
                <th width="20%">Deskripsi Alasan</th>
                <th>Sekolah Sasaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                @php
                    $sekolahNames = [];
                    if ($row->is_mandiri == 1) {
                        $sekolah_display = 'Mandiri (Refleksi)';
                    } else {
                        $sekolahIds = explode(',', $row->sekolah_id);
                        $sekolahs = \App\SekolahM::whereIn('id', $sekolahIds)->get();
                        foreach ($sekolahs as $sekolah) {
                            $sekolahNames[] = $sekolah->nama_sekolah;
                        }
                        $sekolah_display = implode(', ', $sekolahNames);
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->bulan }} - {{ $row->tahun_ajaran }}</td>
                    <td>{{ $row->nama_program_kerja }}</td>
                    <td>
                        {{ $row->jenisprogram ? $row->jenisprogram->nama : '-' }}<br>
                        <small style="color: #666;">{{ $row->aspekprogram ? $row->aspekprogram->nama : '-' }}</small>
                    </td>
                    <td>{!! $row->deskripsi_permasalahan !!}</td>
                    <td>{{ $sekolah_display }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 40px; border: none; page-break-inside: avoid;">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none; height: 55px;">
                @if(!empty($user->nama_atasan))
                    <p style="margin: 0; padding: 0;">Mengetahui,<br><strong>{{ $user->jabatan_atasan ?? 'Atasan Langsung' }}</strong></p>
                @endif
            </td>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none; height: 55px;">
                <p style="margin: 0; padding: 0;">Pengawas Sekolah,</p>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: middle; border: none; height: 70px;">
            </td>
            <td style="width: 50%; text-align: left; vertical-align: middle; border: none; height: 70px; padding-left: 30px;">
                <p style="margin: 0; font-size: 14px; font-weight: bold; color: #000000;">#</p>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none;">
                @if(!empty($user->nama_atasan))
                    <p style="margin-bottom: 0;"><strong><u>{{ $user->nama_atasan }}</u></strong></p>
                    <p style="margin-top: 3px;">NIP. {{ $user->nip_atasan ?? '-' }}</p>
                @endif
            </td>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none;">
                <p style="margin-bottom: 0;"><strong><u>{{ $user->name }}</u></strong></p>
                <p style="margin-top: 3px;">NIP. {{ $user->nip }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        File ini digenerate secara otomatis dari Delman Super pada {{ $generateDate }}.
    </div>
</body>
</html>
