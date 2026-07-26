<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonevBospDashboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $monevList;
    private $rowNumber = 0;

    public function __construct($monevList)
    {
        $this->monevList = $monevList;
    }

    public function collection()
    {
        return $this->monevList;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kabupaten / Kota',
            'Nama Pengawas',
            'Nama Sekolah',
            'Bulan',
            'Tahun',
            'Kelas X',
            'Kelas XI',
            'Kelas XII',
            'Total Siswa Riil',
            'Data Cutoff Dapodik Dinas',
            'Status Data',
            'Catatan Pengawas',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $status = 'Sesuai';
        if ($row->total_siswa_riil > $row->siswa_dinas_bos) {
            $status = 'Selisih Lebih';
        } elseif ($row->total_siswa_riil < $row->siswa_dinas_bos) {
            $status = 'Selisih Kurang';
        }

        $statusData = $status;
        if (!empty($row->status_ijop)) {
            $statusData .= ' (' . $row->status_ijop . ')';
        }

        return [
            $this->rowNumber,
            $row->sekolah && $row->sekolah->kabupaten ? $row->sekolah->kabupaten->nama_kabupaten : ($row->sekolah->kota ?? '-'),
            $row->pengawas ? $row->pengawas->name : '-',
            $row->sekolah ? $row->sekolah->nama_sekolah : '-',
            $row->bulan,
            $row->tahun,
            $row->siswa_kelas_10,
            $row->siswa_kelas_11,
            $row->siswa_kelas_12,
            $row->total_siswa_riil,
            $row->siswa_dinas_bos,
            $statusData,
            $row->catatan_observasi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
