<?php
include __DIR__ . '/../login/conexao.php';

function buscarProduto($pdo, $codigo) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_unico = ?");
    $stmt->execute([$codigo]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function listarHistorico($pdo, $data_inicio, $data_fim, $ordem, $classificacao = '') {
    $where = [];
    $params = [];

    if ($data_inicio) {
        $where[] = "data_cadastro >= ?";
        $params[] = $data_inicio . " 00:00:00";
    }

    if ($data_fim) {
        $where[] = "data_cadastro <= ?";
        $params[] = $data_fim . " 23:59:59";
    }

    if ($classificacao) {
        $where[] = "classificacao = ?";
        $params[] = $classificacao;
    }

    $sql = "SELECT * FROM produtos";
    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY nome " . ($ordem === "DESC" ? "DESC" : "ASC");

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$aba = $_GET['aba'] ?? 'buscar';

$codigo = $_GET['codigo'] ?? '';
$produto = $codigo ? buscarProduto($pdo, $codigo) : null;

$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$ordem = $_GET['ordem'] ?? 'ASC';
$classificacao = $_GET['classificacao'] ?? '';

$produtos = listarHistorico($pdo, $data_inicio, $data_fim, $ordem, $classificacao);
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

    <ul class="nav nav-tabs mt-4">
      <li class="nav-item">
        <a class="nav-link <?= $aba === 'buscar' ? 'active' : '' ?>" href="?aba=buscar">Buscar Produto</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $aba === 'historico' ? 'active' : '' ?>" href="?aba=historico">Histórico</a>
      </li>
    </ul>

    <div class="mt-4">
      <?php if ($aba === 'buscar'): ?>
        <form method="GET" class="mb-4">
          <input type="hidden" name="aba" value="buscar" />
          <div class="mb-3">
            <label class="form-label">Código do Produto</label>
            <input type="text" name="codigo" class="form-control" required value="<?= htmlspecialchars($codigo) ?>" />
          </div>
          <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <?php if ($codigo): ?>
          <?php if ($produto): ?>
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                <p class="card-text"><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                <p class="card-text"><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                <p class="card-text"><strong>Classificação:</strong> <?= htmlspecialchars($produto['classificacao']) ?></p>
                <p class="card-text"><strong>Data de Cadastro:</strong> <?= $produto['data_cadastro'] ?></p>
              </div>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">Produto não encontrado.</div>
          <?php endif; ?>
        <?php endif; ?>

      <?php elseif ($aba === 'historico'): ?>
        <form method="GET" class="row g-3 mb-4">
          <input type="hidden" name="aba" value="historico" />

          <div class="col-md-3">
            <label class="form-label">Data Início</label>
            <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($data_inicio) ?>" />
          </div>

          <div class="col-md-3">
            <label class="form-label">Data Fim</label>
            <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($data_fim) ?>" />
          </div>

          <div class="col-md-3">
            <label class="form-label">Classificação</label>
            <select name="classificacao" class="form-select">
              <option value="" <?= $classificacao === '' ? 'selected' : '' ?>>Todas</option>
              <option value="Eletrônicos" <?= $classificacao === 'Eletrônicos' ? 'selected' : '' ?>>Eletrônicos</option>
              <option value="Informática" <?= $classificacao === 'Informática' ? 'selected' : '' ?>>Informática</option>
              <option value="Material de escritório" <?= $classificacao === 'Material de escritório' ? 'selected' : '' ?>>Material de escritório</option>
              <option value="Acessórios de áudio" <?= $classificacao === 'Acessórios de áudio' ? 'selected' : '' ?>>Acessórios de áudio</option>
              <option value="Eletrônicos vestíveis" <?= $classificacao === 'Eletrônicos vestíveis' ? 'selected' : '' ?>>Eletrônicos vestíveis</option>
              <option value="Fotografia/Vídeo" <?= $classificacao === 'Fotografia/Vídeo' ? 'selected' : '' ?>>Fotografia/Vídeo</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Ordem</label>
            <select name="ordem" class="form-select">
              <option value="ASC" <?= $ordem === 'ASC' ? 'selected' : '' ?>>A-Z</option>
              <option value="DESC" <?= $ordem === 'DESC' ? 'selected' : '' ?>>Z-A</option>
            </select>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-secondary">Filtrar</button>
          </div>
        </form>

        <?php if ($produtos): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Classificação</th>
                <th>Preço</th>
                <th>Data de Cadastro</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produtos as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['nome']) ?></td>
                  <td><?= htmlspecialchars($p['descricao']) ?></td>
                  <td><?= htmlspecialchars($p['classificacao']) ?></td>
                  <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                  <td><?= $p['data_cadastro'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-info">Nenhum produto encontrado nesse período.</div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
