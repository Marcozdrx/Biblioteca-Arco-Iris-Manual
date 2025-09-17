<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['cargo'] == 0) {
    header("Location: ../HTML/login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id'];
    $sqlDeletarLivro = "DELETE FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sqlDeletarLivro);
    $stmt->bindParam(':id', $id);
    
    if($stmt->execute() && $_SESSION['cargo'] == 1){
        echo "<script>
        alert('Livro deletado com sucesso!');
        window.location.href = '../HTML/inicio-admin.php';
        </script>";
        exit;
    }elseif($stmt->execute() && $_SESSION['cargo'] == 2){
        echo "<script>
        alert('Livro deletado com sucesso!');
        window.location.href = '../HTML/inicio-secretaria.php';
        </script>";
        exit;
    }elseif($_SESSION['cargo'] == 1){
        echo "<script>
        alert('Erro ao deltar livro!');
        window.location.href = '../HTML/inicio-admin.php';
        </script>";
        exit;
    }else{
        echo "<script>
        alert('Erro ao deletar livro!');
        window.location.href = '../HTML/inicio-secretaria.php';
        </script>";
        exit;
    }
}

?>