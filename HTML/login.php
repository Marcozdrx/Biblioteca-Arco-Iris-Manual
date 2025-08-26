<?php
require_once '../PHP/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="CSS/styles.css">
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
      <form class="form-box" id="loginForm">
        <div class="input-group">
          <span class="icon">📚</span>
          <input type="text" id="cpf" name="cpf" placeholder="CPF (000.000.000-00)" maxlength="14" required>
        </div>
        <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" id="senha" name="senha" placeholder="Senha" required>
          <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
        </div>
        <div class="input-group">
          <span class="icon">📞</span>
          <input type="tel" id="telefone" name="telefone" placeholder="Telefone (00) 00000-0000" maxlength="15" required>
        </div>
        <div class="links">
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