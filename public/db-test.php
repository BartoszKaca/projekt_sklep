<?php
$host = 'db';
$port = 3306;
$db   = 'sklep_laravel';
$user = 'laravel';
$pass = 'laraveldo';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Połączono z MySQL OK\n";
} catch (PDOException $e) {
    echo "Błąd połączenia PDO: " . $e->getMessage() . "\n";
}