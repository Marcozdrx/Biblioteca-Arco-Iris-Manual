<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se há dados de recuperação na sessão
if (!isset($_SESSION['recuperacao_email']) || !isset($_SESSION['recuperacao_senha_temp'])) {
    header('Location: recuperar-senha.php');
    exit();
}

// Verificar se a sessão não expirou (15 minutos)
if (isset($_SESSION['recuperacao_timestamp']) && (time() - $_SESSION['recuperacao_timestamp']) > 900) {
    unset($_SESSION['recuperacao_email']);
    unset($_SESSION['recuperacao_senha_temp']);
    unset($_SESSION['recuperacao_timestamp']);
    header('Location: recuperar-senha.php');
    exit();
}

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['senha_temporaria'])) {
    $senha_temporaria = trim($_POST['senha_temporaria']);
    
    if (empty($senha_temporaria)) {
        $mensagem = 'Por favor, digite a senha temporária.';
        $tipo_mensagem = 'erro';
    } else {
        // Verificar se a senha temporária está correta
        if ($senha_temporaria === $_SESSION['recuperacao_senha_temp']) {
            // Redirecionar para página de nova senha
            header('Location: nova-senha.php');
            exit();
        } else {
            $mensagem = 'Senha temporária incorreta.';
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Validar Senha Temporária - Biblioteca Arco-Íris</title>
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
      
      <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipo_mensagem ?>">
          <?= htmlspecialchars($mensagem) ?>
        </div>
      <?php endif; ?>
      
      <form class="form-box" method="POST">
        <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Validar Senha Temporária</h2>
        <p style="text-align: center; margin-bottom: 20px; color: #ffffff;">
          Digite a senha temporária que foi gerada para o email: <strong><?= htmlspecialchars($_SESSION['recuperacao_email']) ?></strong>
        </p>
        <div class="input-group">
          <span class="icon">🔑</span>
          <input type="text" name="senha_temporaria" placeholder="Digite a senha temporária" required>
        </div>
        <button type="submit" class="btn">VALIDAR</button>
        <div class="links" style="display: flex; justify-content: center;">
          <a href="recuperar-senha.php" class="btn">VOLTAR</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
