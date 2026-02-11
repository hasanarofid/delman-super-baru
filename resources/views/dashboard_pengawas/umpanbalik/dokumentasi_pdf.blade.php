<!DOCTYPE html>
<html>
<head>
    <title>Laporan Dokumentasi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table, th, td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .copyright-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            font-style: italic;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="copyright-footer">
        File ini digenerate dari Delman Super
    </div>
    <h1>Laporan Dokumentasi Pendampingan</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pendampingan</th>
                <th>Foto Bukti 1</th>
                <th>Sekolah</th>
                <th>Program Kerja</th>
                <th>Rencana Tindak Lanjut (RTL)</th>
                <th>Catatan RTL</th>
                <th>Pengawas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['tanggal'] }}</td>
                <td>
                    @if(!empty($row['foto_base64']))
                    <img src="{{ $row['foto_base64'] }}" alt="Foto Bukti 1" width="100px">
                    @else
                    -
                    @endif
                </td>
                <td>{{ $row['nama_sekolah'] }}</td>
                <td>{{ $row['program'] }}</td>
                <td>{{ $row['rtl_status'] }}</td>
                <td>{{ $row['catatan_rtl'] }}</td>
                <td>{{ $row['pengawas'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <br><br>
    
    <div style="width: 100%; text-align: right;">
        <div style="display: inline-block; text-align: left; padding-right: 50px;">
            <p>Mengetahui,</p>
            <p>Pengawas Sekolah</p>
            <br><br>
            <p style="text-align: left; font-size: 24px; font-weight: bold; color: #000;">#</p>
            <br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $user->name }}</p>
            <p>NIP. {{ $user->nip }}</p>
        </div>
    </div>
</body>
</html>
