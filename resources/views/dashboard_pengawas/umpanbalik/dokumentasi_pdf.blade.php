<!DOCTYPE html>
<html>
<head>
    <title>Laporan Dokumentasi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Laporan Dokumentasi Pendampingan</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pendampingan</th>
                <th>Foto Bukti 1</th>
                <th>Sekolah</th>
                <th>Program Kerja</th>
                <th>Pengawas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['tanggal'] }}</td>
                <td>
                    
                    <img src="{{ route('umpanbalikfoto', $row['foto'])}}" alt="Foto Bukti 1" width="100px">
                </td>
                <td>{{ $row['nama_sekolah'] }}</td>
                <td>{{ $row['program'] }}</td>
                <td>{{ $row['pengawas'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
