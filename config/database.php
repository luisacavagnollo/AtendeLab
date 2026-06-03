<?php

$host = '127.0.0.1';
$port = '3306'; // Altere para 3307 se necessário (laboratórios Univille)
$dbname = 'atendelab';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
