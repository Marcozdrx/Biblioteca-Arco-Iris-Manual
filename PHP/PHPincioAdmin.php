<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../HTML/login.php");
    exit();
}

$categorias = [];
$sqlBuscaCategoria = "SELECT nome, id FROM categorias ORDER BY nome ASC";
$stmt = $pdo->prepare($sqlBuscaCategoria);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

if($_SESSION['is_admin'] != 1){
    echo "Acesso negado, apenas usuarios com permissão podem acessar essa pagina";
}else{

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $action = $_POST['action'] ?? 'add';
        
        if($action == 'add') {
            // Adicionar novo livro
            $titulo = $_POST['titulo'];
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
        } elseif($action == 'edit') {
            // Editar livro existente
            $bookId = $_POST['bookId'];
            $titulo = $_POST['titulo'];
            $estoque = $_POST['estoque'];
            $autor = $_POST['autor'];
            $dataPublicacao = $_POST['dataPublicacao'];
            $numeroPaginas = $_POST['numeroPaginas'];
            $categoria = $_POST['categoria'];
            $descricao = $_POST['descricao'];
            $editora = $_POST['editora'];
            $isbn = $_POST['isbn'];
            $idioma = $_POST['idioma'];
            
            // Verificar se uma nova imagem foi enviada
            if(isset($_FILES['capa']) && $_FILES['capa']['error'] == 0) {
                $capa = file_get_contents($_FILES['capa']['tmp_name']);
                $sqlUpdate = "UPDATE livros SET titulo=:titulo, autor_id=:autor, categoria_id=:categoria, isbn=:isbn, ano_publicacao=:dataPublicacao, numero_paginas=:numeroPaginas, descricao=:descricao, imagem_capa=:capa, estoque=:estoque, editora=:editora, idioma=:idioma WHERE id=:id";
                $stmt = $pdo->prepare($sqlUpdate);
                $stmt->bindParam(':capa', $capa, PDO::PARAM_LOB);
            } else {
                $sqlUpdate = "UPDATE livros SET titulo=:titulo, autor_id=:autor, categoria_id=:categoria, isbn=:isbn, ano_publicacao=:dataPublicacao, numero_paginas=:numeroPaginas, descricao=:descricao, estoque=:estoque, editora=:editora, idioma=:idioma WHERE id=:id";
                $stmt = $pdo->prepare($sqlUpdate);
            }
            
            $stmt->bindParam(':id', $bookId);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':dataPublicacao', $dataPublicacao);
            $stmt->bindParam(':numeroPaginas', $numeroPaginas);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':estoque', $estoque);
            $stmt->bindParam(':editora', $editora);
            $stmt->bindParam(':idioma', $idioma);
            
            if($stmt->execute()){
                echo "<script>alert('Livro atualizado com sucesso')</script>";
                header("Location: inicio-admin.php");
            }else{
                echo "<script>alert('Erro ao atualizar livro')</script>";
            }
        }
    }

}
?>