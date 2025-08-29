<?php
session_start();
require_once '../PHP/conexao.php';

if($_SESSION['is_admin'] != 1){
    echo "Acesso negado, apenas ADMINS podem acessar essa página";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome     = $_POST['nome'];
    $cpf      = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email    = $_POST['email'];
    $senha    = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, ativo) 
            VALUES (:nome, :cpf, :telefone, :email, :senha, TRUE)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);

    if ($stmt->execute()) {
//  aqui é pra redicionar devollta pra pagina de usuarios
        header("Location: ../HTML/usuarios.php");
        exit;
    } else {
        echo "Erro ao cadastrar usuário!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registro - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="arco-iris">
        <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
        <br>
        <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
      </div>
      <form class="form-box" id="registroForm" method="POST" action="">
        <div class="input-group">

          <span class="icon">👤</span>
          <input type="text" name="nome" placeholder="Nome" required>
          </div>
          <div class="input-group">
          <span class="icon">📚</span>
          <input type="text" name="cpf" placeholder="CPF (somente números)" pattern="\d*" maxlength="11" required>
          </div>
          <div class="input-group">
          <span class="icon">📞</span>
          <input type="tel" name="telefone" placeholder="Telefone" maxlength="11" required>
          </div>
          <div class="input-group">
          <span class="icon">✉️</span>
          <input type="email" name="email" placeholder="E-mail" required>
          </div>
          <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" name="senha" placeholder="Senha" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>  
        </div>
        <button type="submit" class="btn">REGISTRAR</button>
        <div class="links" style="display: flex; justify-content: center;">
          <a href="usuarios.php" class="btn">VOLTAR</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html> 
