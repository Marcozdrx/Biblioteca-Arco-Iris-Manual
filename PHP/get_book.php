<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $bookId = $_GET['id'] ?? null;
    
    if (!$bookId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID do livro não fornecido']);
        exit();
    }
    
    try {
        $sql = "SELECT l.*, a.nome as nome_autor, c.nome as nome_categoria 
                FROM livros l 
                LEFT JOIN autores a ON l.autor_id = a.id 
                LEFT JOIN categorias c ON l.categoria_id = c.id 
                WHERE l.id = :id AND l.ativo = TRUE";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $bookId);
        $stmt->execute();
        
        $livro = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$livro) {
            http_response_code(404);
            echo json_encode(['error' => 'Livro não encontrado']);
            exit();
        }
        
        // Converter imagem para base64 se necessário
        if (!empty($livro['imagem_capa'])) {
            if (is_string($livro['imagem_capa']) && strlen($livro['imagem_capa']) > 100) {
                $livro['imagem_capa'] = 'data:image/jpeg;base64,' . base64_encode($livro['imagem_capa']);
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($livro);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}
?>
