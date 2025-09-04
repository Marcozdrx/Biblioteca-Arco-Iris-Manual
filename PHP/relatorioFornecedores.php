<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../HTML/login.php");
    exit();
}

// Buscar dados dos fornecedores e suas doações
$sql = "SELECT f.id, f.nome, f.cpf_cnpj, f.telefone, f.status, f.total_doacoes,
               COUNT(d.id) as total_doacoes_realizadas
        FROM fornecedores f
        LEFT JOIN doacoes d ON f.id = d.usuario_id AND d.tipo_doacao = 'livro' AND d.status = 'aprovada'
        GROUP BY f.id, f.nome, f.cpf_cnpj, f.telefone, f.status, f.total_doacoes
        ORDER BY f.total_doacoes DESC, f.nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas gerais
$sql = "SELECT COUNT(*) as total_fornecedores FROM fornecedores";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$total_fornecedores = $stmt->fetch(PDO::FETCH_ASSOC)['total_fornecedores'];

$sql = "SELECT COUNT(*) as fornecedores_ativos FROM fornecedores WHERE status = 'ativo'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores_ativos = $stmt->fetch(PDO::FETCH_ASSOC)['fornecedores_ativos'];

$sql = "SELECT SUM(total_doacoes) as total_doacoes_geral FROM fornecedores";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$total_doacoes_geral = $stmt->fetch(PDO::FETCH_ASSOC)['total_doacoes_geral'] ?? 0;

// Configurar headers para download
$filename = 'relatorio_fornecedores_' . date('Y-m-d_H-i-s') . '.txt';
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Gerar conteúdo do relatório
echo "========================================\n";
echo "  RELATÓRIO DE FORNECEDORES - BIBLIOTECA\n";
echo "    Biblioteca Arco-Íris\n";
echo "========================================\n\n";

echo "Data de geração: " . date('d/m/Y H:i:s') . "\n\n";

echo "ESTATÍSTICAS GERAIS:\n";
echo "--------------------\n";
echo "Total de fornecedores: " . $total_fornecedores . "\n";
echo "Fornecedores ativos: " . $fornecedores_ativos . "\n";
echo "Total de livros doados: " . $total_doacoes_geral . "\n\n";

echo "LISTA DE FORNECEDORES:\n";
echo "======================\n\n";

if (!empty($fornecedores)) {
    foreach ($fornecedores as $index => $fornecedor) {
        echo ($index + 1) . ". " . $fornecedor['nome'] . "\n";
        echo "   CPF/CNPJ: " . $fornecedor['cpf_cnpj'] . "\n";
        echo "   Telefone: " . $fornecedor['telefone'] . "\n";
        echo "   Status: " . ucfirst($fornecedor['status']) . "\n";
        echo "   Quantidade de livros doados: " . $fornecedor['total_doacoes'] . "\n";
        echo "   Doações aprovadas: " . $fornecedor['total_doacoes_realizadas'] . "\n\n";
    }
} else {
    echo "Nenhum fornecedor cadastrado.\n\n";
}

echo "TOP FORNECEDORES (por quantidade de doações):\n";
echo "============================================\n";
$top_fornecedores = array_filter($fornecedores, function($f) {
    return $f['total_doacoes'] > 0;
});

if (!empty($top_fornecedores)) {
    $top_fornecedores = array_slice($top_fornecedores, 0, 5);
    foreach ($top_fornecedores as $index => $fornecedor) {
        echo ($index + 1) . ". " . $fornecedor['nome'] . " - " . $fornecedor['total_doacoes'] . " livros\n";
    }
} else {
    echo "Nenhum fornecedor com doações registradas.\n";
}

echo "\n========================================\n";
echo "Relatório gerado automaticamente pelo\n";
echo "Sistema Biblioteca Arco-Íris\n";
echo "========================================\n";
?>
