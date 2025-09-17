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
    SUM(CASE WHEN cargo = 1 THEN 1 ELSE 0 END) as usuarios_admin
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
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 2) {
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
        <a class="voltar" href="inicio-secretaria.php">Voltar</a>
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
           
        </div>

        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3 id="totalUsuarios">23</h3>
                    <p>Total de Usuários</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3 id="usuariosAtivos">22</h3>
                    <p>Usuários Ativos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3 id="usuariosBloqueados">1</h3>
                    <p>Usuários Bloqueados</p>
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
                    
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        
                        <?php foreach($usuarios as $usuario): ?>
                            <?php if(!empty($usuario['id'])): ?>
                                
                                <tr>
                                    <td><?=htmlspecialchars($usuario['id'])?></td>
                                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                                    <td>
                                        <span class="status-badge <?= $usuario['ativo'] == 1 ? 'active' : 'inactive' ?>">
                                            <?= $usuario['ativo'] == 1 ? 'Ativo' : 'Bloqueado' ?>
                                        </span>
                                        <?php if($usuario['cargo'] == 1): ?>
                                            <span class="admin-badge">👑 Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($emprestimos_por_usuario[$usuario['id']]) ?></td>
                                    <td style="display: none;">
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



    

    <script>
// Variáveis globais
let todosUsuarios = [];
let filtroAtual = 'todos';
let termoPesquisa = '';

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Coletar todos os usuários da tabela
    coletarUsuarios();
    
    // Configurar eventos dos filtros
    configurarFiltros();
    
    // Configurar pesquisa
    configurarPesquisa();
    
    // Atualizar estatísticas
    atualizarEstatisticas();
});

function coletarUsuarios() {
    const tbody = document.getElementById('usersTableBody');
    const linhas = tbody.querySelectorAll('tr');
    
    todosUsuarios = [];
    linhas.forEach(linha => {
        const celulas = linha.querySelectorAll('td');
        if (celulas.length >= 5) {
            const usuario = {
                id: celulas[0].textContent.trim(),
                nome: celulas[1].textContent.trim(),
                status: celulas[2].textContent.trim(),
                emprestimos: celulas[3].textContent.trim(),
                linha: linha
            };
            todosUsuarios.push(usuario);
        }
    });
}

function configurarFiltros() {
    const botoesFiltro = document.querySelectorAll('.filter-btn');
    
    botoesFiltro.forEach(botao => {
        botao.addEventListener('click', function() {
            // Remover classe active de todos os botões
            botoesFiltro.forEach(btn => btn.classList.remove('active'));
            
            // Adicionar classe active ao botão clicado
            this.classList.add('active');
            
            // Aplicar filtro
            filtroAtual = this.getAttribute('data-filter');
            aplicarFiltros();
        });
    });
}

function configurarPesquisa() {
    const campoPesquisa = document.getElementById('searchInput');
    
    campoPesquisa.addEventListener('input', function() {
        termoPesquisa = this.value.toLowerCase();
        aplicarFiltros();
    });
}

function aplicarFiltros() {
    const tbody = document.getElementById('usersTableBody');
    
    // Limpar tabela
    tbody.innerHTML = '';
    
    // Filtrar usuários
    let usuariosFiltrados = todosUsuarios.filter(usuario => {
        // Filtro por pesquisa
        if (termoPesquisa && !usuario.nome.toLowerCase().includes(termoPesquisa)) {
            return false;
        }
        
        // Filtro por status
        switch(filtroAtual) {
            case 'ativos':
                return usuario.status.includes('1') || usuario.status.includes('Ativo');
            case 'bloqueados':
                return usuario.status.includes('0') || usuario.status.includes('Bloqueado');
            case 'com-emprestimos':
                return parseInt(usuario.emprestimos) > 0;
            case 'todos':
            default:
                return true;
        }
    });
    
    // Adicionar usuários filtrados à tabela
    if (usuariosFiltrados.length === 0) {
        const linhaVazia = document.createElement('tr');
        linhaVazia.innerHTML = '<td colspan="5" style="text-align: center; padding: 20px; color: #666;">Nenhum usuário encontrado</td>';
        tbody.appendChild(linhaVazia);
    } else {
        usuariosFiltrados.forEach(usuario => {
            tbody.appendChild(usuario.linha.cloneNode(true));
        });
    }
    
    // Atualizar estatísticas
    atualizarEstatisticas();
}

function atualizarEstatisticas() {
    const totalUsuarios = todosUsuarios.length;
    const usuariosAtivos = todosUsuarios.filter(u => u.status.includes('1') || u.status.includes('Ativo')).length;
    const usuariosBloqueados = todosUsuarios.filter(u => u.status.includes('0') || u.status.includes('Bloqueado')).length;
    
    // Atualizar elementos (se existirem)
    const totalElement = document.getElementById('totalUsuarios');
    const ativosElement = document.getElementById('usuariosAtivos');
    const bloqueadosElement = document.getElementById('usuariosBloqueados');
    
    if (totalElement) totalElement.textContent = totalUsuarios;
    if (ativosElement) ativosElement.textContent = usuariosAtivos;
    if (bloqueadosElement) bloqueadosElement.textContent = usuariosBloqueados;
}







    </script>
</body>
</html> 