<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] == 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emprestimo_id = $_POST['emprestimo_id'] ?? null;
    $acao = $_POST['acao'] ?? null;
    
    if (!$emprestimo_id || !$acao) {
        echo json_encode(['error' => 'Parâmetros inválidos']);
        exit();
    }
    
    try {
        if ($acao == 'confirmar') {
            // Buscar informações do empréstimo
            $sql = "SELECT e.*, l.id as livro_id FROM emprestimos e 
                    INNER JOIN livros l ON e.livro_id = l.id 
                    WHERE e.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimo_id);
            $stmt->execute();
            $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$emprestimo) {
                echo json_encode(['error' => 'Empréstimo não encontrado']);
                exit();
            }
            
            // Atualizar status do empréstimo
            $sql = "UPDATE emprestimos SET status = 'devolvido', data_devolucao_real = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimo_id);
            $stmt->execute();
            
            // Incrementar estoque do livro
            $sql = "UPDATE livros SET estoque = estoque + 1 WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimo['livro_id']);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Devolução confirmada']);
            
        } elseif ($acao == 'lembrete') {
            // Buscar informações do empréstimo e usuário
            $sql = "SELECT e.*, u.nome as nome_usuario, u.email as email_usuario, l.titulo as titulo_livro 
                    FROM emprestimos e 
                    INNER JOIN usuarios u ON e.usuario_id = u.id 
                    INNER JOIN livros l ON e.livro_id = l.id 
                    WHERE e.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimo_id);
            $stmt->execute();
            $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$emprestimo) {
                echo json_encode(['error' => 'Empréstimo não encontrado']);
                exit();
            }
            
            // Aqui você pode implementar o envio de email
            // Por enquanto, vamos apenas simular o envio
            $diasAtraso = max(0, (strtotime('today') - strtotime($emprestimo['data_devolucao_prevista'])) / (60*60*24));
            
            // Registrar o lembrete no banco (opcional)
            $sql = "INSERT INTO lembretes (emprestimo_id, data_envio, tipo) VALUES (:emprestimo_id, NOW(), 'devolucao')";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':emprestimo_id', $emprestimo_id);
            $stmt->execute();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Lembrete enviado para ' . $emprestimo['email_usuario'],
                'dias_atraso' => $diasAtraso
            ]);
            
        } else {
            echo json_encode(['error' => 'Ação inválida']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao processar devolução: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>
