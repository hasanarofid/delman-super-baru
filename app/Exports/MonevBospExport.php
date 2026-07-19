<?php

namespace App\Exports;

use App\Models\MonevBosp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonevBospExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pengawasId;

    public function __construct($pengawasId)
    {
        $this->pengawasId = $pengawasId;
    }

    public function collection()
    {
        return MonevBosp::with('sekolah')
            ->where('pengawas_id', $this->pengawasId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Bulan',
            'Tahun',
            'Nama Sekolah',
            'Status Ijop',
            'Siswa Kelas 10',
            'Siswa Kelas 11',
            'Siswa Kelas 12',
            'Total Siswa Riil',
            'Siswa Dinas/BOS',
            'Realisasi BOSP (Rp)',
            'Catatan Observasi',
            'Tanggal Submit'
        ];
    }

    public function map($row): array
    {
        return [
            $row->bulan,
            $row->tahun,
            $row->sekolah ? $row->sekolah->nama_sekolah : '-',
            $row->status_ijop,
            $row->siswa_kelas_10,
            $row->siswa_kelas_11,
            $row->siswa_kelas_12,
            $row->total_siswa_riil,
            $row->siswa_dinas_bos,
            $row->realisasi_bosp,
            $row->catatan_observasi,
            $row->created_at->format('d-m-Y H:i')
        ];
    }
}
