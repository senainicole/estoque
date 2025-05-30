<h2>Armazenagem</h2>
<p>Procedimentos de armazenagem de produtos.</p>
<form method="get" action="buscar.php">
    Buscar produto por nome ou código: 
    <input type="text" name="q" placeholder="Digite nome ou código" required>
    <button type="submit">Buscar</button>
</form>
<?php
require 'login/conexao.php';

$q = $_GET['q'] ?? '';

if ($q !== '') {
    // Pesquisa usando LIKE para nome e código_unico
    $sql = "SELECT id, nome, codigo_unico, descricao, preco, qr_code 
            FROM produtos 
            WHERE nome LIKE ? OR codigo_unico LIKE ? 
            ORDER BY id DESC";

    $term = "%$q%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$term, $term]);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produtos = [];
}
?>

<h1>Buscar Produtos</h1>

<form method="get" action="buscar.php">
    Buscar produto por nome ou código: 
    <input type="text" name="q" placeholder="Digite nome ou código" value="<?= htmlspecialchars($q) ?>" required>
    <button type="submit">Buscar</button>
</form>

<?php if ($q !== ''): ?>
    <h2>Resultados para "<?= htmlspecialchars($q) ?>"</h2>

    <?php if (count($produtos) > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Código Único</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>QR Code</th>
            </tr>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= htmlspecialchars($produto['id']) ?></td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= htmlspecialchars($produto['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($produto['descricao']) ?></td>
                    <td><?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td>
                        <?php if ($produto['qr_code']): ?>
                            <?php
                                $base64 = base64_encode($produto['qr_code']);
                                echo "<img src='data:image/png;base64,{$base64}' alt='QR Code' style='width:100px; height:100px;'>";
                            ?>
                        <?php else: ?>
                            Sem QR Code
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum produto encontrado.</p>
    <?php endif; ?>

<?php endif; ?>
