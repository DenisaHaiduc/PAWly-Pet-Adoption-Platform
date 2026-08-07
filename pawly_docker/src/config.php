<?php
// $host = 'db';
// $db   = 'pawly_app';
// $user = 'root';
// $pass = 'root';

$host = 'localhost';
$db   = 'hdar2652';
$user = 'hdar2652';
$pass = 'MThi-Dg4-GRm';

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     die("Eroare de conexiune: " . $e->getMessage());
}
?>