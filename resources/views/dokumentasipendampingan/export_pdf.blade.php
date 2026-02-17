<!DOCTYPE html>
<html>
<head>
    <title>Laporan Dokumentasi Pendampingan</title>
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
            table-layout: fixed; /* Important for long text and image alignment */
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
        <h2>Laporan Dokumentasi Pendampingan</h2>
        <p>Periode: {{ $bln != 'all' ? $bln : 'Semua Bulan' }} {{ $tahun != 'all' ? $tahun : 'Semua Tahun' }}</p>
        <p>Kategori Program: {{ $kategoriName }}</p>
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
                <th width="4%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Foto Bukti</th>
                <th width="18%">Sekolah</th>
                <th width="12%">Kategori</th>
                <th width="18%">Program Kerja</th>
                <th width="21%">Catatan RTL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $row['tanggal'] }}</td>
                <td align="center">
                    @if(!empty($row['foto_base64']))
                        <img src="{{ $row['foto_base64'] }}" width="100%">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $row['nama_sekolah'] }}</td>
                <td>{{ $row['kategori'] ?? '-' }}</td>
                <td>{{ $row['program'] }}</td>
                <td>
                    @if(!empty($row['catatan_rtl']) && $row['catatan_rtl'] != '-')
                        {{ $row['catatan_rtl'] }}
                    @else
                        <em>RTL belum dibuat</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Pengawas Sekolah,</p>
            <div style="font-size: 24px; font-weight: bold; margin: 10px 0;">#</div>
            <p><strong><u>{{ $pengawasProfile ? $pengawasProfile->name : '..........................................' }}</u></strong></p>
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
