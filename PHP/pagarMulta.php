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
$metodoPagamento = $_POST['metodo_pagamento'];

try {
    // Verificar se o empréstimo pertence ao usuário
    $sql = "SELECT * FROM emprestimos WHERE id = :emprestimo_id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':emprestimo_id' => $emprestimoId, ':usuario_id' => $usuarioId]);
    $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$emprestimo) {
        echo json_encode(['error' => 'Empréstimo não encontrado']);
        exit();
    }
    
    // Calcular multa atual
    $dataPrevista = new DateTime($emprestimo['data_devolucao_prevista']);
    $dataAtual = new DateTime();
    $multaCalculada = 0;
    
    if ($emprestimo['status'] == 'emprestado' && $dataAtual > $dataPrevista) {
        $diferenca = $dataAtual->diff($dataPrevista);
        $multaCalculada = $diferenca->days * 0.25;
    }
    
    // Atualizar multa no banco
    $sql = "UPDATE emprestimos SET 
            multa_valor = :multa_valor,
            multa_paga = 1,
            data_multa_paga = NOW()
            WHERE id = :emprestimo_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':multa_valor' => $multaCalculada,
        ':emprestimo_id' => $emprestimoId
    ]);
    
    // Registrar o pagamento no histórico (opcional)
    $sql = "INSERT INTO historico_atividades (usuario_id, tipo_acao, descricao, dados_json) 
            VALUES (:usuario_id, 'pagamento_multa', 'Pagamento de multa realizado', :dados_json)";
    
    $dadosJson = json_encode([
        'emprestimo_id' => $emprestimoId,
        'valor_multa' => $multaCalculada,
        'metodo_pagamento' => $metodoPagamento,
        'data_pagamento' => date('Y-m-d H:i:s')
    ]);
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':dados_json' => $dadosJson
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Multa paga com sucesso!',
        'valor_pago' => $multaCalculada,
        'metodo_pagamento' => $metodoPagamento
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar pagamento: ' . $e->getMessage()]);
}
?>
