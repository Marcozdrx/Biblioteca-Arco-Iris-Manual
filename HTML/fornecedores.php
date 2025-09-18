<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/fornecedores.css">
    <link rel="stylesheet" href="../CSS/modais.css">
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Fornecedores</span>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Gestão de Fornecedores</h1>
            <div class="header-buttons">
                <button class="report-btn" onclick="gerarRelatorioFornecedores()">
                    📊 Relatório
                </button>
                <button class="add-supplier-btn" onclick="window.location.href='cadastrar-fornecedores.php'">
                    <span>+</span> Adicionar Fornecedor
                </button>
            </div>
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
                            <th></th>
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



    <script>
        // Variáveis globais
        let fornecedores = [];
        let fornecedoresFiltrados = [];
        let acaoConfirmada = null;

        // Carregar fornecedores ao inicializar a página
        document.addEventListener('DOMContentLoaded', function() {
            carregarFornecedores();
            configurarEventos();
        });

        // Função para carregar fornecedores do banco
        async function carregarFornecedores() {
            try {
                const response = await fetch('../PHP/buscarFornecedores.php');
                const data = await response.json();
                
                if (data.success) {
                    fornecedores = data.fornecedores;
                    fornecedoresFiltrados = [...fornecedores];
                    
                    // Atualizar estatísticas
                    document.getElementById('totalFornecedores').textContent = data.stats.total_fornecedores || 0;
                    document.getElementById('totalDoacoes').textContent = data.stats.total_doacoes || 0;
                    
                    // Exibir fornecedores na tabela
                    exibirFornecedores(fornecedoresFiltrados);
                } else {
                    console.error('Erro ao carregar fornecedores:', data.error);
                    showNotification('Erro ao carregar fornecedores: ' + data.error, 'error');
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                showNotification('Erro ao conectar com o servidor', 'error');
            }
        }

        // Função para exibir fornecedores na tabela
        function exibirFornecedores(listaFornecedores) {
            const tbody = document.getElementById('suppliersTableBody');
            tbody.innerHTML = '';
            
            if (listaFornecedores.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">Nenhum fornecedor encontrado</td></tr>';
                return;
            }
            
            listaFornecedores.forEach(fornecedor => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${fornecedor.id}</td>
                    <td>${fornecedor.nome}</td>
                    <td>${fornecedor.cpf_cnpj}</td>
                    <td>${fornecedor.telefone}</td>
                    <td>${fornecedor.total_doacoes}</td>
                    <td>
                        <span class="status-badge ${fornecedor.status === 'ativo' ? 'active' : 'inactive'}">
                            ${fornecedor.status === 'ativo' ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <button class="action-btn edit-btn" onclick="editarFornecedor(${fornecedor.id})" title="Editar">
                            ✏️
                        </button>
                        <button class="action-btn delete-btn" onclick="excluirFornecedor(${fornecedor.id})" title="Excluir">
                            🗑️
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Função para configurar eventos
        function configurarEventos() {
            // Busca
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.querySelector('.search-btn');
            
            searchInput.addEventListener('input', filtrarFornecedores);
            searchBtn.addEventListener('click', filtrarFornecedores);
            
            // Filtros
            const filterBtns = document.querySelectorAll('.filter-btn');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover classe active de todos os botões
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Adicionar classe active ao botão clicado
                    this.classList.add('active');
                    
                    const filtro = this.getAttribute('data-filter');
                    aplicarFiltro(filtro);
                });
            });
            
            // Formulário de fornecedor
            const supplierForm = document.getElementById('supplierForm');
            supplierForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const editId = this.getAttribute('data-edit-id');
                const isEdit = editId !== null;
                
                const dados = {
                    nome: document.getElementById('supplierName').value.trim(),
                    cpf_cnpj: document.getElementById('supplierDocument').value.trim(),
                    telefone: document.getElementById('supplierPhone').value.trim()
                };
                
                // Validações básicas
                if (!dados.nome || !dados.cpf_cnpj || !dados.telefone) {
                    showNotification('Por favor, preencha todos os campos obrigatórios.', 'error');
                    return;
                }
                
                // Se for edição, adicionar o ID
                if (isEdit) {
                    dados.id = parseInt(editId);
                }
                
                // Mostrar confirmação de edição
                const actionText = isEdit ? 'editar' : 'cadastrar';
                const confirmMessage = isEdit ? 
                    `Tem certeza que deseja salvar as alterações do fornecedor "${dados.nome}"?` :
                    `Tem certeza que deseja cadastrar o fornecedor "${dados.nome}"?`;
                
                showEditConfirmation(
                    isEdit ? 'Confirmar Edição de Fornecedor' : 'Confirmar Cadastro de Fornecedor',
                    confirmMessage,
                    function() {
                        salvarFornecedor(dados);
                    }
                );
            });
        }

        // Função para filtrar fornecedores
        function filtrarFornecedores() {
            const termo = document.getElementById('searchInput').value.toLowerCase();
            
            fornecedoresFiltrados = fornecedores.filter(fornecedor => 
                fornecedor.nome.toLowerCase().startsWith(termo) ||
                fornecedor.cpf_cnpj.startsWith(termo) ||
                fornecedor.telefone.startsWith(termo) ||
                (fornecedor.email && fornecedor.email.toLowerCase().startsWith(termo))
            );
            
            exibirFornecedores(fornecedoresFiltrados);
        }

        // Função para aplicar filtros
        function aplicarFiltro(filtro) {
            switch(filtro) {
                case 'todos':
                    fornecedoresFiltrados = [...fornecedores];
                    break;
                case 'ativos':
                    fornecedoresFiltrados = fornecedores.filter(f => f.status === 'ativo');
                    break;
                case 'com-doacoes':
                    fornecedoresFiltrados = fornecedores.filter(f => f.total_doacoes > 0);
                    break;
                case 'top-fornecedores':
                    fornecedoresFiltrados = fornecedores
                        .filter(f => f.total_doacoes > 0)
                        .sort((a, b) => b.total_doacoes - a.total_doacoes)
                        .slice(0, 5);
                    break;
            }
            
            exibirFornecedores(fornecedoresFiltrados);
        }

        // Função para editar fornecedor
        function editarFornecedor(id) {
            const fornecedor = fornecedores.find(f => f.id === id);
            if (fornecedor) {
                // Preencher modal com dados do fornecedor
                document.getElementById('modalTitle').textContent = 'Editar Fornecedor';
                document.getElementById('supplierName').value = fornecedor.nome;
                document.getElementById('supplierDocument').value = fornecedor.cpf_cnpj;
                document.getElementById('supplierPhone').value = fornecedor.telefone;
                
                // Mostrar modal
                document.getElementById('supplierModal').style.display = 'block';
                
                // Armazenar ID para edição
                document.getElementById('supplierForm').setAttribute('data-edit-id', id);
            }
        }

        // Função para salvar fornecedor (criar ou editar)
        async function salvarFornecedor(dados) {
            const editId = document.getElementById('supplierForm').getAttribute('data-edit-id');
            const isEdit = editId !== null;
            
            try {
                const url = isEdit ? '../PHP/editarFornecedor.php' : '../PHP/cadastrarFornecedor.php';
                const method = 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(dados)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Fornecedor salvo com sucesso!', 'success');
                    fecharModal();
                    carregarFornecedores(); // Recarregar dados
                } else {
                    showNotification('Erro: ' + result.error, 'error');
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                showNotification('Erro ao conectar com o servidor', 'error');
            }
        }

        // Função para excluir fornecedor
        function excluirFornecedor(id) {
            const fornecedor = fornecedores.find(f => f.id === id);
            if (fornecedor) {
                showDeleteConfirmation(
                    'Confirmar Exclusão de Fornecedor',
                    `Tem certeza que deseja excluir o fornecedor "${fornecedor.nome}"? Esta ação não pode ser desfeita.`,
                    async function() {
                        try {
                            const response = await fetch('../PHP/excluirFornecedor.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ id: id })
                            });
                            
                            const result = await response.json();
                            
                            if (result.success) {
                                showNotification(result.message, 'success');
                                carregarFornecedores(); // Recarregar dados
                            } else {
                                showNotification('Erro: ' + result.error, 'error');
                            }
                        } catch (error) {
                            console.error('Erro na requisição:', error);
                            showNotification('Erro ao conectar com o servidor', 'error');
                        }
                    }
                );
            }
        }

        // Função para fechar modal
        function fecharModal() {
            document.getElementById('supplierModal').style.display = 'none';
            document.getElementById('supplierForm').reset();
            document.getElementById('supplierForm').removeAttribute('data-edit-id');
            document.getElementById('modalTitle').textContent = 'Adicionar Fornecedor';
        }

        // Função para gerar relatório de fornecedores
        function gerarRelatorioFornecedores() {
            window.location.href = '../PHP/relatorioFornecedores.php';
        }

        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            const supplierModal = document.getElementById('supplierModal');
            
            if (event.target === supplierModal) {
                fecharModal();
            }
        }
    </script>
    <script src="../JS/modais.js"></script>
</body>
</html> 