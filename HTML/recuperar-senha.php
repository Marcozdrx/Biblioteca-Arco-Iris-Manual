<?php
session_start();
require_once '../PHP/conexao.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $mensagem = 'Por favor, digite seu email.';
        $tipo_mensagem = 'erro';
    } else {
        // Verificar se o email existe no banco
        $sql = "SELECT id, nome, email FROM usuarios WHERE email = ? AND ativo = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar se encontrou o usuário
        if ($usuario && !empty($usuario['id'])) {
            // Gerar senha temporária
            $senha_temporaria = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 8);
            
            // Salvar senha temporária em arquivo
            $arquivo_senha = '../arquivos/senhas_temporarias.txt';
            $diretorio = dirname($arquivo_senha);
            
            // Criar diretório se não existir
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }
            
            $conteudo = "Email: " . $email . "\n";
            $conteudo .= "Senha Temporária: " . $senha_temporaria . "\n";
            $conteudo .= "Data/Hora: " . date('d/m/Y H:i:s') . "\n";
            $conteudo .= "Usuário: " . $usuario['nome'] . "\n";
            $conteudo .= "---\n";
            
            file_put_contents($arquivo_senha, $conteudo, FILE_APPEND | LOCK_EX);
            
            // Armazenar dados na sessão para validação posterior
            $_SESSION['recuperacao_email'] = $email;
            $_SESSION['recuperacao_senha_temp'] = $senha_temporaria;
            $_SESSION['recuperacao_timestamp'] = time();
            
            $mensagem = 'Senha temporária gerada com sucesso! Verifique o arquivo de senhas temporárias.';
            $tipo_mensagem = 'sucesso';
        } else {
            // Debug: verificar se há problema na consulta
            $sql_debug = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
            $stmt_debug = $pdo->prepare($sql_debug);
            $stmt_debug->execute([$email]);
            $total = $stmt_debug->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($total > 0) {
                $mensagem = 'Email encontrado, mas usuário pode estar inativo. Contate o administrador.';
            } else {
                $mensagem = 'Email não encontrado em nossa base de dados. Verifique se digitou corretamente.';
            }
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Senha - Biblioteca Arco-Íris</title>
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
        <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Recuperar Senha</h2>
        <div class="input-group">
          <span class="icon">📧</span>
          <input type="email" name="email" placeholder="Digite seu email" required>
        </div>
        <button type="submit" class="btn">GERAR SENHA TEMPORÁRIA</button>
        <div class="links" style="display: flex; justify-content: center; gap: 10px;">
          <a href="login.php" class="btn">VOLTAR</a>
         
        </div>
      </form>
      
      <?php if ($tipo_mensagem == 'sucesso'): ?>
        <div style="text-align: center; margin-top: 20px;">
          <a href="validar-senha-temporaria.php" class="btn" style="background: #28a745;">VALIDAR SENHA TEMPORÁRIA</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html> 