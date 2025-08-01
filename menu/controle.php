<?php
include __DIR__ . '/../login/conexao.php';

function listarTodosProdutos($pdo) {
    $sql = "SELECT * FROM produtos ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$produtos = listarTodosProdutos($pdo);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Histórico de Produtos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .fade-in {
      animation: fadeIn 0.4s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .table-hover tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.08);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="card shadow fade-in">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">
          <i class="fas fa-list me-2"></i>Produtos Cadastrados
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Nome</th>
                <th>Código</th>
                <th>Classificação</th>
                <th>Data de Cadastro</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                  <tr>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= htmlspecialchars($produto['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($produto['classificacao']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($produto['data_cadastro'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">Nenhum produto encontrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
