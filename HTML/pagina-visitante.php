<?php
require_once '../PHP/conexao.php';
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


  <div class="books-container">
    <div class="book-card">
      <img src="../IMG/domquixote.jpg" alt="Dom Quixote" class="book-cover">
      <div class="book-title">Dom Quixote</div>
      <button class="ver-mais-btn" onclick="openBookModal('Dom Quixote', 'Miguel de Cervantes')">Ver mais</button>
    </div>
    <div class="book-card">
      <img src="../IMG/1984.jpg" alt="1984" class="book-cover">
      <div class="book-title">1984</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/orgulhoepreconceito.jpg" alt="Orgulho e Preconceito" class="book-cover">
      <div class="book-title">Orgulho e Preconceito</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/osenhordosaneis.webp" alt="O Senhor dos Anéis" class="book-cover">
      <div class="book-title">O Senhor dos Anéis</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ametamorfose.webp" alt="A Metamorfose" class="book-cover">
      <div class="book-title">A Metamorfose</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/cemanosdesolidao.jpg" alt="Cem Anos de Solidão" class="book-cover">
      <div class="book-title">Cem Anos de Solidão</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/arevolucaodosbichos.jpg" alt="A Revolução dos Bichos" class="book-cover">
      <div class="book-title">A Revolução dos Bichos</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/oalquimista.jpg" alt="O Alquimista" class="book-cover">
      <div class="book-title">O Alquimista</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ameninaqueroubavalivros.jpg" alt="A Menina que Roubava Livros" class="book-cover">
      <div class="book-title">A Menina que Roubava Livros</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ohobbit.jpg" alt="O Hobbit" class="book-cover">
      <div class="book-title">O Hobbit</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/aculpaedasestrelas.jpg" alt="A Culpa é das Estrelas" class="book-cover">
      <div class="book-title">A Culpa é das Estrelas</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ocodigodavinci.jpg" alt="O Código Da Vinci" class="book-cover">
      <div class="book-title">O Código Da Vinci</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/acabana.jpg" alt="A Cabana" class="book-cover">
      <div class="book-title">A Cabana</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/onomedovento.jpg" alt="O Nome do Vento" class="book-cover">
      <div class="book-title">O Nome do Vento</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/percyjacksoneoladraoderaios.jpg" alt="Percy Jackson e o Ladrão de Raios" class="book-cover">
      <div class="book-title">Percy Jackson e o Ladrão de Raios</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ascronicasdenarnia.jpg" alt="As Crônicas de Nárnia" class="book-cover">
      <div class="book-title">As Crônicas de Nárnia</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/odiariodeannnefrank.jpg" alt="O Diário de Anne Frank" class="book-cover">
      <div class="book-title">O Diário de Anne Frank</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/aartedaguerra.jpg" alt="A Arte da Guerra" class="book-cover">
      <div class="book-title">A Arte da Guerra</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/oprincipe.jpg" alt="O Príncipe" class="book-cover">
      <div class="book-title">O Príncipe</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/asmemoriaspostumasdebrascuba.jpg" alt="Memórias Póstumas de Brás Cubas" class="book-cover">
      <div class="book-title">Memórias Póstumas de Brás Cubas</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/domcasmurro.webp" alt="Dom Casmurro" class="book-cover">
      <div class="book-title">Dom Casmurro</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/ocortico.jpg" alt="O Cortiço" class="book-cover">
      <div class="book-title">O Cortiço</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/vidassecas.jpg" alt="Vidas Secas" class="book-cover">
      <div class="book-title">Vidas Secas</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/capitaesdaareia.jpg" alt="Capitães da Areia" class="book-cover">
      <div class="book-title">Capitães da Areia</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/grandesertaoveredas.jpg" alt="Grande Sertão: Veredas" class="book-cover">
      <div class="book-title">Grande Sertão: Veredas</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
    <div class="book-card">
      <img src="../IMG/macunaima.jpg" alt="Macunaíma" class="book-cover">
      <div class="book-title">Macunaíma</div>
      <a href="registro.php" class="ver-mais-btn">Ver mais</a>
    </div>
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