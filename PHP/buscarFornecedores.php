<?php
/**
 * ARQUIVO PARA BUSCAR FORNECEDORES
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

try {
    // Buscar todos os fornecedores
    $sql = "SELECT 
                id, 
                nome, 
                cpf_cnpj, 
                telefone, 
                email, 
                endereco, 
                cidade, 
                estado, 
                cep, 
                status, 
                total_doacoes
            FROM fornecedores 
            ORDER BY nome ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $fornecedores = $stmt->fetchAll();
    
    // Buscar estatísticas
    $sql_stats = "SELECT 
                    COUNT(*) as total_fornecedores,
                    SUM(total_doacoes) as total_doacoes
                  FROM fornecedores";
    
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch();
    
    // Retornar dados em JSON
    echo json_encode([
        'success' => true,
        'fornecedores' => $fornecedores,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar fornecedores: ' . $e->getMessage()
    ]);
}
?>
