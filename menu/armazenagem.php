<?php
// Conexão com o banco
$pdo = new PDO("mysql:host=localhost;dbname=estoque", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';
$qrCodeUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $classificacao = $_POST['classificacao'] ?? '';

    if ($nome && $codigo && is_numeric($preco) && $classificacao) {
        // Verifica se o código já existe
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE codigo_unico = ?");
        $verifica->execute([$codigo]);

        if ($verifica->fetchColumn() > 0) {
            $msg = "Erro: já existe um produto com esse código.";
        } else {
            // Monta o texto do QR code incluindo a classificação
            $dados = "Produto: $nome - Código: $codigo - Descrição: $descricao - Preço: R$ " . number_format($preco, 2, ',', '.') . " - Classificação: $classificacao";

            // Gera a URL do QR Code (Google Charts API)
            $qrCodeUrl = "https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=" . urlencode($dados);

            // Insere no banco incluindo classificação
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, codigo_unico, descricao, preco, classificacao) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $codigo, $descricao, $preco, $classificacao]);

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

      <div class="mb-3">
        <label class="form-label">Classificação *</label>
        <select name="classificacao" class="form-control" required>
          <option value="" <?= (($_POST['classificacao'] ?? '') === '') ? 'selected' : '' ?>>Selecione</option>
          <option value="Eletrônicos" <?= (($_POST['classificacao'] ?? '') === 'Eletrônicos') ? 'selected' : '' ?>>Eletrônicos</option>
          <option value="Informática" <?= (($_POST['classificacao'] ?? '') === 'Informática') ? 'selected' : '' ?>>Informática</option>
          <option value="Material de escritório" <?= (($_POST['classificacao'] ?? '') === 'Material de escritório') ? 'selected' : '' ?>>Material de escritório</option>
          <option value="Acessórios de áudio" <?= (($_POST['classificacao'] ?? '') === 'Acessórios de áudio') ? 'selected' : '' ?>>Acessórios de áudio</option>
          <option value="Eletrônicos vestíveis" <?= (($_POST['classificacao'] ?? '') === 'Eletrônicos vestíveis') ? 'selected' : '' ?>>Eletrônicos vestíveis</option>
          <option value="Fotografia/Vídeo" <?= (($_POST['classificacao'] ?? '') === 'Fotografia/Vídeo') ? 'selected' : '' ?>>Fotografia/Vídeo</option>
        </select>
      </div>

      <button type="submit" class="btn btn-success">Cadastrar Produto</button>
    </form>

    <?php if ($qrCodeUrl): ?>
      <div class="mt-5">
        <h5>QR Code do Produto:</h5>
        <img src="<?= $qrCodeUrl ?>" alt="QR Code" class="img-thumbnail" style="max-width: 200px;">
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Faz a mensagem desaparecer após 3 segundos
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
