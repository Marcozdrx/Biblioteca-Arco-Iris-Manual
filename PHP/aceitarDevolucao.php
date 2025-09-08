<?php
    require_once 'conexao.php';

    if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
        header("Location: ../HTML/login.php");
        exit();
    }

    if(isset($_POST['aceitarDevo'])){
        $sql = "UPDATE emprestimos SET `status` = 'devolvido' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute([':id' => $_POST['IdEmprestimo']])){
            echo "Devolução aceita";
            header('Location: ../HTML/inicio-admin.php');
        }
    }
    
?>