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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_senha'])) {
    $nova_senha = trim($_POST['nova_senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);
    
    if (empty($nova_senha) || empty($confirmar_senha)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipo_mensagem = 'erro';
    } elseif (strlen($nova_senha) < 6) {
        $mensagem = 'A senha deve ter pelo menos 6 caracteres.';
        $tipo_mensagem = 'erro';
    } elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = 'As senhas não coincidem.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            // Atualizar a senha no banco de dados
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $email = $_SESSION['recuperacao_email'];
            
            $sql = "UPDATE usuarios SET senha = ? WHERE email = ? AND ativo = 1";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([$senha_hash, $email]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                // Limpar dados da sessão
                unset($_SESSION['recuperacao_email']);
                unset($_SESSION['recuperacao_senha_temp']);
                unset($_SESSION['recuperacao_timestamp']);
                
                $mensagem = 'Senha alterada com sucesso! Você pode fazer login agora.';
                $tipo_mensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao alterar a senha. Tente novamente.';
                $tipo_mensagem = 'erro';
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro interno. Tente novamente mais tarde.';
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Nova Senha - Biblioteca Arco-Íris</title>
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
      
      <?php if ($tipo_mensagem == 'sucesso'): ?>
        <div style="text-align: center; margin-top: 20px;">
          <a href="login.php" class="btn" style="background: #28a745;">FAZER LOGIN</a>
        </div>
      <?php else: ?>
        <form class="form-box" method="POST">
          <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Definir Nova Senha</h2>
          <p style="text-align: center; margin-bottom: 20px; color: #666;">
            Para o email: <strong><?= htmlspecialchars($_SESSION['recuperacao_email']) ?></strong>
          </p>
          <div class="input-group">
            <span class="icon">🔒</span>
            <input type="password" name="nova_senha" placeholder="Nova senha (mínimo 6 caracteres)" required minlength="6">
            <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
          </div>
          <div class="input-group">
            <span class="icon">🔒</span>
            <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required minlength="6">
            <button type="button" class="toggle-password" onclick="togglePassword(this)">👁️</button>
          </div>
          <button type="submit" class="btn">ALTERAR SENHA</button>
          <div class="links" style="display: flex; justify-content: center;">
            <a href="validar-senha-temporaria.php" class="btn">VOLTAR</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
  
  <script>
    function togglePassword(button) {
      const input = button.previousElementSibling;
      if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
      } else {
        input.type = 'password';
        button.textContent = '👁️';
      }
    }
  </script>
</body>
</html>
