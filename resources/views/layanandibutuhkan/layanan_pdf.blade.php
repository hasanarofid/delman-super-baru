<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kebutuhan Layanan Pengawas Sekolah</title>
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
            table-layout: fixed;
        }
        table.main-table th, table.main-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            vertical-align: top;
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
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: left;
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
                $logoDataBytes = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($logoDataBytes);
            }
        @endphp
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo">
        @endif
        <h2>Daftar Kebutuhan Layanan Pengawas Sekolah</h2>
        <p>Periode: {{ $generateDate }}</p>
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
                <td>{{ $pengawasProfile->pangkat ?? '-' }} - {{ $pengawasProfile->gol_ruang ?? '-' }}</td>
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
                <th width="5%">No</th>
                <th width="35%">Sekolah</th>
                <th width="60%">Layanan yang dibutuhkan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $row['nama_sekolah'] }}</td>
                <td>{{ $row['layanan'] }}</td>
            </tr>
            @endforeach
            @if(count($data) == 0)
            <tr>
                <td colspan="3" align="center">Tidak ada data</td>
            </tr>
            @endif
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 40px; border: none; page-break-inside: avoid;">
        <tr>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none; height: 55px;">
                @if($pengawasProfile && !empty($pengawasProfile->nama_atasan))
                    <p style="margin: 0; padding: 0;">Mengetahui,<br><strong>{{ $pengawasProfile->jabatan_atasan ?? 'Atasan Langsung' }}</strong></p>
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
                @if($pengawasProfile && !empty($pengawasProfile->nama_atasan))
                    <p style="margin-bottom: 0;"><strong><u>{{ $pengawasProfile->nama_atasan }}</u></strong></p>
                    <p style="margin-top: 3px;">NIP. {{ $pengawasProfile->nip_atasan ?? '-' }}</p>
                @endif
            </td>
            <td style="width: 50%; text-align: left; vertical-align: top; border: none;">
                <p style="margin-bottom: 0;"><strong><u>{{ $pengawasProfile ? $pengawasProfile->name : '..........................................' }}</u></strong></p>
                <p style="margin-top: 3px;">NIP. {{ $pengawasProfile ? $pengawasProfile->nip : '..........................................' }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>Dokumen ini dibuat secara otomatis oleh <strong>Delman Super</strong></td>
                <td align="right">Digital Generate: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
