<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookId = $_POST['bookId'] ?? null;
    
    try {
        // Verificar se o livro existe
        $sqlCheck = "SELECT id FROM livros WHERE id = :id AND ativo = TRUE";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':id', $bookId);
        $stmtCheck->execute();
        
        if ($stmtCheck->rowCount() == 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Livro não encontrado']);
            exit();
        }
        
        // Verificar se há empréstimos ativos para este livro
        $sqlEmprestimos = "SELECT COUNT(*) as total FROM emprestimos WHERE livro_id = :id AND status IN ('emprestado', 'atrasado')";
        $stmtEmprestimos = $pdo->prepare($sqlEmprestimos);
        $stmtEmprestimos->bindParam(':id', $bookId);
        $stmtEmprestimos->execute();
        $result = $stmtEmprestimos->fetch();
        
        if ($result['total'] > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Não é possível excluir o livro. Existem empréstimos ativos.']);
            exit();
        }
        
        // Deleta o livro
        $sqlDelete = "DELETE FROM livros WHERE id = :id";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $bookId);
        
        if ($stmtDelete->execute()) {
            echo json_encode(['success' => 'Livro excluído com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir livro']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
?>
