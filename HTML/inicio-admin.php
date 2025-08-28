<?php
require_once '../PHP/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Arco-Íris - Administração</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/inicioadmin.css">
</head>
<body>
    <div>
        <a class="voltar" href="index.php">Voltar</a>
      </div>
    <header class="header">
        <div>
            <a class="voltar" href="index.php">Voltar</a>
          </div>
        <div class="header-title">
            <img src="IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Painel Administrativo</span>
        </div>
        <div class="header-buttons">
            <div class="dropdown-menu">
                <a href="fornecedores.php" class="header-btn dropdown-trigger">Fornecedores ▼</a>
                <div class="dropdown-content">
                    <a href="fornecedores.php" class="dropdown-item">👥 Ver Fornecedores</a>
                    <a href="cadastrar-fornecedores.php" class="dropdown-item">➕ Cadastrar Fornecedor</a>
                </div>
            </div>
            
            <a href="graficos.php" style="text-decoration: none;">
                <button class="graficos">
                    <span>Gráficos</span>
                </button>
            </a>
            
            <div class="dropdown-menu">
                <a href="usuarios.php" class="header-btn dropdown-trigger">Usuários ▼</a>
                <div class="dropdown-content">
                    <a href="usuarios.php" class="dropdown-item">👥 Gerenciar Usuários</a>
                    <a href="agendamentos.php" class="dropdown-item">📅 Agendamentos</a>
                    <button class="dropdown-item-btn" onclick="toggleDonationsPanel()">
                        <span>🎁 Doações Pendentes</span>
                        <span class="count-badge" id="donationsCount">0</span>
                    </button>
                    <button class="dropdown-item-btn" onclick="toggleDevolucoesPanel()">
                        <span>📚 Devoluções Pendentes</span>
                        <span class="count-badge" id="devolucoesCount">0</span>
                    </button>
                </div>
            </div>
            <a href="index.php" class="header-btn">Sair</a>
        </div>
    </header>

    <div class="admin-container">
        <div class="books-section">
            <div class="books-header">
                <h2 class="books-title">Livros Disponíveis</h2>
                <button class="action-btn" onclick="showAddBookModal()">+ Adicionar Novo Livro</button>
            </div>
            <div class="books-grid" id="booksGrid">
                <!-- Os livros serão inseridos aqui via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal para adicionar/editar livro -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Adicionar Novo Livro</h2>
            <form id="bookForm" class="modal-form">
                <input type="hidden" id="bookId">
                <input type="text" id="bookTitle" placeholder="Título do livro" required>
                <input type="text" id="bookImage" placeholder="URL da imagem" required>
                <input type="number" id="bookEstoque" placeholder="Quantidade em estoque" min="0" required>
                <input type="text" id="bookAutor" placeholder="Autor do livro" required>
                <input type="number" id="bookAno" placeholder="Ano de publicação" min="1000" max="2024" required>
                <input type="number" id="bookPaginas" placeholder="Número de páginas" min="1" required>
                <input type="text" id="bookCategoria" placeholder="Categoria do livro" required>
                <textarea id="bookDescription" placeholder="Sinopse do livro" rows="4" required></textarea>
                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>

    <div class="donations-panel" id="donationsPanel">
        <div class="donations-header">
            <h2 class="donations-title">Doações Pendentes</h2>
            <button class="close-donations" onclick="toggleDonationsPanel()">&times;</button>
        </div>
        <div id="donationsList">
            <!-- As doações pendentes serão inseridas aqui via JavaScript -->
        </div>
    </div>

    <div class="devolucoes-panel" id="devolucoesPanel">
        <div class="devolucoes-header">
            <h2 class="devolucoes-title">Devoluções Pendentes</h2>
            <button class="close-devolucoes" onclick="toggleDevolucoesPanel()">&times;</button>
        </div>
        <div id="devolucoesList">
            <!-- As devoluções pendentes serão inseridas aqui via JavaScript -->
        </div>
    </div>
    <script>
        // Função para mostrar modal de adicionar livro
        function showAddBookModal() {
            document.getElementById('modalTitle').textContent = 'Adicionar Novo Livro';
            document.getElementById('bookForm').reset();
            document.getElementById('bookId').value = '';
            document.getElementById('bookModal').style.display = 'block';
        }

        // Função para mostrar modal de editar livro
        function showEditBookModal(bookId) {
            // Esta função será chamada quando o PHP gerar os botões de editar
            // O PHP deve preencher os campos do formulário antes de chamar esta função
            document.getElementById('modalTitle').textContent = 'Editar Livro';
            document.getElementById('bookModal').style.display = 'block';
        }

        // Função para fechar modal
        function closeModal() {
            document.getElementById('bookModal').style.display = 'none';
        }

        // Função para alternar painel de doações
        function toggleDonationsPanel() {
            const panel = document.getElementById('donationsPanel');
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
            }
        }

        // Função para alternar painel de devoluções
        function toggleDevolucoesPanel() {
            const panel = document.getElementById('devolucoesPanel');
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
            }
        }

        // Função para deletar livro (será chamada pelo PHP)
        function deleteBook(bookId) {
            if (confirm('Tem certeza que deseja excluir este livro?')) {
                // Redirecionar para uma página PHP que fará a exclusão
                window.location.href = `delete_book.php?id=${bookId}`;
            }
        }

        // Função para aceitar doação (será chamada pelo PHP)
        function acceptDonation(donationId) {
            if (confirm('Tem certeza que deseja aceitar esta doação?')) {
                // Redirecionar para uma página PHP que fará a aceitação
                window.location.href = `accept_donation.php?id=${donationId}`;
            }
        }

        // Função para recusar doação (será chamada pelo PHP)
        function rejectDonation(donationId) {
            if (confirm('Tem certeza que deseja recusar esta doação?')) {
                // Redirecionar para uma página PHP que fará a recusa
                window.location.href = `reject_donation.php?id=${donationId}`;
            }
        }

        // Função para confirmar devolução (será chamada pelo PHP)
        function confirmarDevolucao(devolucaoId) {
            if (confirm('Tem certeza que deseja confirmar esta devolução?')) {
                // Redirecionar para uma página PHP que fará a confirmação
                window.location.href = `confirm_devolucao.php?id=${devolucaoId}`;
            }
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Fechar modal ao pressionar ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html> 