<?php

namespace App\Imports;

use App\SekolahM;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SekolahKabupatenImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check if id_sekolah exists, since headers will be snake_case (id_sekolah, npsn, nama_sekolah, id_kabupaten, nama_kabupaten)
            // By default WithHeadingRow converts to lowercase with underscores
            $id = $row['id_sekolah'] ?? null;
            $kabupatenId = $row['id_kabupaten'] ?? null;

            if ($id && $kabupatenId) {
                $sekolah = SekolahM::find($id);
                if ($sekolah) {
                    $sekolah->kabupaten_id = $kabupatenId;
                    $sekolah->save();
                }
            }
        }
    }
}
