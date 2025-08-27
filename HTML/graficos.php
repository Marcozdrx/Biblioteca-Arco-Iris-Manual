<?php
require_once '../PHP/conexao.php';
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
            <img src="IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Análise de Dados</span>
        </div>
    </header>

    <div class="container">
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3 id="totalLivros">0</h3>
                    <p>Total de Livros</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-content">
                    <h3 id="livrosEmprestados">0</h3>
                    <p>Livros Emprestados</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3 id="totalUsuarios">0</h3>
                    <p>Usuários Ativos</p>
                </div>
            </div>
        </div>
        
        <p id="taxaEmprestimo" style="display: none;">0%</p>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 