<?php
session_start();
require_once '../PHP/conexao.php';

$autores = [];
$sqlBuscaAutor = "SELECT nome, id FROM autores ORDER BY nome ASC";
$stmt = $pdo->prepare($sqlBuscaAutor);
$stmt->execute();
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$livros = [];
$sqlApresentaLivros = "SELECT l.*, COALESCE(a.nome, 'Autor não informado') as nome_autor FROM livros l LEFT JOIN autores a ON l.autor_id = a.id WHERE l.ativo = TRUE";
$stmt = $pdo->prepare($sqlApresentaLivros);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);



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
    <form style="display: flex; align-items: center; gap: 8px;" action="../PHP/pesquisarLivros.php" method="POST">
      <input type="text" placeholder="Pesquisar livros..." style="padding: 8px 16px; border-radius: 4px; border: none; font-size: 16px; outline: none; width: 300px;" id="searchInput">
    </form>
    <div class="header-buttons">
      <a href="emprestimos.php" class="header-btn">Meus Empréstimos</a>
      <a href="perfil.php" class="header-btn">Perfil</a>
      <a href="logout.php" class="header-btn">Sair</a>
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
    <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Banner">
    <?php else: ?>
      <img src="../IMG/default-avatar.svg" alt="capa do livro">
  <?php endif; ?>
    <?php endforeach; ?>
      <img src="../IMG/ohobbit.jpg" alt="Banner 2">
     
    </div>
  </div>

  <div >
  <div class="books-grid" id="booksGrid">
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
            <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Capa do livro">
            <?php else: ?>
                <img src="../IMG/default-avatar.svg" alt="capa do livro">
            <?php endif; ?>
            <h3><?= htmlspecialchars($livro['titulo']) ?></h3>
            <p>Autor: <?= htmlspecialchars($livro['nome_autor']) ?></p>

              <a class="botao-emprestar" href="detalhes-livro.php?id=<?= $livro['id'] ?>">Ver Livro</a>
        </div>
    <?php endforeach; ?>
    </div>
  </div>
  
  <script src="../JS/common.js"></script>
</body>
</html> 