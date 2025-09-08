<?php
    require_once 'conexao.php';
    session_start();
    if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
        header("Location: login.php");
        exit();
    }


    if(isset($_POST['aceitarDevo'])){
        $sql = "UPDATE emprestimos SET `status` = 'devolvido' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute([':id' => $_POST['IdEmprestimo']])){
            echo "<script>
                alert('Devolução aceita!');
                window.location.href = '../HTML/inicio-admin.php';
                </script>";
                exit;   
        }
    }
    
?>