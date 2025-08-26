<?php
require_once '../PHP/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="CSS/usuarios.css">
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Usuários</span>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Gestão de Usuários</h1>
            <button class="add-user-btn" onclick="abrirModalAdicionar()">
                <span>+</span> Adicionar Usuário
            </button>
        </div>

        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3 id="totalUsuarios">0</h3>
                    <p>Total de Usuários</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3 id="usuariosAtivos">0</h3>
                    <p>Usuários Ativos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3 id="usuariosBloqueados">0</h3>
                    <p>Usuários Bloqueados</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-content">
                    <h3 id="mediaEmprestimos">0</h3>
                    <p>Média de Empréstimos</p>
                </div>
            </div>
        </div>

        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Pesquisar usuários...">
                <button class="search-btn">🔍</button>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="todos">Todos</button>
                <button class="filter-btn" data-filter="ativos">Ativos</button>
                <button class="filter-btn" data-filter="bloqueados">Bloqueados</button>
                <button class="filter-btn" data-filter="com-emprestimos">Com Empréstimos</button>
            </div>
        </div>

        <div class="users-table-container">
            <div class="table-wrapper">
                <table id="usersTable">
                <thead>
                    <tr>
                            <th>ID</th>
                            <th>Nome</th>
                        <th>Status</th>
                            <th>Empréstimos Ativos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                        <!-- Usuários serão inseridos aqui pelo JavaScript -->
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar usuário -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2 id="modalTitle">Adicionar Usuário</h2>
            <form id="userForm">
                <div class="form-group">
                    <label for="userName">Nome Completo:</label>
                    <input type="text" id="userName" required>
                </div>
                <div class="form-group">
                    <label for="userPassword">Senha:</label>
                    <input type="password" id="userPassword" required>
                </div>
                <div class="form-group">
                    <label for="userStatus">Status:</label>
                    <select id="userStatus">
                        <option value="ativo">Ativo</option>
                        <option value="bloqueado">Bloqueado</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Salvar</button>
                </div>
            </form>
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

    <!-- Modal para gerenciar multas -->
    <div id="multaModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModalMulta()">&times;</span>
            <h2 id="multaModalTitle">Gerenciar Multas</h2>
            <div id="multaModalContent">
                <div class="user-info">
                    <h3 id="userNameMulta"></h3>
                </div>
                <div class="emprestimos-section">
                    <h4>Empréstimos Ativos</h4>
                    <div id="emprestimosList" class="emprestimos-list">
                        <!-- Lista de empréstimos será carregada aqui -->
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="fecharModalMulta()">Fechar</button>
            </div>
        </div>
    </div>
</body>
</html> 