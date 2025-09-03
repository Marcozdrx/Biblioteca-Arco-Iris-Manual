<?php
/**
 * ARQUIVO PARA BUSCAR USUÁRIOS
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

try {
    // Buscar todos os usuários
    $sql = "SELECT 
                id, 
                nome, 
                cpf, 
                telefone, 
                email, 
                is_admin, 
                ativo,
                tem_debito,
                tem_doacao_pendente
            FROM usuarios 
            ORDER BY nome ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll();
    
    // Buscar estatísticas
    $sql_stats = "SELECT 
        COUNT(*) as total_usuarios,
        SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as usuarios_ativos,
        SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as usuarios_bloqueados,
        SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) as usuarios_admin
        FROM usuarios";
    
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch();
    
    // Buscar empréstimos ativos por usuário
    $sql_emprestimos = "SELECT 
        u.id,
        COUNT(e.id) as emprestimos_ativos
        FROM usuarios u
        LEFT JOIN emprestimos e ON u.id = e.usuario_id 
        AND e.data_devolucao_real IS NULL
        GROUP BY u.id";
    
    $stmt_emprestimos = $pdo->prepare($sql_emprestimos);
    $stmt_emprestimos->execute();
    $emprestimos_por_usuario = $stmt_emprestimos->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Adicionar informações de empréstimos aos usuários
    foreach ($usuarios as &$usuario) {
        $usuario['emprestimos_ativos'] = $emprestimos_por_usuario[$usuario['id']] ?? 0;
    }
    
    // Calcular média de empréstimos
    $total_emprestimos = array_sum($emprestimos_por_usuario);
    $stats['media_emprestimos'] = $stats['total_usuarios'] > 0 ? 
        round($total_emprestimos / $stats['total_usuarios'], 1) : 0;
    
    // Retornar dados em JSON
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao buscar usuários: ' . $e->getMessage()
    ]);
}
?>
