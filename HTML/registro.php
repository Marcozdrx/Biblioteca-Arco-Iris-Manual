<?php
require_once 'PHP/database.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registro - Biblioteca Arco-Íris</title>
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
      <form class="form-box" id="registroForm" method="POST" action="PHP/auth.php">
        <div class="input-group">
          <span class="icon">📚</span>
          <input type="text" name="cpf" placeholder="CPF (somente números)" pattern="\d*" maxlength="11" required>
        </div>
        <div class="input-group">
          <span class="icon">📞</span>
          <input type="tel" name="telefone" placeholder="Telefone" maxlength="11" required>
        </div>
        <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" name="senha" placeholder="Senha" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
        </div>
        <div class="input-group">
          <span class="icon">👤</span>
          <input type="text" name="nome" placeholder="Nome" required>
        </div>
        <button type="submit" class="btn">REGISTRAR</button>
        <div class="links" style="display: flex; justify-content: center;">
          <a href="login.php" class="btn">VOLTAR</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html> 