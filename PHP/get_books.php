<?php
header('Content-Type: application/json');
require_once 'conexao.php';

try {
    $sql = "SELECT l.*, COALESCE(a.nome, 'Autor não informado') as nome_autor 
            FROM livros l 
            LEFT JOIN autores a ON l.autor_id = a.id 
            WHERE l.ativo = TRUE 
            ORDER BY l.titulo ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Converter imagens para base64 se necessário
    foreach ($livros as &$livro) {
        if (!empty($livro['imagem_capa'])) {
            // Se a imagem está em formato binário, converter para base64
            if (is_string($livro['imagem_capa']) && strlen($livro['imagem_capa']) > 100) {
                $livro['imagem_capa'] = 'data:image/jpeg;base64,' . base64_encode($livro['imagem_capa']);
            }
        }
    }
    
    echo json_encode($livros);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar livros: ' . $e->getMessage()]);
}
?>
