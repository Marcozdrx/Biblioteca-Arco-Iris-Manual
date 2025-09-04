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
    // Executar cálculo automático de multas
    $sql = "CALL CalcularMultas()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Buscar empréstimos do usuário com informações do livro
    $sql = "SELECT 
                e.id,
                e.data_emprestimo,
                e.data_devolucao_prevista,
                e.data_devolucao_real,
                e.status,
                e.renovado,
                e.multa_valor,
                e.multa_paga,
                l.titulo as titulo_livro,
                l.imagem_capa,
                a.nome as nome_autor,
                c.nome as categoria
            FROM emprestimos e
            INNER JOIN livros l ON e.livro_id = l.id
            LEFT JOIN autores a ON l.autor_id = a.id
            LEFT JOIN categorias c ON l.categoria_id = c.id
            WHERE e.usuario_id = :usuario_id
            ORDER BY e.data_emprestimo DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario_id' => $usuarioId]);
    $emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular dias de atraso e multas para cada empréstimo
    foreach ($emprestimos as &$emprestimo) {
        $dataPrevista = new DateTime($emprestimo['data_devolucao_prevista']);
        $dataAtual = new DateTime();
        
        if ($emprestimo['status'] == 'emprestado' && $dataAtual > $dataPrevista) {
            $diferenca = $dataAtual->diff($dataPrevista);
            $emprestimo['dias_atraso'] = $diferenca->days;
            
            // Calcular multa (R$ 0,25 por dia)
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
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar empréstimos: ' . $e->getMessage()]);
}
?>
