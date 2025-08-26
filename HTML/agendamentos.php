<?php
require_once 'conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="CSS/agendamentos.css">
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Agendamentos</span>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Gestão de Agendamentos</h1>
            <div class="header-actions">
                <button class="btn-export" onclick="exportarAgendamentos()">
                    📊 Exportar Relatório
                </button>
                <button class="btn-refresh" onclick="carregarAgendamentos()">
                    🔄 Atualizar
                </button>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3 id="totalAgendamentos">0</h3>
                    <p>Total de Agendamentos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <h3 id="agendamentosHoje">0</h3>
                    <p>Agendamentos Hoje</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏰</div>
                <div class="stat-content">
                    <h3 id="agendamentosPendentes">0</h3>
                    <p>Pendentes</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3 id="livrosMaisAgendados">0</h3>
                    <p>Livros Mais Agendados</p>
                </div>
            </div>
        </div>

        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Pesquisar agendamentos...">
                <button class="search-btn">🔍</button>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="todos">Todos</button>
                <button class="filter-btn" data-filter="hoje">Hoje</button>
                <button class="filter-btn" data-filter="pendentes">Pendentes</button>
                <button class="filter-btn" data-filter="concluidos">Concluídos</button>
            </div>
        </div>

        <div class="agendamentos-table-container">
            <div class="table-wrapper">
                <table id="agendamentosTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Livro</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th>Data Agendamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="agendamentosTableBody">
                        <!-- Agendamentos serão inseridos aqui pelo JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para detalhes do agendamento -->
    <div id="agendamentoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2 id="modalTitle">Detalhes do Agendamento</h2>
            <div class="agendamento-details">
                <div class="detail-row">
                    <label>Usuário:</label>
                    <span id="modalUsuario"></span>
                </div>
                <div class="detail-row">
                    <label>Livro:</label>
                    <span id="modalLivro"></span>
                </div>
                <div class="detail-row">
                    <label>Data:</label>
                    <span id="modalData"></span>
                </div>
                <div class="detail-row">
                    <label>Horário:</label>
                    <span id="modalHorario"></span>
                </div>
                <div class="detail-row">
                    <label>Status:</label>
                    <span id="modalStatus"></span>
                </div>
                <div class="detail-row">
                    <label>Data do Agendamento:</label>
                    <span id="modalDataAgendamento"></span>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-concluir" onclick="concluirAgendamento()">✅ Concluir</button>
                <button class="btn-cancelar" onclick="cancelarAgendamento()">❌ Cancelar</button>
                <button class="btn-fechar" onclick="fecharModal()">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação -->
    <div id="confirmModal" class="modal">
        <div class="modal-content confirm-modal">
            <h3 id="confirmTitle">Confirmar Ação</h3>
            <p id="confirmMessage">Tem certeza que deseja realizar esta ação?</p>
            <div class="confirm-actions">
                <button class="btn-cancel" onclick="fecharConfirmModal()">Cancelar</button>
                <button class="btn-confirm" onclick="confirmarAcao()">Confirmar</button>
            </div>
        </div>
    </div>

   </body>
</html>