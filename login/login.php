<?php
session_start();
require_once 'conexao.php'; 

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $sql = "SELECT * FROM funcionarios WHERE email = ? AND senha = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $senha]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $_SESSION['logado'] = true;
        $_SESSION['email'] = $email;
        header("Location: ../menu/menu.php");
        exit();
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login | Estoque Augebit</title>
  <style>
    @font-face {
      font-family: 'Poppins';
      src: url('../fonts/Poppins-Regular.ttf') format('truetype');
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      display: flex;
      height: 100vh;
      background: #f9f9f9;
    }

    .left {
      flex: 1;
      background: url('sua-imagem-aqui.jpg') no-repeat center;
      background-size: cover;
    }

    .right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #fcfcfc;
      padding: 40px;
    }

    .container {
      max-width: 350px;
      width: 100%;
    }

    h2 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .subtitle {
      font-size: 12px;
      color: #999;
      margin-bottom: 30px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input {
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 13px;
      font-family: 'Poppins', sans-serif;
    }

    button {
      padding: 12px;
      background: #000;
      color: #fff;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 15px;
      margin-top: 10px;
      transition: background 0.3s;
    }

    button:hover {
      background: #333;
    }

    .forgot {
      margin-top: 15px;
      text-align: center;
    }

    .forgot a {
      color: #4b4cff;
      text-decoration: none;
      font-size: 13px;
    }

    .forgot a:hover {
      text-decoration: underline;
    }

    .erro {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <div class="left"></div>

  <div class="right">
    <div class="container">
      <h2>Olá, bem-vindo(a)!</h2>
      <p class="subtitle">Insira seus dados abaixo para efetuar o login.</p>

      <?php if ($erro): ?>
        <p class="erro"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>

      <form method="POST">
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Login</button>
      </form>

      <div class="forgot">
        <a href="#">Esqueceu sua senha?</a>
      </div>
    </div>
  </div>

</body>
</html>
