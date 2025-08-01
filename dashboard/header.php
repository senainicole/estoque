<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nome = $_SESSION['nome'] ?? '';
$email = $_SESSION['email'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      Dashboard
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarHeader">
      <ul class="navbar-nav">
        <!-- Home -->
       <li class="nav-item">
  <a class="nav-link d-flex align-items-center" href="../menu/menu.php">
    <i class="bi bi-house-door me-1"></i> Home
  </a>
</li>

        <!-- Perfil -->
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="../perfil.php" title="Perfil">
            <i class="bi bi-person-circle me-1"></i> Perfil
          </a>
        </li>

        <!-- Sair -->
        <li class="nav-item">
          <a class="nav-link d-flex align-items-center" href="../login/logout.php" title="Sair">
            <i class="bi bi-box-arrow-right me-1"></i> Sair
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
