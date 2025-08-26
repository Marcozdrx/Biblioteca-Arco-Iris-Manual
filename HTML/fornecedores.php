<?php
require_once 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="CSS/fornecedores.css">
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Fornecedores</span>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Gestão de Fornecedores</h1>
            <button class="add-supplier-btn" onclick="abrirModalAdicionar()">
                <span>+</span> Adicionar Fornecedor
            </button>
        </div>

        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-content">
                    <h3 id="totalFornecedores">0</h3>
                    <p>Total de Fornecedores</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3 id="totalDoacoes">0</h3>
                    <p>Total de Doações</p>
                </div>
            </div>
        </div>

        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Pesquisar fornecedores...">
                <button class="search-btn">🔍</button>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="todos">📋 Todos</button>
                <button class="filter-btn" data-filter="ativos">✅ Ativos</button>
                <button class="filter-btn" data-filter="com-doacoes">📚 Com Doações</button>
                <button class="filter-btn" data-filter="top-fornecedores">🏆 Top Fornecedores</button>
            </div>
        </div>

        <div class="suppliers-table-container">
            <div class="table-wrapper">
                <table id="suppliersTable">
                <thead>
                    <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>Telefone</th>
                            <th>Doações</th>
                            <th>Status</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                    </tr>
                </thead>
                    <tbody id="suppliersTableBody">
                        <!-- Fornecedores serão inseridos aqui pelo JavaScript -->
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar fornecedor -->
    <div id="supplierModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2 id="modalTitle">Adicionar Fornecedor</h2>
            <form id="supplierForm">
                <div class="form-group">
                    <label for="supplierName">Nome Completo:</label>
                    <input type="text" id="supplierName" required>
                </div>
                <div class="form-group">
                    <label for="supplierDocument">CPF/CNPJ:</label>
                    <input type="text" id="supplierDocument" maxlength="18" placeholder="000.000.000-00 ou 00.000.000/0000-00" required>
                </div>
                <div class="form-group">
                    <label for="supplierPhone">Telefone:</label>
                    <input type="tel" id="supplierPhone" maxlength="15" placeholder="(00) 00000-0000" required>
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
</body>
</html> 