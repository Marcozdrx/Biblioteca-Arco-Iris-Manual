<?php
/**
 * ARQUIVO PARA CADASTRAR FORNECEDORES
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
if (!isset($input['nome']) || !isset($input['cpf_cnpj']) || !isset($input['telefone'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados obrigatórios não fornecidos']);
    exit();
}

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

try {
    // Verificar se CPF/CNPJ já existe
    $sql_check = "SELECT id FROM fornecedores WHERE cpf_cnpj = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$cpf_cnpj]);
    
    if ($stmt_check->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'CPF/CNPJ já cadastrado']);
        exit();
    }
    
    // Inserir novo fornecedor
    $sql = "INSERT INTO fornecedores (nome, cpf_cnpj, telefone, email, endereco, cidade, estado, cep, status, total_doacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    
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
        $status
    ]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Fornecedor cadastrado com sucesso!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao cadastrar fornecedor']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
?>
