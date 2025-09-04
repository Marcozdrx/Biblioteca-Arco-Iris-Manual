<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit();
}

$usuarioId = (int) $_SESSION['id'];
$emprestimoId = (int) $_POST['emprestimo_id'];

try {
    // Verificar se o empréstimo pertence ao usuário
    $sql = "SELECT * FROM emprestimos WHERE id = :emprestimo_id AND usuario_id = :usuario_id AND status = 'emprestado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':emprestimo_id' => $emprestimoId, ':usuario_id' => $usuarioId]);
    $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$emprestimo) {
        echo json_encode(['error' => 'Empréstimo não encontrado ou não pode ser renovado']);
        exit();
    }
    
    // Verificar se já foi renovado
    if ($emprestimo['renovado']) {
        echo json_encode(['error' => 'Este empréstimo já foi renovado']);
        exit();
    }
    
    // Verificar se passou pelo menos 6 dias desde o empréstimo
    $dataEmprestimo = new DateTime($emprestimo['data_emprestimo']);
    $dataAtual = new DateTime();
    $diferenca = $dataAtual->diff($dataEmprestimo);
    
    if ($diferenca->days < 6) {
        $diasRestantes = 6 - $diferenca->days;
        echo json_encode(['error' => "Aguarde {$diasRestantes} dias para renovar este empréstimo"]);
        exit();
    }
    
    // Renovar o empréstimo (adicionar 7 dias à data de devolução)
    $sql = "UPDATE emprestimos SET 
            data_devolucao_prevista = DATE_ADD(data_devolucao_prevista, INTERVAL 7 DAY),
            renovado = 1
            WHERE id = :emprestimo_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':emprestimo_id' => $emprestimoId]);
    
    // Buscar o empréstimo atualizado
    $sql = "SELECT data_devolucao_prevista FROM emprestimos WHERE id = :emprestimo_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':emprestimo_id' => $emprestimoId]);
    $emprestimoAtualizado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Empréstimo renovado com sucesso!',
        'nova_data_devolucao' => date('d/m/Y', strtotime($emprestimoAtualizado['data_devolucao_prevista']))
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao renovar empréstimo: ' . $e->getMessage()]);
}
?>
