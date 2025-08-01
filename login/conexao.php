<?php
// Conexão com o banco
$pdo = new PDO("mysql:host=localhost;dbname=estoque", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = '';
$msgTipo = 'info';
$produtoEncontrado = null;
$historico = [];

// Buscar produto por código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $codigoBusca = trim($_POST['codigo_busca'] ?? '');
    
    if ($codigoBusca) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_unico = ?");
        $stmt->execute([$codigoBusca]);
        $produtoEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$produtoEncontrado) {
            $msg = "Produto não encontrado com o código informado.";
            $msgTipo = "warning";
        }
    } else {
        $msg = "Por favor, informe um código para buscar.";
        $msgTipo = "danger";
    }
}

// Carregar histórico completo (sempre carregado)
try {
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
    $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $msg = "Erro ao carregar histórico: " . $e->getMessage();
    $msgTipo = "danger";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .fade-out {
            transition: opacity 1s ease-out;
            opacity: 1;
        }
        .fade-out.hide {
            opacity: 0;
        }
        .produto-card {
            border-left: 4px solid #28a745;
            background: #f8f9fa;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge-custom {
            font-size: 0.8em;
        }
        .search-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .history-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-3">
                    <i class="fas fa-boxes text-primary"></i>
                    Controle de Estoque
                </h1>
                <div class="text-center">
                    <a href="cadastrar.php" class="btn btn-success me-2">
                        <i class="fas fa-plus"></i> Cadastrar Produto
                    </a>
                    <button type="button" class="btn btn-info" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Atualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Mensagens -->
        <?php if ($msg): ?>
            <div id="mensagem" class="alert alert-<?= $msgTipo ?> fade-out">
                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- Seção de Busca -->
        <div class="search-section">
            <h3 class="mb-4">
                <i class="fas fa-search"></i> Buscar Produto
            </h3>
            <form method="POST" class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Código do Produto</label>
                    <input type="text" 
                           name="codigo_busca" 
                           class="form-control form-control-lg" 
                           placeholder="Digite o código do produto..."
                           value="<?= htmlspecialchars($_POST['codigo_busca'] ?? '') ?>"
                           required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="buscar" class="btn btn-light btn-lg w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>

            <!-- Resultado da Busca -->
            <?php if ($produtoEncontrado): ?>
                <div class="mt-4">
                    <h5 class="mb-3">
                        <i class="fas fa-check-circle text-success"></i> Produto Encontrado
                    </h5>
                    <div class="card produto-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title text-dark">
                                        <i class="fas fa-box"></i> <?= htmlspecialchars($produtoEncontrado['nome']) ?>
                                    </h5>
                                    <p class="card-text text-muted mb-2">
                                        <strong>Código:</strong> <?= htmlspecialchars($produtoEncontrado['codigo_unico']) ?>
                                    </p>
                                    <?php if ($produtoEncontrado['descricao']): ?>
                                        <p class="card-text text-muted mb-2">
                                            <strong>Descrição:</strong> <?= htmlspecialchars($produtoEncontrado['descricao']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="card-text mb-2">
                                        <strong>Preço:</strong> 
                                        <span class="text-success fw-bold">
                                            R$ <?= number_format($produtoEncontrado['preco'], 2, ',', '.') ?>
                                        </span>
                                    </p>
                                    <p class="card-text">
                                        <span class="badge bg-primary badge-custom">
                                            <?= htmlspecialchars($produtoEncontrado['classificacao']) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <?php
                                    $dados = "Produto: {$produtoEncontrado['nome']} - Código: {$produtoEncontrado['codigo_unico']} - Descrição: {$produtoEncontrado['descricao']} - Preço: R$ " . number_format($produtoEncontrado['preco'], 2, ',', '.') . " - Classificação: {$produtoEncontrado['classificacao']}";
                                    $qrCodeUrl = "https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=" . urlencode($dados);
                                    ?>
                                    <img src="<?= $qrCodeUrl ?>" alt="QR Code" class="img-thumbnail mb-2">
                                    <small class="text-muted d-block">QR Code</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Seção de Histórico -->
        <div class="history-section">
            <h3 class="mb-4">
                <i class="fas fa-history"></i> Histórico de Produtos
                <span class="badge bg-secondary ms-2"><?= count($historico) ?> produtos</span>
            </h3>

            <?php if (empty($historico)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum produto cadastrado</h5>
                    <p class="text-muted">Comece cadastrando seu primeiro produto!</p>
                    <a href="cadastrar.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Cadastrar Produto
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">
                                    <i class="fas fa-hashtag"></i> ID
                                </th>
                                <th scope="col">
                                    <i class="fas fa-box"></i> Nome
                                </th>
                                <th scope="col">
                                    <i class="fas fa-barcode"></i> Código
                                </th>
                                <th scope="col">
                                    <i class="fas fa-align-left"></i> Descrição
                                </th>
                                <th scope="col">
                                    <i class="fas fa-dollar-sign"></i> Preço
                                </th>
                                <th scope="col">
                                    <i class="fas fa-tags"></i> Classificação
                                </th>
                                <th scope="col">
                                    <i class="fas fa-qrcode"></i> QR Code
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historico as $produto): ?>
                                <tr>
                                    <td class="fw-bold"><?= $produto['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                    </td>
                                    <td>
                                        <code><?= htmlspecialchars($produto['codigo_unico']) ?></code>
                                    </td>
                                    <td>
                                        <?php if ($produto['descricao']): ?>
                                            <span class="text-muted">
                                                <?= htmlspecialchars(substr($produto['descricao'], 0, 50)) ?>
                                                <?= strlen($produto['descricao']) > 50 ? '...' : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <em class="text-muted">Sem descrição</em>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success fw-bold">
                                        R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($produto['classificacao']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $dados = "Produto: {$produto['nome']} - Código: {$produto['codigo_unico']} - Descrição: {$produto['descricao']} - Preço: R$ " . number_format($produto['preco'], 2, ',', '.') . " - Classificação: {$produto['classificacao']}";
                                        $qrCodeUrl = "https://chart.googleapis.com/chart?cht=qr&chs=80x80&chl=" . urlencode($dados);
                                        ?>
                                        <img src="<?= $qrCodeUrl ?>" 
                                             alt="QR Code" 
                                             class="img-thumbnail" 
                                             style="max-width: 50px; cursor: pointer;"
                                             onclick="mostrarQRCode('<?= addslashes($produto['nome']) ?>', '<?= $qrCodeUrl ?>')">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para QR Code -->
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode"></i> QR Code - <span id="produtoNome"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrCodeImg" src="" alt="QR Code" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Faz a mensagem desaparecer após 4 segundos
        setTimeout(() => {
            const alerta = document.getElementById('mensagem');
            if (alerta) {
                alerta.classList.add('hide');
                setTimeout(() => alerta.remove(), 1000);
            }
        }, 4000);

        // Função para mostrar QR Code em modal
        function mostrarQRCode(nomeProduto, qrUrl) {
            document.getElementById('produtoNome').textContent = nomeProduto;
            document.getElementById('qrCodeImg').src = qrUrl.replace('80x80', '200x200');
            new bootstrap.Modal(document.getElementById('qrModal')).show();
        }

        // Auto-focus no campo de busca
        document.addEventListener('DOMContentLoaded', function() {
            const campoBusca = document.querySelector('input[name="codigo_busca"]');
            if (campoBusca) {
                campoBusca.focus();
            }
        });

        // Permite buscar pressionando Enter
        document.querySelector('input[name="codigo_busca"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('button[name="buscar"]').click();
            }
        });
    </script>
</body>
</html>