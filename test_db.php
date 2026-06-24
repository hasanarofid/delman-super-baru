<?php
$host = '127.0.0.1';
$db   = 'u144635195_simodip';
$user = 'u144635195_simodip';
$pass = 'u144635195_simodip';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Count umpanbalik with tanggapan
    $stmt = $pdo->query("SELECT id_pengawas, COUNT(*) as total FROM umpanbalik_t WHERE EXISTS (SELECT * FROM tanggapanumpanbalik_t WHERE tanggapanumpanbalik_t.id_umpanbalik = umpanbalik_t.id) GROUP BY id_pengawas");
    $results = $stmt->fetchAll();
    echo "Raw umpanbalik with tanggapan:\n";
    print_r($results);
    
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
