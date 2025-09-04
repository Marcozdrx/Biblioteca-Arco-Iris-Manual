<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Buscar dados para os gráficos
$stats = [];

// Total de livros
$sql = "SELECT COUNT(*) as total FROM livros WHERE ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['totalLivros'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Livros emprestados
$sql = "SELECT COUNT(*) as total FROM emprestimos WHERE status = 'emprestado'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['livrosEmprestados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de usuários ativos
$sql = "SELECT COUNT(*) as total FROM usuarios WHERE ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stats['totalUsuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Taxa de empréstimo
$stats['taxaEmprestimo'] = $stats['totalLivros'] > 0 ? round(($stats['livrosEmprestados'] / $stats['totalLivros']) * 100, 1) : 0;

// Top 10 livros mais emprestados
$sql = "SELECT l.titulo, a.nome as autor, COUNT(e.id) as total_emprestimos, l.estoque
        FROM livros l 
        LEFT JOIN autores a ON l.autor_id = a.id
        LEFT JOIN emprestimos e ON l.id = e.livro_id
        WHERE l.ativo = TRUE
        GROUP BY l.id, l.titulo, a.nome, l.estoque
        ORDER BY total_emprestimos DESC
        LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$topLivros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Empréstimos por mês (últimos 6 meses)
$sql = "SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') as mes, COUNT(*) as total
        FROM emprestimos 
        WHERE data_emprestimo >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(data_emprestimo, '%Y-%m')
        ORDER BY mes DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$emprestimosMensais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Últimos empréstimos
$sql = "SELECT e.data_emprestimo, l.titulo, u.nome as usuario, e.status
        FROM emprestimos e
        INNER JOIN livros l ON e.livro_id = l.id
        INNER JOIN usuarios u ON e.usuario_id = u.id
        ORDER BY e.data_emprestimo DESC
        LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ultimosEmprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/graficos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Análise de Dados</span>
        </div>
    </header>

    <div class="container">
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3><?= $stats['totalLivros'] ?></h3>
                    <p>Total de Livros</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-content">
                    <h3><?= $stats['livrosEmprestados'] ?></h3>
                    <p>Livros Emprestados</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3><?= $stats['totalUsuarios'] ?></h3>
                    <p>Usuários Ativos</p>
                </div>
            </div>
        </div>
        
        <p id="taxaEmprestimo" style="display: none;"><?= $stats['taxaEmprestimo'] ?>%</p>
        <div class="charts-grid">
            <div class="chart-container">
                <h2>Livros Mais Emprestados</h2>
                <canvas id="livrosPopularesChart"></canvas>
            </div>
            
            <div class="chart-container">
                <h2>Empréstimos por Mês</h2>
                <canvas id="emprestimosMensaisChart"></canvas>
            </div>
        </div>

        <div class="data-tables">
            <div class="table-container">
                <h2>Top 10 Livros Mais Emprestados</h2>
                <div class="table-wrapper">
                    <table id="topLivrosTable">
                        <thead>
                            <tr>
                                <th>Posição</th>
                                <th>Livro</th>
                                <th>Autor</th>
                                <th>Empréstimos</th>
                                <th>Disponível</th>
                            </tr>
                        </thead>
                        <tbody id="topLivrosTableBody">
                            <?php foreach ($topLivros as $index => $livro): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($livro['titulo']) ?></td>
                                    <td><?= htmlspecialchars($livro['autor'] ?? 'Autor não informado') ?></td>
                                    <td><?= $livro['total_emprestimos'] ?></td>
                                    <td><?= $livro['estoque'] > 0 ? 'Sim' : 'Não' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-container">
                <h2>Últimos Empréstimos</h2>
                <div class="table-wrapper">
                    <table id="ultimosEmprestimosTable">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Livro</th>
                                <th>Usuário</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ultimosEmprestimosTableBody">
                            <?php foreach ($ultimosEmprestimos as $emprestimo): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($emprestimo['data_emprestimo'])) ?></td>
                                    <td><?= htmlspecialchars($emprestimo['titulo']) ?></td>
                                    <td><?= htmlspecialchars($emprestimo['usuario']) ?></td>
                                    <td><?= ucfirst($emprestimo['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados dos gráficos vindos do PHP
        const topLivrosData = <?= json_encode($topLivros) ?>;
        const emprestimosMensaisData = <?= json_encode($emprestimosMensais) ?>;
        
        // Gráfico de livros mais populares
        const livrosPopularesCtx = document.getElementById('livrosPopularesChart').getContext('2d');
        new Chart(livrosPopularesCtx, {
            type: 'bar',
            data: {
                labels: topLivrosData.map(livro => livro.titulo.substring(0, 20) + '...'),
                datasets: [{
                    label: 'Número de Empréstimos',
                    data: topLivrosData.map(livro => livro.total_emprestimos),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Gráfico de empréstimos mensais
        const emprestimosMensaisCtx = document.getElementById('emprestimosMensaisChart').getContext('2d');
        new Chart(emprestimosMensaisCtx, {
            type: 'line',
            data: {
                labels: emprestimosMensaisData.map(item => {
                    const [year, month] = item.mes.split('-');
                    const monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 
                                     'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                    return `${monthNames[parseInt(month)-1]}/${year}`;
                }).reverse(),
                datasets: [{
                    label: 'Empréstimos',
                    data: emprestimosMensaisData.map(item => item.total).reverse(),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 