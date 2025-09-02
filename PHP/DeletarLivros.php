<?php
require_once 'conexao.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id'];
    $sqlDeletarLivro = "DELETE FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sqlDeletarLivro);
    $stmt->bindParam(':id', $id);
    
    if($stmt->execute()){
        echo "<script>alert('Livro deletado com sucesso')</script>";
        header("Location: ../HTML/inicio-admin.php");
    }else{
        echo "<script>alert('Erro ao deletar livro')</script>";
    }
}

?>