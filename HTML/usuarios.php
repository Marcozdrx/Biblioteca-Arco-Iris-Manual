<?php
session_start();
require_once '../PHP/conexao.php';

$usuarios = [];
$sql = "SELECT * FROM usuarios ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql_stats = "SELECT 
    COUNT(*) as total_usuarios,
    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as usuarios_ativos,
    SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as usuarios_bloqueados,
    SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) as usuarios_admin
    FROM usuarios";

    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch();
    
    // Buscar empréstimos ativos por usuário
    $sql_emprestimos = "SELECT 
        u.id,
        COUNT(e.id) as emprestimos_ativos
        FROM usuarios u
        LEFT JOIN emprestimos e ON u.id = e.usuario_id 
        AND e.data_devolucao_real IS NULL
        GROUP BY u.id";
    
    $stmt_emprestimos = $pdo->prepare($sql_emprestimos);    
    $stmt_emprestimos->execute();
    $emprestimos_por_usuario = $stmt_emprestimos->fetchAll(PDO::FETCH_KEY_PAIR);

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/usuarios.css">
</head>
<body>
    <div>
        <a class="voltar" href="inicio-admin.php">Voltar</a>
    </div>
    <header class="header">
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Usuários</span>
        </div>
    </header>
    <div class="container">
        <div class="page-header">
            <h1>Gestão de Usuários</h1>
            <button class="add-user-btn">
                <a href="inserir_usuario.php">Adicionar Usuario</a>
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
                        <?php foreach($usuarios as $usuario): ?>
                            <?php if(!empty($usuario['id'])): ?>
                                <tr>
                                    <td><?=htmlspecialchars($usuario['id'])?></td>
                                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                                    <td>
                                        <span class="status-badge <?= $usuario['ativo']?> 'active' : 'inactive'}">
                                            <?= htmlspecialchars($usuario['ativo']) ?>
                                        </span>
                                        <?php if($usuario['is_admin'] == 1): ?>
                                            <span class="admin-badge">👑 Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($emprestimos_por_usuario[$usuario['id']]) ?></td>
                                    <td>
                                        <button class="action-btn edit-btn" onclick="editarUsuario(<?=$usuario['id']?>)" title="Editar">
                                            ✏️
                                        </button>
                                        <?php if($usuario['ativo'] == 0): ?>
                                            <button class="action-btn unblock-btn" 
                                                onclick="desbloquearUsuario(<?=$usuario['ativo']?>)" 
                                                title="'Desbloquear'">
                                                ✅
                                            </button>
                                        <?php else: ?>
                                            <button class="action-btn  block-btn" 
                                                onclick="bloquearUsuario(<?=$usuario['ativo']?>)" 
                                                title="Bloquea'">
                                                🚫
                                            </button>
                                        <?php endif; ?>
                                        <button class="action-btn delete-btn" onclick="excluirUsuario(<?=$usuario['id']?>)" title="Excluir">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhum usuário encontrado</td></tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar usuário -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Adicionar Usuário</h2>
            <form id="userForm">
                <div class="form-group">
                    <label for="userName">Nome Completo:</label>
                    <input type="text" id="userName" required>
                </div>
                <div class="form-group" id="passwordGroup">
                    <label for="userPassword">Senha:</label>
                    <input type="password" id="userPassword" placeholder="Digite a senha" required>
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

    <script>
        // Variáveis globais
        let usuarios = [];
        let usuariosFiltrados = [];
        let acaoConfirmada = null;

        // Carregar usuários ao inicializar a página
        document.addEventListener('DOMContentLoaded', function() {
            configurarEventos();
        });

        // Função para carregar usuários do banco
        async function carregarUsuarios() {
            try {
                const response = await fetch('../PHP/buscarUsuarios.php');
                const data = await response.json();
                
                if (data.success) {
                    usuarios = data.usuarios;
                    usuariosFiltrados = [...usuarios];
                    
                    // Atualizar estatísticas
                    document.getElementById('totalUsuarios').textContent = data.stats.total_usuarios || 0;
                    document.getElementById('usuariosAtivos').textContent = data.stats.usuarios_ativos || 0;
                    document.getElementById('usuariosBloqueados').textContent = data.stats.usuarios_bloqueados || 0;
                    document.getElementById('mediaEmprestimos').textContent = data.stats.media_emprestimos || 0;
                    
                    // Exibir usuários na tabela
                    exibirUsuarios(usuariosFiltrados);
                } else {
                    console.error('Erro ao carregar usuários:', data.error);
                    alert('Erro ao carregar usuários: ' + data.error);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
                alert('Erro ao conectar com o servidor');
            }
        }

        // Função para configurar eventos
        function configurarEventos() {
            // Busca
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.querySelector('.search-btn');
            
            searchInput.addEventListener('input', filtrarUsuarios);
            searchBtn.addEventListener('click', filtrarUsuarios);
            
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
        }

        // Função para filtrar usuários
        function filtrarUsuarios() {
            const termo = document.getElementById('searchInput').value.toLowerCase();
            
            usuariosFiltrados = usuarios.filter(usuario => 
                usuario.nome.toLowerCase().includes(termo) ||
                usuario.cpf.includes(termo) ||
                usuario.email.toLowerCase().includes(termo) ||
                usuario.telefone.includes(termo)
            );
            
            exibirUsuarios(usuariosFiltrados);
        }

        // Função para aplicar filtros
        function aplicarFiltro(filtro) {
            switch(filtro) {
                case 'todos':
                    usuariosFiltrados = [...usuarios];
                    break;
                case 'ativos':
                    usuariosFiltrados = usuarios.filter(u => u.ativo == 1);
                    break;
                case 'bloqueados':
                    usuariosFiltrados = usuarios.filter(u => u.ativo == 0);
                    break;
                case 'com-emprestimos':
                    usuariosFiltrados = usuarios.filter(u => u.emprestimos_ativos > 0);
                    break;
            }
            
            exibirUsuarios(usuariosFiltrados);
        }

        // Função para editar usuário
        
        // Função para fechar modal
        function fecharModal() {
            document.getElementById('userModal').style.display = 'none';
            document.getElementById('userForm').reset();
            document.getElementById('userForm').removeAttribute('data-edit-id');
            document.getElementById('modalTitle').textContent = 'Adicionar Usuário';
        }


        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            const userModal = document.getElementById('userModal');
            const confirmModal = document.getElementById('confirmModal');
            const multaModal = document.getElementById('multaModal');
            
            if (event.target === userModal) {
                fecharModal();
            }
            if (event.target === confirmModal) {
                fecharConfirmModal();
            }
            if (event.target === multaModal) {
                fecharModalMulta();
            }
        }

        // Função para fechar modal de multa
        function fecharModalMulta() {
            document.getElementById('multaModal').style.display = 'none';
        }
    </script>
</body>
</html> 