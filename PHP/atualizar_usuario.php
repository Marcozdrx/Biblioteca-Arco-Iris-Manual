<?php
require_once 'conexao.php';
session_start();

if($_SESSION['is_admin'] != 0){
    echo "<script>alert('Voce nao tem permissao para acessar essa pagina');window.location.href='../index.php';</script>";
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $nome_usuario = $_POST['nome'];
    $telefone_usuario = $_POST['telefone'];
    $cpf_usuario = $_POST['cpf'];
    $id_usuario = $_POST['id_usuario'];
    if(!empty($_FILES['foto_usuario'])){
        $foto_usuario = file_get_contents($_FILES['foto_usuario']['tmp_name']);
        $sql = "UPDATE usuarios SET nome = :nome, cpf = :cpf, telefone = :telefone, foto_usuario = :foto_usuario WHERE id = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome_usuario);
        $stmt->bindParam(":telefone", $telefone_usuario);
        $stmt->bindParam(":cpf", $cpf_usuario);
        $stmt->bindParam(":foto_usuario", $foto_usuario);
        $stmt->bindParam(":id_usuario", $id_usuario);
        if($stmt->execute()){
            echo "<script>alert('Usuario com foto atualizado com sucesso');window.location.href='../HTML/perfil.php';</script>";
        }else{
            echo "<script>alert('Erro ao atulizar usuario');window.location.href='perfil.php';</script>";
        }
    }else{
        $sql = "UPDATE usuarios SET nome = :nome, cpf = :cpf, telefone = :telefone WHERE id = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome_usuario);
        $stmt->bindParam(":telefone", $telefone_usuario);
        $stmt->bindParam(":cpf", $cpf_usuario);
        $stmt->bindParam(":id_usuario", $id_usuario);
        if($stmt->execute()){
            echo "<script>alert('Usuario atualizado com sucesso');window.location.href='perfil.php';</script>";
        }else{
            echo "<script>alert('Erro ao atulizar usuario');window.location.href='../HTML/perfil.php';</script>";
        }
    }
}else{
    echo "<script>alert('Método não permitido');window.location.href='perfil.php';</script>";
}

?>