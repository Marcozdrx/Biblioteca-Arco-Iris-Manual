<?php
require_once 'conexao.php';

try {
    // Executar o procedimento CalcularMultas
    $sql = "CALL CalcularMultas()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Multas calculadas com sucesso']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao calcular multas: ' . $e->getMessage()]);
}
?>
