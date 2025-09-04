<?php
session_start();
require_once 'conexao.php';

// Verificar login do usuário
if (!isset($_SESSION['id'])) {
    header('Location: ../HTML/login.php');
    exit();
}

$usuarioId = (int) $_SESSION['id'];
$livroId = isset($_GET['livro_id']) ? (int) $_GET['livro_id'] : 0;

if ($livroId <= 0) {
    header('Location: ../HTML/usuario.php');
    exit();
}

try {
    // Verificar se já existe empréstimo ativo do mesmo livro para o usuário
    $sql = "SELECT COUNT(*) AS total FROM emprestimos WHERE usuario_id = :usuario_id AND livro_id = :livro_id AND status = 'emprestado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':usuario_id' => $usuarioId, ':livro_id' => $livroId]);
    $jaTemEmprestimo = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;

    if ($jaTemEmprestimo) {
        header('Location: ../HTML/emprestimos.php');
        exit();
    }

    // Verificar estoque
    $sql = "SELECT estoque FROM livros WHERE id = :livro_id AND ativo = TRUE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':livro_id' => $livroId]);
    $livro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$livro) {
        header('Location: ../HTML/usuario.php');
        exit();
    }

    if ((int) $livro['estoque'] <= 0) {
        header('Location: ../HTML/detalhes-livro.php?id=' . $livroId);
        exit();
    }

    // Criar empréstimo: data prevista = hoje + 14 dias
    $sql = "INSERT INTO emprestimos (usuario_id, livro_id, data_emprestimo, data_devolucao_prevista, status) VALUES (:usuario_id, :livro_id, NOW(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'emprestado')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':livro_id' => $livroId
    ]);

    // Decrementar estoque
    $sql = "UPDATE livros SET estoque = estoque - 1 WHERE id = :livro_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':livro_id' => $livroId]);

    // Redirecionar para Meus Empréstimos
    header('Location: ../HTML/emprestimos.php');
    exit();
} catch (PDOException $e) {
    // Em caso de erro, retornar ao detalhes com mensagem simples via query (opcional)
    header('Location: ../HTML/detalhes-livro.php?id=' . $livroId);
    exit();
}
