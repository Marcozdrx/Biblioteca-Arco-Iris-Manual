<?php
require_once 'conexao.php';
session_start();




if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['idLivroEdit'];
    $titulo = $_POST['tituloEdit'];
    $estoque = $_POST['estoqueEdit'];
    $autor_id = $_POST['autorEdit'];
    $imagem_capa = $_FILES['capaEdit'];
    $dataPublicacao = $_POST['dataPublicacaoEdit'];
    $numero_paginas = $_POST['numeroPaginasEdit'];
    $editora = $_POST['editoraEdit'];
    $isbn = $_POST['isbnEdit'];
    $idioma = $_POST['idiomaEdit'];
    $categoria_id = $_POST['categoriaEdit'];
    $descricao = $_POST['descricaoEdit'];
    
    $sqlAttLivro = "UPDATE livros SET 
    titulo = :titulo, 
    autor_id = :autor, 
    categoria_id = :categoria, 
    isbn = :isbn, 
    ano_publicacao = :ano_publicacao, 
    numero_paginas = :numero_paginas, 
    descricao = :descricao, 
    estoque = :estoque, 
    editora = :editora, 
    idioma = :idioma,
    imagem_capa = :imagem_capa
    WHERE id = :id";
    
    
    $stmt = $pdo->prepare($sqlAttLivro);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':imagem_capa', $imagem_capa, PDO::PARAM_LOB);
    $stmt->bindParam(':estoque', $estoque);
    $stmt->bindParam(':autor', $autor_id, PDO::PARAM_INT);
    $stmt->bindParam(':ano_publicacao', $dataPublicacao); 
    $stmt->bindParam(':numero_paginas', $numero_paginas);
    $stmt->bindParam(':editora', $editora);
    $stmt->bindParam(':isbn', $isbn);
    $stmt->bindParam(':idioma', $idioma);
    $stmt->bindParam(':categoria', $categoria_id);
    $stmt->bindParam(':descricao', $descricao);
    
    if($stmt->execute()){
        echo "<script>alert('Livro atualizado com sucesso')</script>";
        header("Location: ../HTML/inicio-admin.php");
    }else{
        echo "<script>alert('Erro ao atualizar livro')</script>";
    }
}
?>
