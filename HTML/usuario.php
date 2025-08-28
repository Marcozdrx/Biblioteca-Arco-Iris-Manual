<?php
require_once '../PHP/conexao.php';

  if($_SERVER['REQUEST_METHOD'] == 'POST'){

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
<body style="background-image: url(IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;" >
  <header class="header">
    <div class="header-title">
      <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
      <span>Biblioteca Arco-Íris</span>
    </div>
    <form style="display: flex; align-items: center; gap: 8px;" onsubmit="event.preventDefault();">
      <input type="text" placeholder="Pesquisar livros..." style="padding: 8px 16px; border-radius: 4px; border: none; font-size: 16px; outline: none; width: 300px;" id="searchInput">
    </form>
    <div class="header-buttons">
      <a href="emprestimos.php" class="header-btn">Meus Empréstimos</a>
      <a href="perfil.php" class="header-btn">Perfil</a>
      <a href="index.php" class="header-btn">Sair</a>
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
    </div>
  </div>

  <div class="books-container" id="booksContainer">
    <!-- Os cards de livros serão inseridos aqui pelo JS -->  
  </div>
</body>
</html> 