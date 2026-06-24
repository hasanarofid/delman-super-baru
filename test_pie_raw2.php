<?php
$host = '127.0.0.1';
$db   = 'u144635195_simodip';
$user = 'u144635195_simodip';
$pass = 'u144635195_simodip';
$charset = 'utf8mb4';

$env = file_get_contents('.env.example');
if (file_exists('.env')) {
    $env = file_get_contents('.env');
}
preg_match('/DB_DATABASE=(.*)/', $env, $m); if($m) $db = trim($m[1]);
preg_match('/DB_USERNAME=(.*)/', $env, $m); if($m) $user = trim($m[1]);
preg_match('/DB_PASSWORD=(.*)/', $env, $m); if($m) $pass = trim($m[1]);

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $sql = "
    SELECT 
        COUNT(CASE WHEN t.jawaban_4 = 'Ya, melakukan pengawasan di Sekolah' THEN 1 END) as sekolah,
        COUNT(CASE WHEN t.jawaban_4 = 'Ya, melakukan pengawasan secara virtual' THEN 1 END) as by_virtual,
        COUNT(CASE WHEN t.jawaban_4 = 'Ya, pengawasan digabungkan dengan sekolah lain' THEN 1 END) as gabungan,
        COUNT(CASE WHEN t.jawaban_4 = 'Tidak melakukan pengawasan' THEN 1 END) as tidak
    FROM umpanbalik_t u
    INNER JOIN tanggapan_umpanbalik_t t ON t.id_umpanbalik = u.id
    INNER JOIN rencakakerja_t rt ON rt.id = u.id_pelaporan
    ";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    echo "Query Result NO FILTER:\n";
    print_r($results);
    
} catch (\PDOException $e) {
    echo "DB Connection failed: " . $e->getMessage() . "\n";
}
