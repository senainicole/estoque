<?php
// Conexão com o banco
$pdo = new PDO("mysql:host=localhost;dbname=estoque", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$codigoBusca = $_GET['codigo'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';
$ordem = $_GET['ordem'] ?? 'ASC';

function buscarProduto($pdo, $codigo) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_unico = ?");
    $stmt->execute([$codigo]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function listarHistorico($pdo, $dataInicio, $dataFim, $ordem) {
    $query = "SELECT * FROM produtos WHERE 1=1";
    $params = [];

    if ($dataInicio) {
        $query .= " AND data_cadastro >= ?";
        $params[] = $dataInicio . " 00:00:00";
    }
    if ($dataFim) {
        $query .= " AND data_cadastro <= ?";
        $params[] = $dataFim . " 23:59:59";
    }

    $query .= " ORDER BY nome " . ($ordem === 'DESC' ? 'DESC' : 'ASC');

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$produto = null;
if ($codigoBusca) {
    $produto = buscarProduto($pdo, $codigoBusca);
}

$produtos = listarHistorico($pdo, $dataInicio, $dataFim, $ordem);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Controle de Estoque e Inventário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2>Controle de Estoque e Inventário</h2>

    <!-- Formulário de busca por código -->
    <form method="GET" class="row g-3 mb-4">
      <div class="col-auto">
        <input type="text" name="codigo" class="form-control" placeholder="Buscar por código" value="<?= htmlspecialchars($codigoBusca) ?>">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary">Buscar</button>
      </div>
    </form>

    <?php if ($produto): ?>
      <div class="card mb-4">
        <div class="card-header">Detalhes do Produto</div>
        <div class="card-body">
          <p><strong>Nome:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
          <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo_unico']) ?></p>
          <p><strong>Descrição:</strong> <?= htmlspecialchars($produto['descricao']) ?></p>
          <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
          <p><strong>Classificação:</strong> <?= htmlspecialchars($produto['classificacao']) ?></p>
        </div>
      </div>
    <?php elseif ($codigoBusca): ?>
      <div class="alert alert-warning">Produto não encontrado.</div>
    <?php endif; ?>

    <!-- Filtro de histórico -->
    <form method="GET" class="row g-3 mb-4">
      <div class="col-auto">
        <label for="data_inicio" class="form-label">Data Início</label>
        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>">
      </div>
      <div class="col-auto">
        <label for="data_fim" class="form-label">Data Fim</label>
        <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>">
      </div>
      <div class="col-auto">
        <label for="ordem" class="form-label">Ordenar por Nome</label>
        <select name="ordem" id="ordem" class="form-select">
          <option value="ASC" <?= $ordem === 'ASC' ? 'selected' : '' ?>>A-Z</option>
          <option value="DESC" <?= $ordem === 'DESC' ? 'selected' : '' ?>>Z-A</option>
        </select>
      </div>
      <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-secondary">Filtrar</button>
      </div>
    </form>

    <!-- Tabela histórico -->
    <table class="table table-striped table-bordered">
      <thead class="table-light">
        <tr>
          <th>Nome</th>
          <th>Classificação</th>
          <th>Descrição</th>
          <th>Preço</th>
          <th>Data de Cadastro</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($produtos): ?>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['classificacao']) ?></td>
              <td><?= htmlspecialchars($p['descricao']) ?></td>
              <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
              <td><?= $p['data_cadastro'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">Nenhum produto encontrado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
