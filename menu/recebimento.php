<?php
require '../login/conexao.php';

// Inclua essa lib se você não tiver, pode baixar aqui: https://sourceforge.net/projects/phpqrcode/
// Ou crie pasta 'phpqrcode' e coloque a lib lá
require_once('../phpqrcode/qrlib.php'); // Ajuste caminho conforme sua estrutura

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? 0;

    // Gera código único automaticamente
    $codigo_unico = uniqid('prod_');

    // Gerar QR code em uma variável temporária
    ob_start();
    QRcode::png($codigo_unico, null, QR_ECLEVEL_L, 4);
    $imageString = ob_get_contents();
    ob_end_clean();

    // Inserir no banco
    $sql = "INSERT INTO produtos (nome, codigo_unico, descricao, preco, qr_code) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $codigo_unico, $descricao, $preco, $imageString]);

    echo "<p style='color:green;'>Produto cadastrado com sucesso!</p>";
}
?>

<form method="POST" action="">
    <input type="text" name="nome" placeholder="Nome do produto" required><br>
    <textarea name="descricao" placeholder="Descrição"></textarea><br>
    <input type="number" step="0.01" name="preco" placeholder="Preço" required><br>
    <button type="submit">Cadastrar Produto</button>
</form>
