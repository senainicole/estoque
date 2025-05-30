<?php
session_start();



// Pegando o email do colaborador da sessão
$nome = $_SESSION['nome'] ?? '';
$email = $_SESSION['email'] ?? '';

// Define o menu inteiro, para exibir sempre
$menu_itens = [
    'recebimento' => 'Recebimento',
    'armazenagem' => 'Armazenagem',
    'controle' => 'Controle de Estoque e Inventário'
];

// Define quais páginas o usuário pode acessar conforme final do e-mail
$permissoes = [];
if (str_ends_with($email, '@augebit.rec.com')) {
    $permissoes = ['recebimento'];
} elseif (str_ends_with($email, '@augebit.arm.com')) {
    $permissoes = ['armazenagem'];
} elseif (str_ends_with($email, '@augebit.cei.com')) {
    $permissoes = ['controle'];
} else {
    echo "Página não encontrada ou acesso não permitido.";
    exit();
}

// Página solicitada (default para a primeira do menu)
$page = $_GET['page'] ?? 'recebimento';

// Se a página não está no menu, não mostra
if (!array_key_exists($page, $menu_itens)) {
    echo "<p>Página não encontrada.</p>";
    exit();
}

// Se o usuário não tem permissão para acessar o conteúdo da página selecionada,
// não inclui o conteúdo, apenas exibe mensagem
$mostra_conteudo = in_array($page, $permissoes);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Augebit - Menu</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
        }
        nav a:hover {
            color: #007bff;
            border-color: #007bff;
        }
        nav a.active {
            color: #007bff;
            border-color: #007bff;
        }
        .msg-permissao {
            color: red;
            font-weight: bold;
        }
    </style>
</head>





<nav>
    <?php foreach ($menu_itens as $key => $label): ?>
        <a href="?page=<?= $key ?>" class="<?= $page === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</nav>

<hr>

<div>
    <?php
    if ($mostra_conteudo) {
        include __DIR__ . '/' . $page . '.php';
    } else {
        echo "<p class='msg-permissao'>Você não tem permissão para acessar o conteúdo desta página.</p>";
    }
    ?>
</div>

</body>
</html>
