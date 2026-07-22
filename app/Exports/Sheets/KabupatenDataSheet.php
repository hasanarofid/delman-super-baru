<?php

namespace App\Exports\Sheets;

use App\Kabupaten;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class KabupatenDataSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        return Kabupaten::all();
    }

    public function headings(): array
    {
        return [
            'ID Kabupaten',
            'Nama Kabupaten',
        ];
    }

    public function map($kabupaten): array
    {
        return [
            $kabupaten->id,
            $kabupaten->nama_kabupaten,
        ];
    }

    public function title(): string
    {
        return 'Data Kabupaten';
    }
}
