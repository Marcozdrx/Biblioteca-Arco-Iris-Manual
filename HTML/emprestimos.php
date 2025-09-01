<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Meus Empréstimos - Biblioteca Arco-Íris</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/emprestimo.css">
</head>
<body>
  <header class="header">
    <div class="header-title">
      <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
      <span>Biblioteca Arco-Íris</span>
    </div>
    <div class="header-buttons">
      <a href="usuario.php" class="header-btn">Voltar</a>
      <a href="logout.php" class="header-btn">Sair</a>
    </div>
  </header>
  <div class="emprestimos-container" id="emprestimosContainer">
    <!-- Empréstimos serão carregados via JS -->
  </div>

  <!-- Modal de Pagamento de Multa -->
  <div id="modalMulta" class="modal">
    <div class="modal-content">
      <span class="close-modal" id="closeModalMulta">&times;</span>
      <h2 class="modal-title">Pagamento de Multa</h2>
      <div id="multaInfo"></div>
      <div class="payment-options">
        <div class="payment-option" data-method="pix">
          <img src="../IMG/pix.png" alt="Pix">
          <span>Pix</span>
        </div>
        <div class="payment-option" data-method="boleto">
          <img src="../IMG/boleto.png" alt="Boleto">
          <span>Boleto</span>
        </div>
        <div class="payment-option" data-method="card">
          <img src="../IMG/card.png" alt="Cartão">
          <span>Cartão</span>
        </div>
        <div class="payment-option" data-method="doacao">
          <img src="../IMG/doacao.png" alt="Doação">
          <span>Doação</span>
        </div>
      </div>
      <button class="btn-confirmar" id="confirmarPagamento">Confirmar Pagamento</button>
      <button class="btn-cancelar" id="cancelarPagamento">Cancelar</button>
      <div class="qrcode-container" id="qrcodeContainer" style="display:none;">
        <h3>Escaneie o QR Code</h3>
        <img src="../IMG/qrcode.png" alt="QR Code" class="qrcode-image">
        <p>Após o pagamento, clique em "Confirmar Pagamento".</p>
        <button class="btn-close-qrcode" id="closeQrcode">Fechar</button>
      </div>
    </div>
  </div>

 
</body>
</html> 