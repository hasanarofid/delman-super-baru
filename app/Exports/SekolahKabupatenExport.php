<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SekolahDataSheet;
use App\Exports\Sheets\KabupatenDataSheet;

class SekolahKabupatenExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new SekolahDataSheet();
        $sheets[] = new KabupatenDataSheet();

        return $sheets;
    }
}
