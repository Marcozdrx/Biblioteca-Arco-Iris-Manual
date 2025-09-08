<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
require_once '../PHP/conexao.php';

if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
  header("Location: login.php");
  exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  $email = trim($_POST['email']);
  $senha = trim($_POST['senha']);
  
  if(!empty($email) && !empty($senha)) {
    try {
      $sql = "SELECT * FROM usuarios WHERE email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['is_admin'] = $usuario['is_admin'];
        
        if($usuario['is_admin'] == 1) {
          header("Location: inicio-admin.php");
          exit();
        } else {
          header("Location: usuario.php");
          exit();
        }
      } else {
        $erro = "E-mail ou senha incorretos.";
      }
    } catch (Exception $e) {
      $erro = "Erro ao conectar com o banco de dados.";
    }
  } else {
    $erro = "Por favor, preencha todos os campos.";
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
    <a class="voltar" href="../index.php">Voltar</a>
  </div>
  
  <div class="container">
    <div class="form-container">
      <div class="arco-iris">
        <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
        <br>
        <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
      </div>
      
      <?php if(isset($erro)): ?>
        <div class="erro-mensagem" style="color: red; text-align: center; margin: 10px 0; padding: 10px; background: #ffe6e6; border-radius: 5px;">
          <?php echo htmlspecialchars($erro); ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="form-box" id="loginForm" action="login.php">
        <div class="input-group">
          <span class="icon">✉️</span>
          <input type="email" name="email" placeholder="exemplo@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
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

    function showHelpModal() {
      document.getElementById('helpModal').style.display = 'block';
    }

    function closeHelpModal() {
      document.getElementById('helpModal').style.display = 'none';
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
      const modal = document.getElementById('helpModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }
  </script>
</body>
</html> 