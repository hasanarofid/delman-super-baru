<?php

namespace App\Exports;

use App\User;
use App\Models\SekolahbinaanT;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportPengawas implements FromCollection, WithMapping, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        $data = collect();
        $no = 1;
        foreach ($this->users as $user) {
            $binaan = SekolahbinaanT::where('id_pengawas', $user->id)->with('sekolah')->get();
            
            $rows_needed = max(4, $binaan->count());
            
            for ($i = 0; $i < $rows_needed; $i++) {
                $school = $binaan->get($i);
                $data->push([
                    'no' => ($i === 0) ? $no : '',
                    'supervisor_index' => $i, // hidden helper
                    'user' => $user,
                    'school_no' => ($i < $binaan->count()) ? ($i + 1) : '',
                    'school_name' => ($i < $binaan->count()) ? (($i + 1) . ". " . ($school->sekolah->nama_sekolah ?? '-')) : ''
                ]);
            }
            $no++;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            ['1. CABANG DINAS PENDIDIKAN DAN KEBUDAYAAN WILAYAH KABUPATEN LEBAK'],
            [
                'No',
                'Nama, NIP, Jenjang Jabatan, Pangkat, Golongan',
                'Satuan Pendidikan Sasaran Pengawasan 2026',
                ''
            ]
        ];
    }

    public function map($row): array
    {
        $info = '';
        $user = $row['user'];
        switch ($row['supervisor_index']) {
            case 0:
                $info = $user->name;
                break;
            case 1:
                $info = 'NIP. ' . $user->nip;
                break;
            case 2:
                $info = $user->jenjang_jabatan ?? '-';
                break;
            case 3:
                $info = ($user->pangkat ?? '-') . ', ' . ($user->gol_ruang ?? '-');
                break;
        }

        return [
            $row['no'],
            $info,
            $row['school_no'],
            $row['school_name']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2:D2')->getFont()->setBold(true);
        $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
        
        // Merge Col C and D for the header
        $sheet->mergeCells('C2:D2');

        $sheet->getStyle('A:D')->getAlignment()->setVertical('center');
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C:C')->getAlignment()->setHorizontal('center');
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 50,
            'C' => 10,
            'D' => 60,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Add borders
                $sheet->getStyle('A2:D' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}

