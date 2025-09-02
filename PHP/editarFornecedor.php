<?php
/**
 * ARQUIVO PARA EDITAR FORNECEDORES
 * Biblioteca Arco-Íris
 */
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
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
if (!isset($input['id']) || !isset($input['nome']) || !isset($input['cpf_cnpj']) || !isset($input['telefone'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados obrigatórios não fornecidos']);
    exit();
}

$id = intval($input['id']);
$nome = trim($input['nome']);
$cpf_cnpj = trim($input['cpf_cnpj']);
$telefone = trim($input['telefone']);
$email = isset($input['email']) ? trim($input['email']) : null;
$endereco = isset($input['endereco']) ? trim($input['endereco']) : null;
$cidade = isset($input['cidade']) ? trim($input['cidade']) : null;
$estado = isset($input['estado']) ? trim($input['estado']) : null;
$cep = isset($input['cep']) ? trim($input['cep']) : null;
$status = isset($input['status']) ? trim($input['status']) : 'ativo';

// Validações básicas
if (empty($nome) || empty($cpf_cnpj) || empty($telefone)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nome, CPF/CNPJ e telefone são obrigatórios']);
    exit();
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

try {
    // Verificar se o fornecedor existe
    $sql_check = "SELECT id FROM fornecedores WHERE id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$id]);
    
    if (!$stmt_check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Fornecedor não encontrado']);
        exit();
    }
    
    // Verificar se CPF/CNPJ já existe em outro fornecedor
    $sql_duplicate = "SELECT id FROM fornecedores WHERE cpf_cnpj = ? AND id != ?";
    $stmt_duplicate = $pdo->prepare($sql_duplicate);
    $stmt_duplicate->execute([$cpf_cnpj, $id]);
    
    if ($stmt_duplicate->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'CPF/CNPJ já cadastrado para outro fornecedor']);
        exit();
    }
    
    // Atualizar fornecedor
    $sql = "UPDATE fornecedores SET 
                nome = ?, 
                cpf_cnpj = ?, 
                telefone = ?, 
                email = ?, 
                endereco = ?, 
                cidade = ?, 
                estado = ?, 
                cep = ?, 
                status = ?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $nome, 
        $cpf_cnpj, 
        $telefone, 
        $email, 
        $endereco, 
        $cidade, 
        $estado, 
        $cep, 
        $status,
        $id
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Fornecedor atualizado com sucesso!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar fornecedor']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>
