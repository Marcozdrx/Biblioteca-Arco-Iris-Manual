<?php
    require_once 'conexao.php';
    session_start();
    if (!isset($_SESSION['id']) || $_SESSION['cargo'] == 0) {
        header("Location: login.php");
        exit();
    }


    if(isset($_POST['aceitarDevo'])){
        $sql = "UPDATE emprestimos SET `status` = 'devolvido' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([':id' => $_POST['IdEmprestimo']]) && $_SESSION['cargo'] == 1){
            echo "<script>
            alert('Livro cadastrado com sucesso!');
            window.location.href = '../HTML/inicio-admin.php';
            </script>";
            exit;
        }elseif($stmt->execute([':id' => $_POST['IdEmprestimo']]) && $_SESSION['cargo'] == 2){
            echo "<script>
            alert('Livro cadastrado com sucesso!');
            window.location.href = '../HTML/inicio-secretaria.php';
            </script>";
            exit;
        }elseif($_SESSION['cargo'] == 1){
            echo "<script>
            alert('Erro ao cadastrar livro!');
            window.location.href = '../HTML/inicio-admin.php';
            </script>";
            exit;
        }else{
            echo "<script>
            alert('Erro ao cadastrar livro!');
            window.location.href = '../HTML/inicio-secretaria.php';
            </script>";
            exit;
        }
    }
    
?>