<?php
require_once '../PHP/conexao.php';
  try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $cpf = $_POST['cpf'];
      $telefone = $_POST['telefone'];
      $senha = $_POST['senha'];
      $nome = $_POST['nome'];
      $email = $_POST['email'];
      $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

      $sql = "INSERT INTO usuarios (nome, cpf, telefone, senha, email, cargo) VALUES (:nome, :cpf, :telefone, :senha, :email, 0)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(":nome", $nome);
      $stmt->bindParam(":cpf", $cpf);
      $stmt->bindParam(":telefone", $telefone);
      $stmt->bindParam(":senha", $senha_hash);
      $stmt->bindParam(":email", $email);
      
      if($stmt->execute()) {
        echo "<script>
                alert('Registro bem-sucedido!');
                window.location.href = 'login.php';
                </script>";
                exit;
      } else {
        echo "<script>
                alert('Erro ao se registrar!');
                window.location.href = 'registro.php';
                </script>";
                exit;
      }
    }
  } catch (Exception $e) {
    echo "<script>alert('Erro ao cadastrar usuário: " . $e->getMessage() . "');</script>";
  }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registro - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/styles.css">
  <script src="../JS/mascaras.js"></script>
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="arco-iris">
        <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
        <br>
        <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
      </div>
      <form class="form-box" id="registroForm" method="POST" action="registro.php">
        <div class="input-group">

          <span class="icon">👤</span>
          <input type="text" name="nome" placeholder="Nome" required>
          </div>
          <div class="input-group">
          <span class="icon">📚</span>
          <input type="text" name="cpf" placeholder="CPF (000.000.000-00)" data-mascara="cpf" maxlength="14" required>
          </div>
          <div class="input-group">
          <span class="icon">📞</span>
          <input type="tel" name="telefone" placeholder="Telefone ((00) 00000-0000)" data-mascara="telefone" maxlength="15" required>
          </div>
          <div class="input-group">
          <span class="icon">✉️</span>
          <input type="email" name="email" placeholder="E-mail" required>
          </div>
          <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" name="senha" placeholder="Senha" minlenght="6" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>  
        </div>
        <button type="submit" class="btn">REGISTRAR</button>
        <div class="links" style="display: flex; justify-content: center;">
          <a href="../index.php" class="btn">VOLTAR</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html> 