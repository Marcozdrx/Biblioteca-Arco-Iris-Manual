<?php
require_once 'conexao.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (!empty($_POST['senhaNova'])) {
        $senhaNova = $_POST['senhaNova'];
        $nome = $_POST['nomeEdit'];
        $status = $_POST['status'];
        $id = $_POST['idUsuarioEdit'];
        $senhaCripto = password_hash($senhaNova, PASSWORD_DEFAULT); 

        $sqlAttUsuario = "UPDATE usuarios SET 
        nome = :nomeEdit, 
        senha = :senhaNova, 
        ativo = :statusUsuario
        WHERE id = :idUsuarioEdit";
        // Incluir imagem_capa no UPDATE
        $stmt = $pdo->prepare($sqlAttUsuario);
        $stmt->bindParam(':idUsuarioEdit', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nomeEdit', $nome);
        $stmt->bindParam(':statusUsuario', $status, PDO::PARAM_INT);
        $stmt->bindParam(':senhaNova', $senhaCripto);
    } else {
        $nome = $_POST['nomeEdit'];
        $status = $_POST['statusUsuario'];
        $id = $_POST['idUsuarioEdit'];
        // Não incluir imagem_capa no UPDATE (manter a atual)
        $sqlAttUsuario = "UPDATE usuarios SET 
        nome = :nomeEdit, 
        ativo = :statusUsuario
        WHERE id = :idUsuarioEdit";

        $stmt = $pdo->prepare($sqlAttUsuario);
        $stmt->bindParam(':idUsuarioEdit', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nomeEdit', $nome);
        $stmt->bindParam(':statusUsuario', $status, PDO::PARAM_INT);
    } 
    if($stmt->execute()){
        echo "<script>alert('Usuario atualizado com sucesso')</script>";
        header('Location: ../HTML/inicio-admin.php');
        
    }else{
        echo "<script>alert('Erro ao atualizar usuario')</script>"; 
    }
}
?>
