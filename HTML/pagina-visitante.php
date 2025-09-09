<?php
require_once '../PHP/conexao.php';

$autores = [];
$sqlBuscaAutor = "SELECT nome, id FROM autores ORDER BY nome ASC";
$stmt = $pdo->prepare($sqlBuscaAutor);
$stmt->execute();
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$livros = [];
$pesquisa = $_GET['pesquisa'] ?? '';

if (!empty($pesquisa)) {
    // Buscar livros que começam com a pesquisa
    $sqlApresentaLivros = "SELECT l.*, COALESCE(a.nome, 'Autor não informado') as nome_autor 
    FROM livros l 
    LEFT JOIN autores a ON l.autor_id = a.id 
    WHERE l.ativo = TRUE AND l.titulo LIKE :pesquisa 
    ORDER BY l.titulo ASC";
    $stmt = $pdo->prepare($sqlApresentaLivros);
    $stmt->bindValue(':pesquisa', $pesquisa . '%', PDO::PARAM_STR);
    $stmt->execute();
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Buscar todos os livros
    $sqlApresentaLivros = "SELECT l.*, COALESCE(a.nome, 'Autor não informado') as nome_autor 
    FROM livros l 
    LEFT JOIN autores a ON l.autor_id = a.id 
    WHERE l.ativo = TRUE 
    ORDER BY l.titulo ASC";
    $stmt = $pdo->prepare($sqlApresentaLivros);
    $stmt->execute();
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biblioteca Arco-Íris - Visitante</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/paginavisitante.css">
</head>
<body>
  <header class="header">
    <div class="header-title">
      <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
      <span>Biblioteca Arco-Íris - Acervo</span>
    </div>
    <div>
      <a class="voltar" href="../index.php">Voltar</a>
    </div>
    <div class="header-buttons">
      <a href="registro.php" class="header-btn">Registrar</a>
      <a href="login.php" class="header-btn">Entrar</a>
    </div>
  </header>

  <div class="carousel-container">
    <div class="carousel">
    <?php foreach ($livros as $livro): ?>
                <?php if(!empty($livro['imagem_capa'])): ?>
                    <?php
                        $imagemData = $livro['imagem_capa'];
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
                <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Banners">
                <?php else: ?>
                    <img src="../IMG/default-avatar.svg" alt="capa do livro" class="book-cover">
                <?php endif; ?>
        <?php endforeach; ?>
    </div>
  </div>


  <div>
    <?php if (!empty($pesquisa)): ?>
      <div style="text-align: center; margin: 20px 0;">
        <h2>Resultados para: "<?= htmlspecialchars($pesquisa) ?>"</h2>
        <p><?= count($livros) ?> livro(s) encontrado(s)</p>
      </div>
    <?php endif; ?>
    
    <?php if (empty($livros) && !empty($pesquisa)): ?>
      <div style="text-align: center; margin: 40px 0; padding: 40px; background: rgba(255,255,255,0.9); border-radius: 10px;">
        <h3>Nenhum livro encontrado</h3>
        <p>Não foram encontrados livros que comecem com "<?= htmlspecialchars($pesquisa) ?>"</p>
        <a href="usuario.php" style="display: inline-block; padding: 10px 20px; background: #ff9100; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;">Ver todos os livros</a>
      </div>
    <?php else: ?>
      <div class="books-container" id="booksGrid">
        <?php foreach ($livros as $livro): ?>
            <div class="book-card">
                <?php if(!empty($livro['imagem_capa'])): ?>
                    <?php
                        $imagemData = $livro['imagem_capa'];
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
                <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Capa do livro" class="book-cover">
                <?php else: ?>
                    <img src="../IMG/default-avatar.svg" alt="capa do livro" class="book-cover">
                <?php endif; ?>
                <div class="book-title"><?= htmlspecialchars($livro['titulo']) ?></div>
                <p>Autor: <?= htmlspecialchars($livro['nome_autor']) ?></p>

                  <button class="ver-mais-btn" onclick="openBookModal(<?= $livro['id'] ?>, '<?= htmlspecialchars($livro['titulo'], ENT_QUOTES) ?>', '<?= htmlspecialchars($livro['nome_autor'], ENT_QUOTES) ?>', <?= $livro['estoque'] ?>)">Ver mais</button>
            </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal de detalhes do livro -->
  <div id="bookModal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalBookTitle">Detalhes do Livro</h2>
        <button class="close-modal" onclick="closeBookModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="modal-book-cover">
          <img id="modalBookImage" src="" alt="Capa do livro">
        </div>
        <div class="modal-book-info">
          <div class="book-details">
            <p><strong>Autor:</strong> <span id="modalBookAuthor"></span></p>
            <p><strong>Estoque:</strong> <span id="modalBookStock">Disponível</span></p>
            <p><strong>Status:</strong> <span id="modalBookStatus" class="status-available">Disponível para empréstimo</span></p>
          </div>
          <div class="modal-actions">
            <button class="btn-borrow" onclick="window.location.href='registro.php'">📝 Cadastrar-se</button>
            <button class="btn-close" onclick="closeBookModal()">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../JS/common.js"></script>
  <script>
    // Função para abrir o modal do livro
    function openBookModal(title, author) {
      // Atualizar conteúdo do modal
      document.getElementById('modalBookTitle').textContent = title;
      document.getElementById('modalBookAuthor').textContent = author;
      document.getElementById('modalBookStock').textContent = 'Disponível';
      
      // Buscar imagem do livro
      const bookCard = event.target.closest('.book-card');
      const bookImage = bookCard.querySelector('img');
      document.getElementById('modalBookImage').src = bookImage.src;
      
      // Atualizar status
      const statusElement = document.getElementById('modalBookStatus');
      statusElement.textContent = 'Disponível para empréstimo';
      statusElement.className = 'status-available';
      
      // Mostrar modal com animação
      const modal = document.getElementById('bookModal');
      const modalContent = modal.querySelector('.modal-content');
      modal.style.display = 'flex';
      
      // Garantir que o modal mantenha a cor laranja
      modalContent.style.background = '#ff9000';
      modalContent.style.backgroundColor = '#ff9000';
      
      setTimeout(() => {
        modal.classList.add('show');
        // Forçar novamente após a animação
        modalContent.style.background = '#ff9000';
        modalContent.style.backgroundColor = '#ff9000';
      }, 10);
    }

    // Função para fechar o modal
    function closeBookModal() {
      const modal = document.getElementById('bookModal');
      modal.classList.remove('show');
      setTimeout(() => {
        modal.style.display = 'none';
      }, 300);
    }

    // Fechar modal ao clicar fora dele
    document.getElementById('bookModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeBookModal();
      }
    });

    // Fechar modal com tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeBookModal();
      }
    });
  </script>
</body>
</html> 