<?php
$servidor = 'localhost';
$banco = 'estoque'; // seu banco de dados
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    $pdo->exec("SET NAMES utf8mb4");
    
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>