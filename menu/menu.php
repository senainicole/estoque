<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'devolucoes';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Augebit</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        /* Container do menu e botão juntos */
        .menu-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Botão só ícone, alinhado ao menu */
        #toggleBtn {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            font-size: 24px;
            padding: 5px;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            transition: transform 0.3s ease;
            flex-shrink: 0; /* para não encolher */
        }

        #toggleBtn svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
            transition: transform 0.3s ease;
        }

        /* Rota a seta quando fechado */
        #toggleBtn[aria-expanded="false"] svg {
            transform: rotate(-90deg);
        }

        #toggleBtn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }

        nav#menu {
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
            list-style: none;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            max-height: 100px;
            opacity: 1;
            overflow: hidden;
        }

        nav#menu.closed {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
        }

        nav#menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            padding: 5px 0;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        nav#menu a:hover {
            border-color: #007bff;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="menu-container">
    <button id="toggleBtn" aria-expanded="true" aria-controls="menu" title="Abrir/Fechar menu">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M9 18l6-6-6-6" />
        </svg>
    </button>

    <nav id="menu" role="navigation" aria-label="Menu principal">
        <a href="?page=recebimento">Recebimento</a>
        <a href="?page=armazenagem">Armazenagem</a>
         <a href="?page=controle">Controle de Estoque e Inventário</a>
    </nav>
</div>

<hr>

<div>
    <?php
    $allowed_pages = ['controle', 'armazenagem', 'recebimento'];

    if (in_array($page, $allowed_pages)) {
        include __DIR__ . "/" . $page . ".php";
    } else {
        echo "<p>Página não encontrada.</p>";
    }
    ?>
</div>

<script>
    const toggleBtn = document.getElementById('toggleBtn');
    const menu = document.getElementById('menu');

    toggleBtn.addEventListener('click', () => {
        const isClosed = menu.classList.toggle('closed');
        toggleBtn.setAttribute('aria-expanded', !isClosed);
    });
</script>

</body>
</html>
