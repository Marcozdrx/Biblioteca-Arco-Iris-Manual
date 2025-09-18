<?php
require_once 'conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    try {
        if (!empty($_POST['senhaNova'])) {
            $senhaNova = $_POST['senhaNova'];
            $nome = $_POST['nomeEdit'];
            $status = $_POST['statusUsuario'];
            $id = $_POST['idUsuarioEdit'];
            $senhaCripto = password_hash($senhaNova, PASSWORD_DEFAULT); 

            $sqlAttUsuario = "UPDATE usuarios SET 
            nome = :nomeEdit, 
            senha = :senhaNova, 
            ativo = :statusUsuario
            WHERE id = :idUsuarioEdit";
            
            $stmt = $pdo->prepare($sqlAttUsuario);
            $stmt->bindParam(':idUsuarioEdit', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nomeEdit', $nome);
            $stmt->bindParam(':statusUsuario', $status, PDO::PARAM_INT);
            $stmt->bindParam(':senhaNova', $senhaCripto);
        } else {
            $nome = $_POST['nomeEdit'];
            $status = $_POST['statusUsuario'];
            $id = $_POST['idUsuarioEdit'];
            
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
            echo json_encode([
                'success' => true, 
                'message' => 'Usuário atualizado com sucesso!'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Erro ao atualizar usuário'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => 'Erro interno do servidor: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
}
?>
