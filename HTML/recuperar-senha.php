<?php
require_once '../PHP/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Senha - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
  <div class="container">
    <div class="form-container">
      <div class="arco-iris">
        <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
        <br>
        <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
      </div>
      <form class="form-box" id="recuperarSenhaForm">
        <div class="input-group">
          <span class="icon">📚</span>
          <input type="text" name="cpf" placeholder="CPF (somente números)" pattern="\d*" maxlength="11" required>
        </div>
        <div class="input-group">
          <span class="icon">📞</span>
          <input type="tel" name="telefone" placeholder="Telefone" required>
        </div>
        <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" name="novaSenha" placeholder="Nova Senha" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
        </div>
        <button type="submit" class="btn">MUDAR SENHA</button>
        <div class="links" style="display: flex; justify-content: center;">
          <a href="login.php" class="btn">VOLTAR</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html> 