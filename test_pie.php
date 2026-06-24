<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jenjang_filter = 'SMA';

$query = \App\Models\UmpanbalikT::selectRaw("
        COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, melakukan pengawasan di Sekolah' THEN 1 END) as sekolah,
        COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, melakukan pengawasan secara virtual' THEN 1 END) as by_virtual,
        COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Ya, pengawasan digabungkan dengan sekolah lain' THEN 1 END) as gabungan,
        COUNT(CASE WHEN tanggapan_umpanbalik_t.jawaban_4 = 'Tidak melakukan pengawasan' THEN 1 END) as tidak
    ")
    ->join('tanggapan_umpanbalik_t', 'tanggapan_umpanbalik_t.id_umpanbalik', '=', 'umpanbalik_t.id')
    ->join('rencakakerja_t as rt', 'rt.id', '=', 'umpanbalik_t.id_pelaporan');

if ($jenjang_filter !== 'all') {
    $query->whereExists(function($q) use ($jenjang_filter) {
        $q->select(\Illuminate\Support\Facades\DB::raw(1))
          ->from('sekolahbinaan_t')
          ->join('sekolah_m', 'sekolah_m.id', '=', 'sekolahbinaan_t.id_sekolah')
          ->whereRaw('sekolahbinaan_t.id_pengawas = umpanbalik_t.id_pengawas')
          ->where('sekolah_m.nama_sekolah', 'LIKE', '%' . $jenjang_filter . '%');
    });
}

// Log query
echo "Query: " . $query->toSql() . "\n";
$data = $query->first();

echo "Result:\n";
print_r($data->toArray());

