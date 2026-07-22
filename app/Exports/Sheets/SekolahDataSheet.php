<?php

namespace App\Exports\Sheets;

use App\SekolahM;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SekolahDataSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return SekolahM::with(['kabupaten', 'pengawas.pengawas'])->get();
    }

    public function headings(): array
    {
        return [
            'ID Sekolah',
            'NPSN',
            'Nama Sekolah',
            'ID Kabupaten',
            'Nama Kabupaten',
            'Nama Pengawas',
        ];
    }

    public function map($sekolah): array
    {
        return [
            $sekolah->id,
            $sekolah->npsn,
            $sekolah->nama_sekolah,
            $sekolah->kabupaten_id,
            $sekolah->kabupaten ? $sekolah->kabupaten->nama_kabupaten : '',
            $sekolah->pengawas && $sekolah->pengawas->pengawas ? $sekolah->pengawas->pengawas->name : '',
        ];
    }

    public function title(): string
    {
        return 'Data Sekolah';
    }
}
