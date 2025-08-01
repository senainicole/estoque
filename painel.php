<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel | Augebit</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      padding-top: 70px; /* espaço para navbar fixa */
    }
    .navbar-brand i,
    .nav-link i {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

  <!-- Navbar fixa no topo -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="perfil.php">

              <i class="bi bi-person-circle me-1"></i> Perfil
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Conteúdo principal -->
  <div class="container mt-4">
    <h1 class="mb-4">Bem-vindo ao Painel da Augebit</h1>

    <!-- Exemplo de conteúdo adicional -->
    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title"><i class="bi bi-info-circle me-2"></i> Informações do Sistema</h5>
        <p class="card-text">Você está logado no painel. Aqui pode ver relatórios, estatísticas ou acessar o perfil.</p>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
