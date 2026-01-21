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
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ExportDelmanSuper implements FromCollection, WithMapping, WithHeadings, WithStyles, WithColumnWidths, WithEvents
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
            $binaan = SekolahbinaanT::where('id_pengawas', $user->id)
                ->with('sekolah')
                ->get();
            
            // Get all school names as comma-separated list
            $sekolahList = $binaan->map(function($item, $index) {
                return ($index + 1) . ". " . ($item->sekolah->nama_sekolah ?? '-');
            })->implode("\n");
            
            // If no schools, show '-'
            if ($binaan->isEmpty()) {
                $sekolahList = '-';
            }
            
            $data->push([
                'no' => $no,
                'user' => $user,
                'sekolah_list' => $sekolahList,
            ]);
            
            $no++;
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pengawas',
            'NIP',
            'Sekolah Binaan',
        ];
    }

    public function map($row): array
    {
        return [
            $row['no'],
            $row['user']->name,
            $row['user']->nip ?? '-',
            $row['sekolah_list'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Center alignment for No column
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
        
        // Vertical alignment for all cells
        $sheet->getStyle('A:D')->getAlignment()->setVertical('top');
        
        // Wrap text for Sekolah Binaan column
        $sheet->getStyle('D:D')->getAlignment()->setWrapText(true);
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 40,  // Nama Pengawas
            'C' => 20,  // NIP
            'D' => 60,  // Sekolah Binaan
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Force NIP column (Column C) to be stored as STRING explicitly
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('C' . $row)->getValue();
                    if ($cellValue && $cellValue !== '-') {
                        $sheet->getCell('C' . $row)->setValueExplicit(
                            $cellValue,
                            DataType::TYPE_STRING
                        );
                    }
                }
                
                // Format NIP column as TEXT
                $sheet->getStyle('C2:C' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                
                // Add borders to all data cells
                $sheet->getStyle('A1:D' . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                // Auto-adjust row heights
                for ($row = 2; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            },
        ];
    }
}

