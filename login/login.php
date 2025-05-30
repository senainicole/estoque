<?php
session_start();
include 'conexao.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE email = ? AND senha = ?");
  $stmt->bind_param("ss", $email, $senha);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 1) {
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
    font-weight: normal;
    font-style: normal;
  }

  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    display: flex;
    height: 100vh;
  }

  .left {
    width: 50%;
    background-color: #0D0E0F;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    align-self: center;
    padding: 40px;
  }

  .left h1 {
    font-size: 36px;
    margin-bottom: 10px;
  }

  .left h1 span {
    color: #4b4cff;
  }

  .left p {
    color: #888;
    margin-top: 20px;
    max-width: 400px;
    text-align: justify;
  }

  .right {
    width: 50%;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px;
  }

  .logo {
    margin-bottom: 20px;
  }

  h2 {
    margin: 0;
    font-size: 24px;
  }

  .subtitle {
    color: #999;
    font-size: 14px;
    margin-bottom: 20px;
  }

  form {
    display: flex;
    flex-direction: column;
  }

  input, select {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
  }

  button {
    padding: 12px;
    background-color: #000;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
  }

  .erro {
    color: red;
    margin-bottom: 15px;
  }

  .forgot {
    margin-top: 10px;
    text-align: center;
  }

  .forgot a {
    color: #4b4cff;
    text-decoration: none;
  }

  .forgot a:hover {
    text-decoration: underline;
  }
</style>

</head>
<body>

  <div class="left">
    <img src="../gif/auf.gif" alt="Descrição do GIF">

  </div>

  <div class="right">
    <img src="logo.png" class="logo" alt="Logo Augebit" width="100">
    <h2>Olá, bem-vindo(a)!</h2>
    <p class="subtitle">Insira seus dados para efetuar o login.</p>

    <?php if ($erro): ?>
      <p class="erro"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      
      <button type="submit">Login</button>
    </form>

    <div class="forgot">
      <a href="#">Esqueceu sua senha?</a>
    </div>
  </div>

</body>
</html>
