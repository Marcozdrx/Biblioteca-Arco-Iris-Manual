<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../HTML/login.php");
    exit();
}

// Buscar dados para o relatório
$relatorio = [];

// Livro mais emprestado
$sql = "SELECT l.titulo, a.nome as autor, COUNT(e.id) as total_emprestimos
        FROM livros l 
        LEFT JOIN autores a ON l.autor_id = a.id
        LEFT JOIN emprestimos e ON l.id = e.livro_id
        WHERE l.ativo = TRUE
        GROUP BY l.id, l.titulo, a.nome
        ORDER BY total_emprestimos DESC
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['livro_mais_emprestado'] = $stmt->fetch(PDO::FETCH_ASSOC);

// Quantidade de usuários que emprestaram
$sql = "SELECT COUNT(DISTINCT usuario_id) as total_usuarios_emprestaram
        FROM emprestimos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['usuarios_emprestaram'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios_emprestaram'];

// Quantidade total de livros emprestados
$sql = "SELECT COUNT(*) as total_emprestimos
        FROM emprestimos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['total_emprestimos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_emprestimos'];

// Top 10 livros mais emprestados
$sql = "SELECT l.titulo, a.nome as autor, COUNT(e.id) as total_emprestimos
        FROM livros l 
        LEFT JOIN autores a ON l.autor_id = a.id
        LEFT JOIN emprestimos e ON l.id = e.livro_id
        WHERE l.ativo = TRUE
        GROUP BY l.id, l.titulo, a.nome
        ORDER BY total_emprestimos DESC
        LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['top_livros'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas gerais
$sql = "SELECT COUNT(*) as total_livros FROM livros WHERE ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['total_livros'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_livros'];

$sql = "SELECT COUNT(*) as total_usuarios FROM usuarios WHERE ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$relatorio['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

// Configurar headers para download
$filename = 'relatorio_graficos_' . date('Y-m-d_H-i-s') . '.txt';
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Gerar conteúdo do relatório
echo "========================================\n";
echo "    RELATÓRIO DE GRÁFICOS - BIBLIOTECA\n";
echo "    Biblioteca Arco-Íris\n";
echo "========================================\n\n";

echo "Data de geração: " . date('d/m/Y H:i:s') . "\n\n";

echo "ESTATÍSTICAS GERAIS:\n";
echo "--------------------\n";
echo "Total de livros: " . $relatorio['total_livros'] . "\n";
echo "Total de usuários: " . $relatorio['total_usuarios'] . "\n";
echo "Total de empréstimos realizados: " . $relatorio['total_emprestimos'] . "\n";
echo "Usuários que já emprestaram: " . $relatorio['usuarios_emprestaram'] . "\n\n";

echo "LIVRO MAIS EMPRESTADO:\n";
echo "----------------------\n";
if ($relatorio['livro_mais_emprestado']) {
    echo "Título: " . $relatorio['livro_mais_emprestado']['titulo'] . "\n";
    echo "Autor: " . ($relatorio['livro_mais_emprestado']['autor'] ?? 'Autor não informado') . "\n";
    echo "Total de empréstimos: " . $relatorio['livro_mais_emprestado']['total_emprestimos'] . "\n\n";
} else {
    echo "Nenhum empréstimo registrado.\n\n";
}

echo "TOP 10 LIVROS MAIS EMPRESTADOS:\n";
echo "-------------------------------\n";
if (!empty($relatorio['top_livros'])) {
    foreach ($relatorio['top_livros'] as $index => $livro) {
        echo ($index + 1) . ". " . $livro['titulo'] . "\n";
        echo "   Autor: " . ($livro['autor'] ?? 'Autor não informado') . "\n";
        echo "   Empréstimos: " . $livro['total_emprestimos'] . "\n\n";
    }
} else {
    echo "Nenhum empréstimo registrado.\n\n";
}

echo "========================================\n";
echo "Relatório gerado automaticamente pelo\n";
echo "Sistema Biblioteca Arco-Íris\n";
echo "========================================\n";
?>
