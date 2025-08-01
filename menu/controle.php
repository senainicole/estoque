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

// Variáveis para controle das abas e dados
$aba = $_GET['aba'] ?? 'buscar';
$codigo = $_GET['codigo'] ?? '';
$produto = null;
$busca_realizada = false;

// Lógica para busca de produto
if ($codigo && $aba === 'buscar') {
    $produto = buscarProduto($pdo, $codigo);
    $busca_realizada = true;
}

// Variáveis para histórico
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$ordem = $_GET['ordem'] ?? 'ASC';
$classificacao = $_GET['classificacao'] ?? '';
$produtos = [];
$filtro_aplicado = false;

// Lógica para histórico
if ($aba === 'historico') {
    // Se pelo menos um filtro foi aplicado ou se é a primeira vez na aba
    if ($data_inicio || $data_fim || $classificacao || isset($_GET['filtrar'])) {
        $produtos = listarHistorico($pdo, $data_inicio, $data_fim, $ordem, $classificacao);
        $filtro_aplicado = true;
    } else {
        // Carrega todos os produtos na primeira visita à aba histórico
        $produtos = listarHistorico($pdo, '', '', $ordem, '');
        $filtro_aplicado = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca e Histórico de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        .loading {
            display: none;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-product {
            border-left: 4px solid #007bff;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-search me-2"></i>
                    Sistema de Busca e Histórico de Produtos
                </h2>

                <!-- Navegação por abas -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= $aba === 'buscar' ? 'active' : '' ?>" 
                           href="?aba=buscar" role="tab">
                            <i class="fas fa-search me-1"></i>
                            Buscar Produto
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?= $aba === 'historico' ? 'active' : '' ?>" 
                           href="?aba=historico" role="tab">
                            <i class="fas fa-history me-1"></i>
                            Histórico
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="myTabContent">
                    
                    <!-- ABA BUSCAR PRODUTO -->
                    <?php if ($aba === 'buscar'): ?>
                    <div class="tab-pane fade show active">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-barcode me-2"></i>
                                            Buscar Produto por Código
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="GET" id="formBuscar">
                                            <input type="hidden" name="aba" value="buscar" />
                                            <div class="mb-3">
                                                <label class="form-label">Código do Produto</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-hashtag"></i>
                                                    </span>
                                                    <input type="text" 
                                                           name="codigo" 
                                                           class="form-control" 
                                                           placeholder="Digite o código único do produto"
                                                           value="<?= htmlspecialchars($codigo) ?>" 
                                                           required />
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i>
                                                Buscar Produto
                                            </button>
                                            <?php if ($codigo): ?>
                                                <a href="?aba=buscar" class="btn btn-outline-secondary ms-2">
                                                    <i class="fas fa-times me-1"></i>
                                                    Limpar
                                                </a>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>

                                <!-- Resultado da busca -->
                                <?php if ($busca_realizada): ?>
                                    <div class="mt-4 fade-in">
                                        <?php if ($produto): ?>
                                            <div class="card card-product shadow-sm">
                                                <div class="card-header bg-success text-white">
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        Produto Encontrado
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="card-title text-primary">
                                                        <?= htmlspecialchars($produto['nome']) ?>
                                                    </h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="card-text">
                                                                <strong><i class="fas fa-info-circle me-1"></i> Descrição:</strong><br>
                                                                <?= nl2br(htmlspecialchars($produto['descricao'])) ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="card-text">
                                                                <strong><i class="fas fa-tag me-1"></i> Classificação:</strong><br>
                                                                <span class="badge bg-secondary"><?= htmlspecialchars($produto['classificacao']) ?></span>
                                                            </p>
                                                            <p class="card-text">
                                                                <strong><i class="fas fa-dollar-sign me-1"></i> Preço:</strong><br>
                                                                <span class="h5 text-success">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                                                            </p>
                                                            <p class="card-text">
                                                                <strong><i class="fas fa-calendar me-1"></i> Data de Cadastro:</strong><br>
                                                                <?= date('d/m/Y H:i', strtotime($produto['data_cadastro'])) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Produto não encontrado!</strong> 
                                                Verifique se o código "<?= htmlspecialchars($codigo) ?>" está correto.
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ABA HISTÓRICO -->
                    <?php if ($aba === 'historico'): ?>
                    <div class="tab-pane fade show active">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>
                                    Filtros de Pesquisa
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" id="formHistorico">
                                    <input type="hidden" name="aba" value="historico" />
                                    <input type="hidden" name="filtrar" value="1" />
                                    
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Data Início
                                            </label>
                                            <input type="date" 
                                                   name="data_inicio" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($data_inicio) ?>" />
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Data Fim
                                            </label>
                                            <input type="date" 
                                                   name="data_fim" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($data_fim) ?>" />
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fas fa-tags me-1"></i>
                                                Classificação
                                            </label>
                                            <select name="classificacao" class="form-select">
                                                <option value="">Todas as Classificações</option>
                                                <option value="Eletrônicos" <?= $classificacao === 'Eletrônicos' ? 'selected' : '' ?>>Eletrônicos</option>
                                                <option value="Informática" <?= $classificacao === 'Informática' ? 'selected' : '' ?>>Informática</option>
                                                <option value="Material de escritório" <?= $classificacao === 'Material de escritório' ? 'selected' : '' ?>>Material de escritório</option>
                                                <option value="Acessórios de áudio" <?= $classificacao === 'Acessórios de áudio' ? 'selected' : '' ?>>Acessórios de áudio</option>
                                                <option value="Eletrônicos vestíveis" <?= $classificacao === 'Eletrônicos vestíveis' ? 'selected' : '' ?>>Eletrônicos vestíveis</option>
                                                <option value="Fotografia/Vídeo" <?= $classificacao === 'Fotografia/Vídeo' ? 'selected' : '' ?>>Fotografia/Vídeo</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">
                                                <i class="fas fa-sort-alpha-down me-1"></i>
                                                Ordenação
                                            </label>
                                            <select name="ordem" class="form-select">
                                                <option value="ASC" <?= $ordem === 'ASC' ? 'selected' : '' ?>>A → Z</option>
                                                <option value="DESC" <?= $ordem === 'DESC' ? 'selected' : '' ?>>Z → A</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter me-1"></i>
                                            Aplicar Filtros
                                        </button>
                                        <a href="?aba=historico" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-undo me-1"></i>
                                            Limpar Filtros
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Resultados do histórico -->
                        <?php if ($filtro_aplicado): ?>
                            <div class="mt-4 fade-in">
                                <?php if ($produtos && count($produtos) > 0): ?>
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-list me-2"></i>
                                                Resultados Encontrados
                                            </h5>
                                            <span class="badge bg-light text-dark">
                                                <?= count($produtos) ?> produto(s)
                                            </span>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th><i class="fas fa-box me-1"></i> Nome</th>
                                                            <th><i class="fas fa-info me-1"></i> Descrição</th>
                                                            <th><i class="fas fa-tag me-1"></i> Classificação</th>
                                                            <th><i class="fas fa-dollar-sign me-1"></i> Preço</th>
                                                            <th><i class="fas fa-calendar me-1"></i> Data Cadastro</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($produtos as $p): ?>
                                                            <tr>
                                                                <td class="fw-bold text-primary">
                                                                    <?= htmlspecialchars($p['nome']) ?>
                                                                </td>
                                                                <td class="text-muted">
                                                                    <?= htmlspecialchars(substr($p['descricao'], 0, 50)) ?>
                                                                    <?= strlen($p['descricao']) > 50 ? '...' : '' ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary">
                                                                        <?= htmlspecialchars($p['classificacao']) ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-success fw-bold">
                                                                    R$ <?= number_format($p['preco'], 2, ',', '.') ?>
                                                                </td>
                                                                <td>
                                                                    <?= date('d/m/Y', strtotime($p['data_cadastro'])) ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Nenhum produto encontrado!</strong> 
                                        Tente ajustar os filtros de pesquisa.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Adiciona loading visual aos formulários
        document.getElementById('formBuscar')?.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Buscando...';
            btn.disabled = true;
        });

        document.getElementById('formHistorico')?.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Filtrando...';
            btn.disabled = true;
        });

        // Auto-focus no campo de código quando na aba buscar
        <?php if ($aba === 'buscar' && !$codigo): ?>
        document.querySelector('input[name="codigo"]')?.focus();
        <?php endif; ?>
    </script>
</body>
</html>