<?php

$controllers = [
    'app/Http/Controllers/ListumpanbalikController.php',
    'app/Http/Controllers/LayanandibutuhkanController.php',
    'app/Http/Controllers/SaranperbaikanController.php',
    'app/Http/Controllers/DokumentasipendampinganController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(
            "applyStakeholderFilter(\$queryPengawas, 'kabupaten_id', null, 'self')",
            "applyStakeholderFilter(\$queryPengawas, 'kabupaten_id', 'nama_sekolah', 'self', 'sekolah')",
            $content
        );
        file_put_contents($file, $content);
        echo "Updated \$queryPengawas in $file\n";
    }
}
