<?php
$envPath = __DIR__ . '/.env';
$env = parse_ini_file($envPath);

$host = $env['DB_HOST'] ?? '127.0.0.1';
$db   = $env['DB_DATABASE'] ?? 'delmansuper';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? '';
$port = $env['DB_PORT'] ?? '3306';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query('SELECT nama_sekolah FROM sekolah_m LIMIT 20');
    while ($row = $stmt->fetch()) {
        echo $row['nama_sekolah'] . "\n";
    }
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
