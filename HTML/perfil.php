<?php
session_start();
require_once '../PHP/conexao.php';

if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 0) {
  header("Location: login.php");
  exit();
}

$sqlPerfilUsuario = "SELECT nome, cpf, telefone, foto_usuario FROM usuarios WHERE id = :id";
$stmt = $pdo->prepare($sqlPerfilUsuario);
$stmt->bindParam(':id', $_SESSION['id']);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['cpf_usuario'] = $usuario['cpf'];
$_SESSION['telefone_usuario'] = $usuario['telefone'];
$_SESSION['foto_usuario'] = $usuario['foto_usuario'];

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se há mensagens de retorno
$mensagem = '';
$tipo_mensagem = '';
if (isset($_GET['mensagem']) && isset($_GET['tipo'])) {
    $mensagem = $_GET['mensagem'];
    $tipo_mensagem = $_GET['tipo'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil - Biblioteca Arco-Íris</title>
  <link rel="stylesheet" href="../CSS/perfil.css">
  <script src="../JS/mascaras.js"></script>
</head>
<body>
  <header class="header">
    <div class="header-title">
      <img src="../IMG/logo.png" alt="Logo" style="width: 30px; height: 30px;">
      <span>Biblioteca Arco-Íris</span>
    </div>
    <div class="header-buttons">
      <a href="usuario.php" class="header-btn">Voltar</a>
      <a href="logout.php" class="header-btn">Sair</a>
    </div>
  </header>

  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h1>Meu Perfil</h1>
        <p>Gerencie suas informações pessoais</p>
        
        <?php if ($mensagem): ?>
          <div class="mensagem <?= $tipo_mensagem ?>" style="margin-top: 15px;">
            <?= htmlspecialchars($mensagem) ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="profile-content">
        <div class="profile-photo-section">
          <div class="photo-container">
          <?php if(!empty($usuario['foto_usuario'])): ?>
                <?php
                    $imagemUsuario = $usuario['foto_usuario'];
                    // Verificar se é WebP 
                    if (substr($imagemUsuario, 0, 4) === 'RIFF') {
                        $mimeType = 'image/webp';
                    } else {
                        // Usar finfo para outros formatos
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_buffer($finfo, $imagemUsuario);
                        finfo_close($finfo);
                    }
                    
                    // Verificar se o MIME foi detectado corretamente
                    if (!$mimeType || $mimeType === 'application/octet-stream') {
                        $mimeType = 'image/webp'; // Fallback para WebP
                    }
                ?>
            <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemUsuario) ?>" class="profile-photo">
            <?php else: ?>
                <img src="../IMG/default-avatar.svg" class="profile-photo">
            <?php endif; ?>
            <div class="photo-overlay">
              <label for="photoInput" class="photo-upload-btn">
                <span>📷</span>
                <span>Alterar Foto</span>
              </label>
              <input type="file" id="photoInput" accept="image/*" style="display: none;">
            </div>
          </div>
          <button id="removePhotoBtn" class="remove-photo-btn">Remover Foto</button>
        </div>

        <div class="profile-info">
          <form id="profileForm" class="profile-form" action="atualizar_usuario.php" method="POST">
            <div class="form-group">
              <label for="nome">Nome Completo</label>
              <input type="text" id="nome" name="nome" value="<?= $_SESSION['nome_usuario'] ?>" required>
            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <input type="text" id="cpf" name="cpf" data-mascara="cpf" maxlength="14" value="<?= $_SESSION['cpf_usuario'] ?>" required>
            </div>

                         <div class="form-group">
               <label for="telefone">Telefone</label>
               <input type="text" id="telefone" name="telefone" data-mascara="telefone" maxlength="15" value="<?= $_SESSION['telefone_usuario'] ?>" required>
             </div>

            <div class="form-actions">
              <button type="submit" class="btn-save">Salvar Alterações</button>
              <button type="button" class="btn-cancel" onclick="window.location.href='usuario.php'">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmação -->
  <div id="confirmModal" class="modal">
    <div class="modal-content">
      <h3>Confirmar Alterações</h3>
      <p>Tem certeza que deseja salvar as alterações no seu perfil?</p>
      <div class="modal-actions">
        <button id="confirmSave" class="btn-confirm">Confirmar</button>
        <button id="cancelSave" class="btn-cancel">Cancelar</button>
      </div>
    </div>
  </div>
</body>
</html> 