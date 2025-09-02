<?php
require_once 'conexao.php';
session_start();



if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id'];
    $sqlDeletarLivro = "UPDATE livros SET ativo = FALSE WHERE id = :id";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
</body>
</html>