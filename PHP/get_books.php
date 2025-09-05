<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

try {
    // Buscar livros disponíveis com informações de autores e categorias
    $sql = "SELECT 
                l.id,
                l.titulo,
                l.estoque,
                l.imagem_capa,
                l.descricao,
                a.nome as nome_autor,
                c.nome as categoria
            FROM livros l
            LEFT JOIN autores a ON l.autor_id = a.id
            LEFT JOIN categorias c ON l.categoria_id = c.id
            WHERE l.ativo = 1 AND l.estoque > 0
            ORDER BY l.titulo ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($livros);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar livros: ' . $e->getMessage()]);
}
?>
