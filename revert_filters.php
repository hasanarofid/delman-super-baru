<?php

$files = [
    'app/Http/Controllers/AdminController.php',
    'app/Http/Controllers/ListumpanbalikController.php',
    'app/Http/Controllers/LayanandibutuhkanController.php',
    'app/Http/Controllers/SaranperbaikanController.php',
    'app/Http/Controllers/DokumentasipendampinganController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Revert AdminController line 47: total_rencankerja_q
        $content = str_replace(
            "applyStakeholderFilter(\$total_rencankerja_q, 'pengawasnama.kabupaten_id', 'nama_sekolah', null, 'sekolah')",
            "applyStakeholderFilter(\$total_rencankerja_q, 'pengawasnama.kabupaten_id', null, 'pengawasnama')",
            $content
        );
        
        // Revert AdminController line 48 & 501: total_umpanbalik_q and query
        $content = str_replace(
            "applyStakeholderFilter(\$total_umpanbalik_q, 'pengawasnama.kabupaten_id', 'nama_sekolah', null, 'rencanakerja.sekolah')",
            "applyStakeholderFilter(\$total_umpanbalik_q, 'pengawasnama.kabupaten_id', null, 'pengawasnama')",
            $content
        );
        $content = str_replace(
            "applyStakeholderFilter(\$query, 'pengawasnama.kabupaten_id', 'nama_sekolah', null, 'rencanakerja.sekolah')",
            "applyStakeholderFilter(\$query, 'pengawasnama.kabupaten_id', null, 'pengawasnama')",
            $content
        );

        // Revert others (TanggapanUmpanbalik & Umpanbalik)
        $content = str_replace(
            "applyStakeholderFilter(\$post, 'umpanBalikT.pengawasnama.kabupaten_id', 'nama_sekolah', null, 'umpanBalikT.rencanakerja.sekolah')",
            "applyStakeholderFilter(\$post, 'umpanBalikT.pengawasnama.kabupaten_id', null, 'umpanBalikT.pengawasnama')",
            $content
        );
        $content = str_replace(
            "applyStakeholderFilter(\$query, 'umpanBalikT.pengawasnama.kabupaten_id', 'nama_sekolah', null, 'umpanBalikT.rencanakerja.sekolah')",
            "applyStakeholderFilter(\$query, 'umpanBalikT.pengawasnama.kabupaten_id', null, 'umpanBalikT.pengawasnama')",
            $content
        );
        $content = str_replace(
            "applyStakeholderFilter(\$post, 'pengawasnama.kabupaten_id', 'nama_sekolah', null, 'rencanakerja.sekolah')",
            "applyStakeholderFilter(\$post, 'pengawasnama.kabupaten_id', null, 'pengawasnama')",
            $content
        );
        
        file_put_contents($file, $content);
        echo "Reverted in $file\n";
    }
}
