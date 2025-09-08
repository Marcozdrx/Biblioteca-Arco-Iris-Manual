<?php
session_start();
require_once '../PHP/conexao.php';

if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 0) {
  header("Location: login.php");
  exit();
}
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



if($_SESSION['is_admin'] != 0){
  echo "Acesso negado, apenas usuarios com permissão podem acessar essa pagina";
}else{

  if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $titulo = $_POST['titulo'] ?? '';
      $capa = file_get_contents($_FILES['capa']['tmp_name']);
      $nomeCapa = $_FILES['capa']['name'];
      
      // DEBUG: Verificar tamanho do arquivo
      echo "<script>console.log('Tamanho do arquivo: " . strlen($capa) . " bytes');</script>";
      echo "<script>console.log('Nome do arquivo: " . $nomeCapa . "');</script>";
      
      $estoque = $_POST['estoque'];
      $autor = $_POST['autor'];
      $dataPublicacao = $_POST['dataPublicacao'];
      $numeroPaginas = $_POST['numeroPaginas'];
      $categoria = $_POST['categoria'];
      $descricao = $_POST['descricao'];
      $editora = $_POST['editora'];
      $isbn = $_POST['isbn'];
      $idioma = $_POST['idioma'];
  
      $sqlInsereLivro = "INSERT INTO livros (titulo, autor_id, categoria_id, isbn, ano_publicacao, numero_paginas, descricao, imagem_capa, estoque, editora, idioma, ativo) 
      VALUES (:titulo, :autor, :categoria, :isbn, :dataPublicacao, :numeroPaginas, :descricao, :capa, :estoque, :editora, :idioma, TRUE)";
  
      $stmt = $pdo->prepare($sqlInsereLivro);
      $stmt->bindParam(':titulo', $titulo);
      $stmt->bindParam(':autor', $autor);
      $stmt->bindParam(':categoria', $categoria);
      $stmt->bindParam(':isbn', $isbn);
      $stmt->bindParam(':dataPublicacao', $dataPublicacao);
      $stmt->bindParam(':numeroPaginas', $numeroPaginas);
      $stmt->bindParam(':descricao', $descricao);
      $stmt->bindParam(':capa', $capa, PDO::PARAM_LOB);
      $stmt->bindParam(':estoque', $estoque);
      $stmt->bindParam(':editora', $editora);
      $stmt->bindParam(':idioma', $idioma);
  
      if($stmt->execute()){
          echo "<script>alert('Livro cadastrado com sucesso')</script>";
          header("Location: inicio-admin.php");
      }else{
          echo "<script>alert('Erro ao cadastrar livro')</script>";
      }
  }

}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biblioteca Arco-Íris - Usuário</title>
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="../CSS/usuario.css">
</head>
<body style="background-image: url(../IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;" >
  <header class="header">
    <div class="header-title">
      <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
      <span>Biblioteca Arco-Íris</span>
    </div>
    <form style="display: flex; align-items: center; gap: 8px;" method="GET" id="searchForm">
      <input type="text" name="pesquisa" placeholder="Pesquisar livros..." style="padding: 8px 16px; border-radius: 4px; border: none; font-size: 16px; outline: none; width: 300px;" id="searchInput" value="<?= htmlspecialchars($pesquisa) ?>">
      <button type="submit" style="padding: 8px 16px; background: #ff9100; color: white; border: none; border-radius: 4px; cursor: pointer;">Buscar</button>
      <?php if (!empty($pesquisa)): ?>
        <a href="usuario.php" style="padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Limpar</a>
      <?php endif; ?>
    </form>
    <div class="header-buttons">
      <a href="emprestimos.php" class="header-btn">Meus Empréstimos</a>
      <a href="perfil.php" class="header-btn">Perfil</a>
      <a href="logout.php" class="header-btn">Sair</a>
    </div>
  </header>

  <div class="carousel-container">
    <div class="carousel">
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
      <!-- Duplicar as imagens para criar o loop infinito -->
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
      <!-- Duplicar as imagens para criar o loop infinito -->
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
      <!-- Duplicar as imagens para criar o loop infinito -->
      <img src="../IMG/cemanosdesolidao.jpg" alt="Banner 1">
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
      <img src="../IMG/acabana.jpg" alt="Banner 3">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="Banner 4">
      <img src="../IMG/1984.jpg" alt="Banner 5">
      <img src="../IMG/domquixote.jpg" alt="Banner 6">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Banner 7">
      <img src="../IMG/osenhordosaneis.webp" alt="Banner 8">
      <img src="../IMG/ametamorfose.webp" alt="Banner 9">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="Banner 10">
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
            <p><strong>Estoque:</strong> <span id="modalBookStock"></span></p>
            <p><strong>Status:</strong> <span id="modalBookStatus"></span></p>
          </div>
          <div class="modal-actions">
            <button id="modalBorrowBtn" class="btn-borrow" onclick="borrowBook()">📚 Emprestar Livro</button>
            <button class="btn-close" onclick="closeBookModal()">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="../JS/common.js"></script>
  <script>
    // Busca em tempo real (opcional)
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const searchForm = document.getElementById('searchForm');
      let searchTimeout;
      
      // Busca em tempo real após 500ms de inatividade
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
          searchTimeout = setTimeout(() => {
            // Redirecionar para a mesma página com o parâmetro de pesquisa
            window.location.href = `usuario.php?pesquisa=${encodeURIComponent(query)}`;
          }, 500);
        } else if (query.length === 0) {
          // Se o campo estiver vazio, mostrar todos os livros
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            window.location.href = 'usuario.php';
          }, 300);
        }
      });
      
      // Permitir busca imediata com Enter
      searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query.length > 0) {
          window.location.href = `usuario.php?pesquisa=${encodeURIComponent(query)}`;
        } else {
          window.location.href = 'usuario.php';
        }
      });
      
      // Focar no campo de pesquisa se houver uma pesquisa ativa
      <?php if (!empty($pesquisa)): ?>
        searchInput.focus();
        searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
      <?php endif; ?>
    });

    // Variáveis globais para o modal
    let currentBookId = null;
    let currentBookStock = 0;

    // Função para abrir o modal do livro
    function openBookModal(bookId, title, author, stock) {
      currentBookId = bookId;
      currentBookStock = stock;
      
      // Atualizar conteúdo do modal
      document.getElementById('modalBookTitle').textContent = title;
      document.getElementById('modalBookAuthor').textContent = author;
      document.getElementById('modalBookStock').textContent = stock + ' disponível(is)';
      
      // Buscar imagem do livro
      const bookCard = document.querySelector(`[onclick*="${bookId}"]`).closest('.book-card');
      const bookImage = bookCard.querySelector('img');
      document.getElementById('modalBookImage').src = bookImage.src;
      
      // Atualizar status e botão
      const statusElement = document.getElementById('modalBookStatus');
      const borrowBtn = document.getElementById('modalBorrowBtn');
      
      if (stock > 0) {
        statusElement.textContent = 'Disponível';
        statusElement.className = 'status-available';
        borrowBtn.style.display = 'inline-block';
        borrowBtn.textContent = '📚 Emprestar Livro';
        borrowBtn.onclick = () => borrowBook();
      } else {
        statusElement.textContent = 'Indisponível';
        statusElement.className = 'status-unavailable';
        borrowBtn.style.display = 'none';
      }
      
      // Mostrar modal com animação
      const modal = document.getElementById('bookModal');
      const modalContent = modal.querySelector('.modal-content');
      modal.style.display = 'flex';
      
      // Garantir que o modal mantenha a cor laranja
      modalContent.style.background = '#ff6600';
      modalContent.style.backgroundColor = '#ff6600';
      
      setTimeout(() => {
        modal.classList.add('show');
        // Forçar novamente após a animação
        modalContent.style.background = '#ff6600';
        modalContent.style.backgroundColor = '#ff6600';
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

    // Função para emprestar livro
    function borrowBook() {
      if (currentBookId && currentBookStock > 0) {
        window.location.href = `../PHP/registrarEmprestimo.php?livro_id=${currentBookId}`;
      }
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