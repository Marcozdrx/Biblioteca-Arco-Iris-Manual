<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doacao_id = $_POST['doacao_id'] ?? null;
    $acao = $_POST['acao'] ?? null;
    
    if (!$doacao_id || !$acao) {
        echo json_encode(['error' => 'Parâmetros inválidos']);
        exit();
    }
    
    try {
        if ($acao == 'aceitar') {
            // Buscar informações da doação
            $sql = "SELECT * FROM doacoes WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $doacao_id);
            $stmt->execute();
            $doacao = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$doacao) {
                echo json_encode(['error' => 'Doação não encontrada']);
                exit();
            }
            
            // Atualizar status da doação
            $sql = "UPDATE doacoes SET status = 'aceita', data_processamento = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $doacao_id);
            $stmt->execute();
            
            // Adicionar livro ao acervo
            $sql = "INSERT INTO livros (titulo, autor_id, categoria_id, isbn, ano_publicacao, numero_paginas, descricao, estoque, editora, idioma, ativo) 
                    VALUES (:titulo, :autor_id, :categoria_id, :isbn, :ano, :paginas, :descricao, 1, :editora, :idioma, TRUE)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titulo', $doacao['titulo_livro']);
            $stmt->bindParam(':autor_id', $doacao['autor_id']);
            $stmt->bindParam(':categoria_id', $doacao['categoria_id']);
            $stmt->bindParam(':isbn', $doacao['isbn']);
            $stmt->bindParam(':ano', $doacao['ano_publicacao']);
            $stmt->bindParam(':paginas', $doacao['numero_paginas']);
            $stmt->bindParam(':descricao', $doacao['descricao']);
            $stmt->bindParam(':editora', $doacao['editora']);
            $stmt->bindParam(':idioma', $doacao['idioma']);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Doação aceita e livro adicionado ao acervo']);
            
        } elseif ($acao == 'recusar') {
            // Atualizar status da doação
            $sql = "UPDATE doacoes SET status = 'recusada', data_processamento = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $doacao_id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Doação recusada']);
            
        } else {
            echo json_encode(['error' => 'Ação inválida']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao processar doação: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método não permitido']);
}
?>
