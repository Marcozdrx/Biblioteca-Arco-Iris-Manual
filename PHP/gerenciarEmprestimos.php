<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

try {
    switch ($acao) {
        case 'listar':
            // Listar todos os empréstimos
            $sql = "SELECT 
                        e.id,
                        e.data_emprestimo,
                        e.data_devolucao_prevista,
                        e.data_devolucao_real,
                        e.status,
                        e.renovado,
                        e.multa_valor,
                        e.multa_paga,
                        u.nome as nome_usuario,
                        u.email as email_usuario,
                        l.titulo as titulo_livro,
                        a.nome as nome_autor
                    FROM emprestimos e
                    INNER JOIN usuarios u ON e.usuario_id = u.id
                    INNER JOIN livros l ON e.livro_id = l.id
                    LEFT JOIN autores a ON l.autor_id = a.id
                    ORDER BY e.data_emprestimo DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular dias de atraso e multas
            foreach ($emprestimos as &$emprestimo) {
                $dataPrevista = new DateTime($emprestimo['data_devolucao_prevista']);
                $dataAtual = new DateTime();
                
                if ($emprestimo['status'] == 'emprestado' && $dataAtual > $dataPrevista) {
                    $diferenca = $dataAtual->diff($dataPrevista);
                    $emprestimo['dias_atraso'] = $diferenca->days;
                    $emprestimo['multa_calculada'] = $diferenca->days * 0.25;
                } else {
                    $emprestimo['dias_atraso'] = 0;
                    $emprestimo['multa_calculada'] = 0;
                }
                
                // Formatar datas
                $emprestimo['data_emprestimo_formatada'] = date('d/m/Y', strtotime($emprestimo['data_emprestimo']));
                $emprestimo['data_devolucao_prevista_formatada'] = date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista']));
                
                if ($emprestimo['data_devolucao_real']) {
                    $emprestimo['data_devolucao_real_formatada'] = date('d/m/Y', strtotime($emprestimo['data_devolucao_real']));
                }
            }
            
            echo json_encode($emprestimos);
            break;
            
        case 'devolver':
            // Confirmar devolução
            $emprestimoId = (int) $_POST['emprestimo_id'];
            
            // Buscar informações do empréstimo
            $sql = "SELECT e.*, l.id as livro_id FROM emprestimos e 
                    INNER JOIN livros l ON e.livro_id = l.id 
                    WHERE e.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimoId);
            $stmt->execute();
            $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$emprestimo) {
                echo json_encode(['error' => 'Empréstimo não encontrado']);
                exit();
            }
            
            // Atualizar status do empréstimo
            $sql = "UPDATE emprestimos SET status = 'devolvido', data_devolucao_real = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimoId);
            $stmt->execute();
            
            // Incrementar estoque do livro
            $sql = "UPDATE livros SET estoque = estoque + 1 WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimo['livro_id']);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Devolução confirmada']);
            break;
            
        case 'renovar':
            // Renovar empréstimo
            $emprestimoId = (int) $_POST['emprestimo_id'];
            
            // Verificar se o empréstimo existe e não foi renovado
            $sql = "SELECT * FROM emprestimos WHERE id = :id AND status = 'emprestado'";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimoId);
            $stmt->execute();
            $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$emprestimo) {
                echo json_encode(['error' => 'Empréstimo não encontrado ou não pode ser renovado']);
                exit();
            }
            
            if ($emprestimo['renovado']) {
                echo json_encode(['error' => 'Este empréstimo já foi renovado']);
                exit();
            }
            
            // Renovar o empréstimo
            $sql = "UPDATE emprestimos SET 
                    data_devolucao_prevista = DATE_ADD(data_devolucao_prevista, INTERVAL 7 DAY),
                    renovado = 1
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $emprestimoId);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Empréstimo renovado com sucesso']);
            break;
            
        case 'estatisticas':
            // Buscar estatísticas dos empréstimos
            $sql = "SELECT 
                        COUNT(*) as total_emprestimos,
                        COUNT(CASE WHEN status = 'emprestado' THEN 1 END) as emprestimos_ativos,
                        COUNT(CASE WHEN status = 'devolvido' THEN 1 END) as emprestimos_devolvidos,
                        COUNT(CASE WHEN status = 'atrasado' THEN 1 END) as emprestimos_atrasados
                    FROM emprestimos";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calcular valor total de multas
            $sql = "SELECT SUM(multa_valor) as total_multas FROM emprestimos WHERE multa_valor > 0";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $multas = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats['total_multas'] = $multas['total_multas'] ?? 0;
            
            echo json_encode($stats);
            break;
            
        default:
            echo json_encode(['error' => 'Ação não reconhecida']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
