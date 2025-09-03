<?php
require_once 'conexao.php';
session_start();




if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (!empty($_FILES['capaEdit']['tmp_name'])) {
        $imagem_capa = file_get_contents($_FILES['capaEdit']['tmp_name']);
        $id = $_POST['idLivroEdit'];
        $titulo = $_POST['tituloEdit'];
        $estoque = $_POST['estoqueEdit'];
        $autor_id = $_POST['autorEdit'];
        $dataPublicacao = $_POST['dataPublicacaoEdit'];
        $numero_paginas = $_POST['numeroPaginasEdit'];
        $editora = $_POST['editoraEdit'];
        $isbn = $_POST['isbnEdit'];
        $idioma = $_POST['idiomaEdit'];
        $categoria_id = $_POST['categoriaEdit'];
        $descricao = $_POST['descricaoEdit'];

        $sqlAttLivro = "UPDATE livros SET 
        titulo = :tituloEdit, 
        autor_id = :autorEdit, 
        categoria_id = :categoriaEdit, 
        isbn = :isbnEdit, 
        ano_publicacao = :dataPublicacaoEdit, 
        numero_paginas = :numeroPaginasEdit, 
        descricao = :descricaoEdit, 
        estoque = :estoqueEdit, 
        editora = :editoraEdit, 
        idioma = :idiomaEdit,
        imagem_capa = :capaEdit
        WHERE id = :idLivroEdit";
        // Incluir imagem_capa no UPDATE
        $stmt = $pdo->prepare($sqlAttLivro);
        $stmt->bindParam(':idLivroEdit', $id);
        $stmt->bindParam(':tituloEdit', $titulo);
        $stmt->bindParam(':capaEdit', $imagem_capa, PDO::PARAM_LOB);
        $stmt->bindParam(':estoqueEdit', $estoque);
        $stmt->bindParam(':autorEdit', $autor_id, PDO::PARAM_INT);
        $stmt->bindParam(':dataPublicacaoEdit', $dataPublicacao); 
        $stmt->bindParam(':numeroPaginasEdit', $numero_paginas);
        $stmt->bindParam(':editoraEdit', $editora);
        $stmt->bindParam(':isbnEdit', $isbn);
        $stmt->bindParam(':idiomaEdit', $idioma);
        $stmt->bindParam(':categoriaEdit', $categoria_id);
        $stmt->bindParam(':descricaoEdit', $descricao);
    } else {
        $id = $_POST['idLivroEdit'];
        $titulo = $_POST['tituloEdit'];
        $estoque = $_POST['estoqueEdit'];
        $autor_id = $_POST['autorEdit'];
        $dataPublicacao = $_POST['dataPublicacaoEdit'];
        $numero_paginas = $_POST['numeroPaginasEdit'];
        $editora = $_POST['editoraEdit'];
        $isbn = $_POST['isbnEdit'];
        $idioma = $_POST['idiomaEdit'];
        $categoria_id = $_POST['categoriaEdit'];
        $descricao = $_POST['descricaoEdit'];
        // Não incluir imagem_capa no UPDATE (manter a atual)
        $sqlAttLivro = "UPDATE livros SET 
        titulo = :tituloEdit, 
        autor_id = :autorEdit, 
        categoria_id = :categoriaEdit, 
        isbn = :isbnEdit, 
        ano_publicacao = :dataPublicacaoEdit, 
        numero_paginas = :numeroPaginasEdit, 
        descricao = :descricaoEdit, 
        estoque = :estoqueEdit, 
        editora = :editoraEdit, 
        idioma = :idiomaEdit
        WHERE id = :idLivroEdit";

        $stmt = $pdo->prepare($sqlAttLivro);
        $stmt->bindParam(':idLivroEdit', $id);
        $stmt->bindParam(':tituloEdit', $titulo);
        $stmt->bindParam(':estoqueEdit', $estoque);
        $stmt->bindParam(':autorEdit', $autor_id, PDO::PARAM_INT);
        $stmt->bindParam(':dataPublicacaoEdit', $dataPublicacao); 
        $stmt->bindParam(':numeroPaginasEdit', $numero_paginas);
        $stmt->bindParam(':editoraEdit', $editora);
        $stmt->bindParam(':isbnEdit', $isbn);
        $stmt->bindParam(':idiomaEdit', $idioma);
        $stmt->bindParam(':categoriaEdit', $categoria_id);
        $stmt->bindParam(':descricaoEdit', $descricao);
    }
    

    
    if($stmt->execute()){
        echo "<script>alert('Livro atualizado com sucesso')</script>";
        header('Location: ../HTML/inicio-admin.php')
        
    }else{
        echo "<script>alert('Erro ao atualizar livro')</script>"; 
    }
}
?>
