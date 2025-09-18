<?php
require_once 'conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['cargo'] == 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id'];
    $sqlDeletarLivro = "DELETE FROM livros WHERE id = :id";
    $stmt = $pdo->prepare($sqlDeletarLivro);
    $stmt->bindParam(':id', $id);
    
    if($stmt->execute()){
        echo json_encode([
            'success' => true, 
            'message' => 'Livro deletado com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Erro ao deletar livro'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
}

?>