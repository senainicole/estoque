<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'phpqrcode/qrlib.php';

$dir = "qr_codes/";
if (!file_exists($dir)) {
    mkdir($dir);
}

$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$preco = $_POST['preco'] ?? '';

if ($nome == '') {
    echo "Erro: nome do produto obrigatório.";
    exit;
}

$conteudo = "Produto: $nome\\nDescrição: $descricao\\nPreço: R$ $preco";
$arquivoQR = $dir . preg_replace('/[^a-zA-Z0-9]/', '_', $nome) . ".png";

QRcode::png($conteudo, $arquivoQR, QR_ECLEVEL_L, 4);

echo "<h2>Produto cadastrado!</h2>";
echo "<img src='$arquivoQR' alt='QR Code'>";
?>
