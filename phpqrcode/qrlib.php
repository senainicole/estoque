<?php

	
	$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR;
	
	// Required libs
	
	include $QR_BASEDIR."qrconst.php";
	include $QR_BASEDIR."qrconfig.php";
	include $QR_BASEDIR."qrtools.php";
	include $QR_BASEDIR."qrspec.php";
	include $QR_BASEDIR."qrimage.php";
	include $QR_BASEDIR."qrinput.php";
	include $QR_BASEDIR."qrbitstream.php";
	include $QR_BASEDIR."qrsplit.php";
	include $QR_BASEDIR."qrrscode.php";
	include $QR_BASEDIR."qrmask.php";
	include $QR_BASEDIR."qrencode.php";

<?php
// Conexão com o banco
$pdo = new PDO("mysql:host=localhost;dbname=estoque", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Inclui a biblioteca PHP QR Code
include __DIR__ . '/phpqrcode/qrlib.php';

$msg = '';
$qrFile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? '';

    if ($nome && $codigo && is_numeric($preco)) {
        // Verifica se o código já existe
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE codigo_unico = ?");
        $verifica->execute([$codigo]);

        if ($verifica->fetchColumn() > 0) {
            $msg = "Erro: já existe um produto com esse código.";
        } else {
            // Insere no banco
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, codigo_unico, descricao, preco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $codigo, $descricao, $preco]);

            // Conteúdo para o QR code
            $texto = "Produto: $nome\nCódigo: $codigo\nDescrição: $descricao\nPreço: R$ " . number_format($preco, 2, ',', '.');

            // Diretório para salvar QR codes
            $dir = __DIR__ . '/temp_qr_codes';
            if (!is_dir($dir)) {
                mkdir($dir);
            }

            // Nome do arquivo (hash para evitar duplicados)
            $filename = $dir . '/qr_' . md5($texto) . '.png';

            // Gera o QR Code PNG
            QRcode::png($texto, $filename, QR_ECLEVEL_L, 5);

            // Caminho relativo para usar na tag <img>
            $qrFile = 'temp_qr_codes/qr_' . md5($texto) . '.png';

            $msg = "Produto cadastrado com sucesso!";
        }
    } else {
        $msg = "Preencha todos os campos obrigatórios corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Cadastrar Produto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .fade-out {
      transition: opacity 1s ease-out;
      opacity: 1;
    }
    .fade-out.hide {
      opacity: 0;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2>Cadastrar Produto</h2>

    <?php if ($msg): ?>
      <div id="mensagem" class="alert alert-info mt-3 fade-out"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
      <div class="mb-3">
        <label class="form-label">Nome do Produto *</label>
        <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" class="form-control" required value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Preço (R$) *</label>
        <input type="number" step="0.01" name="preco" class="form-control" required value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>">
      </div>

      <button type="submit" class="btn btn-success">Cadastrar Produto</button>
    </form>

    <?php if ($qrFile): ?>
      <div class="mt-5">
        <h5>QR Code do Produto:</h5>
        <img src="<?= htmlspecialchars($qrFile) ?>" alt="QR Code" class="img-thumbnail" style="max-width: 200px;">
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Mensagem desaparece após 3 segundos
    setTimeout(() => {
      const alerta = document.getElementById('mensagem');
      if (alerta) {
        alerta.classList.add('hide');
        setTimeout(() => alerta.remove(), 1000);
      }
    }, 3000);
  </script>
</body>
</html>
