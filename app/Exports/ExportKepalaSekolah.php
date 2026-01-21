<?php

namespace App\Exports;

use App\GuruM;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportKepalaSekolah implements FromCollection, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    protected $gurus;

    public function __construct($gurus)
    {
        $this->gurus = $gurus;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->gurus;
    }

    /**
     * @var GuruM $guru
     */
    public function map($guru): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $guru->sekolah ? $guru->sekolah->nama_sekolah : '-',
            $guru->sekolah ? $guru->sekolah->npsn : '-',
            $guru->nama ?? '-',
            $guru->nip ?? '-',
            $guru->jabatan ?? '-',
            $guru->no_telp ?? '-',
            $guru->alamat_lengkap ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Sekolah',
            'NPSN',
            'Nama Kepala Sekolah',
            'NIP',
            'Jabatan',
            'No Telpon',
            'Alamat',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // No
            'B' => 40, // Nama Sekolah
            'C' => 15, // NPSN
            'D' => 30, // Nama Kepala Sekolah
            'E' => 20, // NIP
            'F' => 20, // Jabatan
            'G' => 18, // No Telpon
            'H' => 40, // Alamat
        ];
    }
}

