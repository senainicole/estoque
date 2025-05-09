<?php
$servername = "localhost";
$username = "root";
$password = ""; // deixe vazio se estiver usando XAMPP sem senha
$dbname = "estoque";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}
?>
