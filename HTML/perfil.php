<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil - Biblioteca Arco-Íris</title>
  <link rel="stylesheet" href="CSS/perfil.css">
</head>
<body>
  <header class="header">
    <div class="header-title">
      <img src="IMG/logo.png" alt="Logo" style="width: 30px; height: 30px;">
      <span>Biblioteca Arco-Íris</span>
    </div>
    <div class="header-buttons">
      <a href="usuario.php" class="header-btn">Voltar</a>
      <a href="index.php" class="header-btn">Sair</a>
    </div>
  </header>

  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h1>Meu Perfil</h1>
        <p>Gerencie suas informações pessoais</p>
      </div>

      <div class="profile-content">
        <div class="profile-photo-section">
          <div class="photo-container">
            <img id="profilePhoto" src="IMG/default-avatar.svg" alt="Foto do Perfil" class="profile-photo">
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
          <form id="profileForm" class="profile-form">
            <div class="form-group">
              <label for="nome">Nome Completo</label>
              <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <input type="text" id="cpf" name="cpf" maxlength="14" required>
            </div>

                         <div class="form-group">
               <label for="telefone">Telefone</label>
               <input type="text" id="telefone" name="telefone" maxlength="15" required>
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