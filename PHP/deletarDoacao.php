<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doacao_id = $_POST['doacao_id'] ?? null;
    
    if (!$doacao_id) {
        echo json_encode(['error' => 'ID da doação não fornecido']);
        exit();
    }
    
    try {
        // Verificar se a doação existe e está recusada
        $sql = "SELECT status FROM doacoes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $doacao_id);
        $stmt->execute();
        $doacao = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doacao) {
            echo json_encode(['error' => 'Doação não encontrada']);
            exit();
        }
        
        if ($doacao['status'] !== 'recusada') {
            echo json_encode(['error' => 'Apenas doações recusadas podem ser deletadas']);
            exit();
        }
        
        // Deletar a doação
        $sql = "DELETE FROM doacoes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $doacao_id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Doação deletada com sucesso']);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao deletar doação: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>
