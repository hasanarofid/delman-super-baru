<!DOCTYPE html>
<html>
<head>
    <title>Laporan Dokumentasi</title>
    <style>
        h1 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            page-break-inside: auto;
        }
        table, th, td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
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
    
    <div style="width: 100%; margin-top: 30px;">
        <div style="float: right; width: 300px; text-align: center;">
            <p style="margin-bottom: 5px;">Pengawas Sekolah,</p>
            <div style="font-size: 24px; font-weight: bold; margin: 10px 0;">#</div>
            <p style="margin-bottom: 0;"><strong><u>{{ $user->name }}</u></strong></p>
            <p style="margin-top: 5px;">NIP. {{ $user->nip }}</p>
        </div>
    </div>
</body>
</html>
