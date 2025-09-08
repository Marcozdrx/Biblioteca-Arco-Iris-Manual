<?php
session_start();
require_once 'conexao.php';

if($_SESSION['is_admin'] != 1){
    echo "Acesso negado";
    header('Location: ../index.php');
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $botaoAcao = $_POST['botao'];
    $id = $_POST['idUsuarioAcao'];

    switch($botaoAcao){
        case 'bloquear':
            $sql = "UPDATE usuarios SET ativo = 0 WHERE id = :idUsuarioAcao";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuarioAcao', $id, PDO::PARAM_INT);
            if($stmt->execute()){
                echo "<script>
                alert('Usuário bloqueado com sucesso!');
                window.location.href = '../HTML/usuarios.php';
                </script>";
                exit;
            }
            
        break;
        case 'desbloquear':
            $sql = "UPDATE usuarios SET ativo = TRUE WHERE id = :idUsuarioAcao";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuarioAcao', $id);
            if($stmt->execute()){
                echo "<script>
                alert('Usuario desbloqueado com sucesso!');
                window.location.href = '../HTML/usuarios.php';
                </script>";
                exit;
            }
            break;
        case 'excluir':
            $sql = "DELETE FROM usuarios WHERE id = :idUsuarioAcao";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuarioAcao', $id);
            if($stmt->execute()){
                echo "<script>
                alert('Usuário deletado com sucesso!');
                window.location.href = '../HTML/usuarios.php';
                </script>";
                exit;
            }
            break;

    }
}
?>