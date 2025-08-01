<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Pegando o email do colaborador da sessão
$nome = $_SESSION['nome'] ?? '';
$email = $_SESSION['email'] ?? '';

// Define o menu inteiro, para exibir sempre
$menu_itens = [
    'armazenagem' => 'Recebimento e Armazenagem',
    'controle' => 'Controle de Estoque e Inventário'
];

// Define quais páginas o usuário pode acessar conforme final do e-mail
$permissoes = [];
if (str_ends_with($email, '@augebit.rec.com')) {
    // Unifica recebimento e armazenagem numa só página 'armazenagem'
    $permissoes = ['armazenagem'];
} elseif (str_ends_with($email, '@augebit.arm.com')) {
    $permissoes = ['armazenagem'];
} elseif (str_ends_with($email, '@augebit.cei.com')) {
    $permissoes = ['controle'];
} else {
    echo "Página não encontrada ou acesso não permitido.";
    exit();
}

// Página solicitada (default para a primeira do menu)
$page = $_GET['page'] ?? 'armazenagem';

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
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Painel | Augebit</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      padding-top: 70px; /* espaço para navbar fixa */
      font-family: Arial, sans-serif;
    }
    .navbar-brand i,
    .nav-link i {
      font-size: 1.2rem;
    }
    /* Menu dinâmico abaixo da navbar */
    nav.menu-dinamico {
      margin-top: 15px;
      margin-bottom: 15px;
    }
    nav.menu-dinamico a {
      margin-right: 20px;
      text-decoration: none;
      color: #333;
      font-weight: 600;
      border-bottom: 2px solid transparent;
      padding-bottom: 2px;
      font-size: 1.1rem;
    }
    nav.menu-dinamico a:hover {
      color: #0d6efd; /* bootstrap primary */
      border-color: #0d6efd;
    }
    nav.menu-dinamico a.active {
      color: #0d6efd;
      border-color: #0d6efd;
    }
    .msg-permissao {
      color: red;
      font-weight: bold;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <!-- Navbar fixa no topo -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">

      <a class="navbar-brand d-flex align-items-center" href="../dashboard/dashboard.php">
        Dashboard
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

     <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
  <ul class="navbar-nav">

    <!-- Home -->
    <li class="nav-item">
      <a class="nav-link d-flex align-items-center" href="../menu/menu.php" title="Home">
        <i class="bi bi-house-door me-1"></i> Home
      </a>
    </li>

    <!-- Perfil -->
    <li class="nav-item">
      <a class="nav-link d-flex align-items-center" href="../perfil.php" title="Perfil">
        <i class="bi bi-person-circle me-1"></i>
      </a>
    </li>

    <!-- Sair -->
    <li class="nav-item">
      <a class="nav-link d-flex align-items-center" href="../login/logout.php" title="Sair">
        <i class="bi bi-box-arrow-right me-1"></i>
      </a>
    </li>

  </ul>
</div>

    </div>
  </nav>

  <!-- Menu dinâmico com permissões -->
  <div class="container">
    <nav class="menu-dinamico">
      <?php foreach ($menu_itens as $key => $label): ?>
        <a href="?page=<?= htmlspecialchars($key) ?>" class="<?= $page === $key ? 'active' : '' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="container">
    <?php if ($mostra_conteudo): ?>
      <?php
      $file = __DIR__ . '/' . $page . '.php';
      if (file_exists($file)) {
          include $file;
      } else {
          echo "<p>Página solicitada não encontrada.</p>";
      }
      ?>
    <?php else: ?>
      <p class="msg-permissao">Você não tem permissão para acessar o conteúdo desta página.</p>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
