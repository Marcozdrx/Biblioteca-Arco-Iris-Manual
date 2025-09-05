<?php
require_once 'conexao.php';

// Esta é uma versão pública para testes - não requer autenticação
try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $titulo = $_POST['titulo'];
        $estoque = $_POST['estoque'];
        $autor_nome = $_POST['autor'];
        $dataPublicacao = $_POST['dataPublicacao'];
        $numeroPaginas = $_POST['numeroPaginas'];
        $editora = $_POST['editora'];
        $isbn = $_POST['isbn'];
        $idioma = $_POST['idioma'];
        $categoria_nome = $_POST['categoria'];
        $descricao = $_POST['descricao'];

        // Criar ou buscar autor
        $sql_autor = "SELECT id FROM autores WHERE nome = :nome";
        $stmt_autor = $pdo->prepare($sql_autor);
        $stmt_autor->bindParam(":nome", $autor_nome);
        $stmt_autor->execute();
        $autor_id = $stmt_autor->fetchColumn();

        if (!$autor_id) {
            // Criar novo autor
            $sql_insert_autor = "INSERT INTO autores (nome) VALUES (:nome)";
            $stmt_insert_autor = $pdo->prepare($sql_insert_autor);
            $stmt_insert_autor->bindParam(":nome", $autor_nome);
            $stmt_insert_autor->execute();
            $autor_id = $pdo->lastInsertId();
        }

        // Criar ou buscar categoria
        $sql_categoria = "SELECT id FROM categorias WHERE nome = :nome";
        $stmt_categoria = $pdo->prepare($sql_categoria);
        $stmt_categoria->bindParam(":nome", $categoria_nome);
        $stmt_categoria->execute();
        $categoria_id = $stmt_categoria->fetchColumn();

        if (!$categoria_id) {
            // Criar nova categoria
            $sql_insert_categoria = "INSERT INTO categorias (nome) VALUES (:nome)";
            $stmt_insert_categoria = $pdo->prepare($sql_insert_categoria);
            $stmt_insert_categoria->bindParam(":nome", $categoria_nome);
            $stmt_insert_categoria->execute();
            $categoria_id = $pdo->lastInsertId();
        }

        // Processar upload de imagem se houver
        $imagem_capa = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
            $imagem_capa = file_get_contents($_FILES['capa']['tmp_name']);
        }
        
        // Inserir livro
        $sql = "INSERT INTO livros (titulo, estoque, autor_id, ano_publicacao, numero_paginas, editora, isbn, idioma, categoria_id, descricao, imagem_capa) VALUES (:titulo, :estoque, :autor_id, :ano_publicacao, :numero_paginas, :editora, :isbn, :idioma, :categoria_id, :descricao, :imagem_capa)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":estoque", $estoque);
        $stmt->bindParam(":autor_id", $autor_id);
        $stmt->bindParam(":ano_publicacao", $dataPublicacao);
        $stmt->bindParam(":numero_paginas", $numeroPaginas);
        $stmt->bindParam(":editora", $editora);
        $stmt->bindParam(":isbn", $isbn);
        $stmt->bindParam(":idioma", $idioma);
        $stmt->bindParam(":categoria_id", $categoria_id);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":imagem_capa", $imagem_capa, PDO::PARAM_LOB);
        
        if($stmt->execute()) {
            // Redireciona de volta para a página admin
            header('Location: ../HTML/admin-publico.php');
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar livro');</script>";
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Erro ao cadastrar livro: " . $e->getMessage() . "');</script>";
}
?>
