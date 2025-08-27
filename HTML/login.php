<?php
  session_start();
  require_once '../PHP/conexao.php';

  if($_SERVER['REQUEST_METHOD']=='POST'){
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuarios = $stmt->fetch(PDO::FETCH_ASSOC);

    if($usuarios && password_verify($senha, $usuarios['senha'])){
      $_SESSION['usuarios'] = $usuarios['nome'];
      $_SESSION['is_admin'] = $usuarios['is_admin'];
      $_SESSION['id'] = $usuarios['id'];

      if($usuarios['is_admin']== 1){
        header("Location: inicio-admin.php");
        exit();
      }else{
        header("Location: usuario.php");
        exit();
      }

    }else{
      echo "<script>alert('E-mail ou senha incorretos.');</script>";
    }

  }


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/styles.css">
  <style>

  </style>
</head>
<body>
  <button class="help-btn" onclick="showHelpModal()">?</button>

  <div id="helpModal" class="help-modal">
    <div class="help-modal-content">
      <span class="close-help" onclick="closeHelpModal()">&times;</span>
      <h2>Precisa de ajuda?</h2>
      <p class="help-text">
        Se você está tendo problemas para acessar o sistema ou tem alguma dúvida,
        entre em contato conosco através do e-mail:
        <br><br>
        <a href="mailto:suporte@bibliotecaarcoiris.com" class="help-email">
          suporte@bibliotecaarcoiris.com
        </a>
      </p>
    </div>
  </div>
<div>
  <a class="voltar" href="index.php">Voltar</a>
</div>
  <div class="container">
    <div class="form-container">
      <div class="arco-iris">
        <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
        <br>
        <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
      </div>
      <form class="form-box" id="loginForm" action="login.php" method="POST">
        <div class="input-group">
        <span class="icon">✉️</span>
        <input type="email" name="email" placeholder="exemplo@gmail.com" required>
        </div>
        <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" id="senha" name="senha" placeholder="Senha" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
          </div>
          <div class="input-group">
          <a href="recuperar-senha.php">Esqueceu a senha?</a>
          <button type="submit" class="btn">Entrar</button>
        </div>
        <div class="links" style="display: flex; justify-content: center; gap: 20px;">
          <a href="#" class="btn" id="btnVisitante">ENTRAR COMO VISITANTE</a>
          <a href="registro.php" class="btn">REGISTRAR <br> USUARIO</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html> 