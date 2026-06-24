<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RencanaKerjaT;

$total = RencanaKerjaT::count();
$withSekolah = RencanaKerjaT::whereNotNull('sekolah_id')->count();

echo "Total RencanaKerjaT: $total\n";
echo "Total with sekolah_id: $withSekolah\n";
