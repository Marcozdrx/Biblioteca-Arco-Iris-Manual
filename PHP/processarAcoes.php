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
                echo "Usuario bloqueado com sucesso";
                header("Location: ../HTML/usuarios.php");
            }
            
        break;
        case 'desbloquear':
            $sql = "UPDATE usuarios SET ativo = TRUE WHERE id = :idUsuarioAcao";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuarioAcao', $id);
            if($stmt->execute()){
                echo "Usuario desbloqueado com sucesso";
                header("Location: ../HTML/usuarios.php");
            }
            break;
        case 'excluir':
            $sql = "DELETE FROM usuarios WHERE id = :idUsuarioAcao";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idUsuarioAcao', $id);
            if($stmt->execute()){
                echo "Usuario deletado com sucesso";
                header("Location: ../HTML/usuarios.php");
            }
            break;

    }
}
?>