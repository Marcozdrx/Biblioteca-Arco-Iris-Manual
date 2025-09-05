<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$idusuario = $_SESSION['id'];

$emprestimos = [];
$sql = "SELECT emprestimos.*, livros.titulo, livros.imagem_capa FROM emprestimos 
        INNER JOIN livros ON emprestimos.livro_id = livros.id 
        WHERE emprestimos.usuario_id = ? AND emprestimos.status IN ('emprestado', 'atrasado', 'aguardando_devolucao', 'devolvido')";   
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idusuario]);
        $emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['renovacao'])){
  $sql = "UPDATE emprestimos SET data_devolucao_prevista = DATE_ADD(data_devolucao_prevista, INTERVAL 7 DAY), renovado = TRUE WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  if($stmt->execute([':id' => $_POST['id']])){
    echo "<script>alert('Livro renovado com suceso')</script>"
  }

}
if(isset($_POST['devolver'])){
  $sql = "UPDATE emprestimos SET `status` = 'aguardando_devolucao' WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  if($stmt->execute([':id' => $_POST['id']])){
    echo "<script>alert('Livro enviado para o admin aceitar')</script>"
  }
}
if(isset($_POST['deletarEmprestimo'])){
  $sql = "DELETE FROM emprestimos WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  if($stmt->execute([':id' => $_POST['id']])){
    echo "<script>alert('Emprestimo deletado com sucesso')</script>"
  }

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
    <?php if(!empty($emprestimos)): ?>
      <?php foreach ($emprestimos as $emprestimo): ?>
        <div class='emprestimo-card'>
          <?php if(!empty($emprestimo['imagem_capa'])): ?>
            <?php
                $imagemData = $emprestimo['imagem_capa'];
                // Verificar se é WebP 
                if (substr($imagemData, 0, 4) === 'RIFF') {
                    $mimeType = 'image/webp';
                } else {
                    // Usar finfo para outros formatos
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $imagemData);
                    finfo_close($finfo);
                }
                
                // Verificar se o MIME foi detectado corretamente
                if (!$mimeType || $mimeType === 'application/octet-stream') {
                    $mimeType = 'image/webp'; // Fallback para WebP
                }
            ?>
            <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Capa do livro">
          <?php else: ?>
            <img src="../IMG/default-avatar.svg" alt="capa do livro">
          <?php endif; ?>
          <div class='emprestimo-info'>
            <h3><?=htmlspecialchars($emprestimo['titulo'])?></h3>
            <p><span class='status'><?=htmlspecialchars($emprestimo['status'])?></span></p>
            <p>Data do Empréstimo: <?=date('d/m/Y', strtotime($emprestimo['data_emprestimo']))?></p>
            <p>Data de Devolução: <?=date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista']))?></p>
            <?php if($emprestimo['multa_valor'] > 0): ?>
              <p>Multa: R$ <?=number_format($emprestimo['multa_valor'], 2, ',', '.')?></p>
            <?php endif; ?>
          </div>
          <div class='emprestimo-actions'>
            <form method="POST" action="emprestimos.php">
              <input type="hidden" name="id" value="<?= htmlspecialchars($emprestimo['id'])?>">
              <?php if($emprestimo['status'] == 'devolvido'): ?>
                <button name="deletarEmprestimo">Deletar Emprestimo</button>
              <?php else: ?>
                <?php if($emprestimo['renovado'] == 1): ?>
                    <button disabled>Já renovado</button>
                <?php else: ?>
                    <button name="renovacao">Renovar livro</button>
                <?php endif; ?>
                <?php if($emprestimo['status'] == 'aguardando_devolucao'): ?>
                <button disabled>Aguardando devolução</button>
                <?php else: ?>
                <button name="devolver">Devolver</button>
                <?php endif; ?>
              <?php endif; ?>

            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class='emprestimo-card'>
        <h3>Nenhum empréstimo encontrado</h3>
      </div>
    <?php endif; ?>
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