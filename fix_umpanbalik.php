<?php

$controllers = [
    'app/Http/Controllers/LayanandibutuhkanController.php',
    'app/Http/Controllers/SaranperbaikanController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace(
            "applyStakeholderFilter(\$post, 'umpanBalikT.pengawasnama.kabupaten_id', null, 'umpanBalikT.pengawasnama')",
            "applyStakeholderFilter(\$post, 'umpanBalikT.pengawasnama.kabupaten_id', 'nama_sekolah', null, 'umpanBalikT.rencanakerja.sekolah')",
            $content
        );
        $content = str_replace(
            "applyStakeholderFilter(\$query, 'umpanBalikT.pengawasnama.kabupaten_id', null, 'umpanBalikT.pengawasnama')",
            "applyStakeholderFilter(\$query, 'umpanBalikT.pengawasnama.kabupaten_id', 'nama_sekolah', null, 'umpanBalikT.rencanakerja.sekolah')",
            $content
        );
        file_put_contents($file, $content);
        echo "Updated TanggapanUmpanbalik in $file\n";
    }
}
