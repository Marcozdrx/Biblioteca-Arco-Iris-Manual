<?php
/**
 * ARQUIVO PARA EXCLUIR FORNECEDORES
 * Biblioteca Arco-Íris
 */
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit();
}

// Receber dados via JSON
$input = json_decode(file_get_contents('php://input'), true);

// Validar dados obrigatórios
if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID do fornecedor não fornecido']);
    exit();
}

$id = intval($input['id']);

// Validações básicas
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

try {
    // Verificar se o fornecedor existe
    $sql_check = "SELECT id, nome, total_doacoes FROM fornecedores WHERE id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$id]);
    $fornecedor = $stmt_check->fetch();
    
    if (!$fornecedor) {
        http_response_code(404);
        echo json_encode(['error' => 'Fornecedor não encontrado']);
        exit();
    }
    
    // Verificar se o fornecedor tem doações registradas (campo total_doacoes > 0)
    if ($fornecedor['total_doacoes'] > 0) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Não é possível excluir o fornecedor pois ele possui ' . $fornecedor['total_doacoes'] . ' doação(ões) registrada(s). Desative o fornecedor em vez de excluí-lo.'
        ]);
        exit();
    }
    
    // Excluir fornecedor
    $sql = "DELETE FROM fornecedores WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Fornecedor "' . $fornecedor['nome'] . '" excluído com sucesso!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao excluir fornecedor']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>
