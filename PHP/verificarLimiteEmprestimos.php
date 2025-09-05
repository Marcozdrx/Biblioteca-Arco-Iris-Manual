<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

$usuarioId = (int) $_SESSION['id'];

try {
    // Buscar limite de empréstimos das configurações
    $sql = "SELECT valor FROM configuracoes WHERE chave = 'limite_emprestimos_usuario'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
    $limiteEmprestimos = $config ? (int) $config['valor'] : 5; // Padrão 5 se não configurado
    
    // Contar empréstimos ativos do usuário
    $sql = "SELECT COUNT(*) as total FROM emprestimos WHERE usuario_id = :usuario_id AND status = 'emprestado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario_id' => $usuarioId]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $emprestimosAtivos = (int) $resultado['total'];
    
    // Verificar se o usuário tem débito (multas não pagas)
    $sql = "SELECT COUNT(*) as total FROM emprestimos WHERE usuario_id = :usuario_id AND status = 'emprestado' AND multa_valor > 0 AND multa_paga = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario_id' => $usuarioId]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $temDebito = (int) $resultado['total'] > 0;
    
    $podeEmprestar = $emprestimosAtivos < $limiteEmprestimos && !$temDebito;
    
    echo json_encode([
        'pode_emprestar' => $podeEmprestar,
        'emprestimos_ativos' => $emprestimosAtivos,
        'limite_emprestimos' => $limiteEmprestimos,
        'tem_debito' => $temDebito,
        'mensagem' => $podeEmprestar ? 'Pode fazer empréstimo' : 
                     ($temDebito ? 'Você possui multas não pagas' : 'Limite de empréstimos atingido')
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao verificar limite: ' . $e->getMessage()]);
}
?>
