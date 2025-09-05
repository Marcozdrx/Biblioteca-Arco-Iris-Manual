<?php
require_once '../PHP/conexao.php';

// Esta é uma versão pública para testes - não requer autenticação
$categorias = [];
$sqlBuscaCategoria = "SELECT nome, id FROM categorias ORDER BY nome ASC";
$stmt = $pdo->prepare($sqlBuscaCategoria);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$autores = [];
$sqlBuscaAutor = "SELECT nome, id FROM autores ORDER BY nome ASC";
$stmt = $pdo->prepare($sqlBuscaAutor);
$stmt->execute();
$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$livros = [];
$sqlApresentaLivros = "SELECT l.*, COALESCE(a.nome, 'Autor não informado') as nome_autor FROM livros l LEFT JOIN autores a ON l.autor_id = a.id WHERE l.ativo = TRUE";
$stmt = $pdo->prepare($sqlApresentaLivros);
$stmt->execute();
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar doações pendentes
$doacoesPendentes = [];
$sqlDoacoes = "SELECT * FROM doacoes WHERE status = 'pendente' ORDER BY data_doacao DESC";
$stmt = $pdo->prepare($sqlDoacoes);
$stmt->execute();
$doacoesPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar devoluções pendentes
$devolucoesPendentes = [];
$sqlDevolucoes = "SELECT e.*, u.nome as nome_usuario, l.titulo as titulo_livro 
                  FROM emprestimos e 
                  JOIN usuarios u ON e.usuario_id = u.id 
                  JOIN livros l ON e.livro_id = l.id 
                  WHERE e.status = 'emprestado' AND e.data_devolucao_prevista < CURDATE()";
$stmt = $pdo->prepare($sqlDevolucoes);
$stmt->execute();
$devolucoesPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Arco-Íris - Administração (Teste)</title>
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
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Painel Administrativo (Teste)</span>
        </div>
        <div class="header-buttons">
            <div class="dropdown-menu">
                <a href="fornecedores.php" class="header-btn dropdown-trigger">Fornecedores ▼</a>
                <div class="dropdown-content">
                    <a href="fornecedores.php" class="dropdown-item">👥 Ver Fornecedores</a>
                    <a href="cadastrar-fornecedor-publico.php" class="dropdown-item">➕ Cadastrar Fornecedor</a></div>
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
                  
                        
                

                    <!-- Seu botão existente -->
                    <button class="dropdown-item-btn" onclick="showDevolucoes()">
                        <span>📚 Devoluções Pendentes</span>
                        <span class="count-badge" id="devolucoesCount">0</span>
                    </button>
                </div>
            </div>
            
            <div class="dropdown-menu">
                <a href="#" class="header-btn dropdown-trigger">Doações ▼</a>
                <div class="dropdown-content">
                    <button class="dropdown-item-btn" onclick="toggleDonationsPanel()">
                        <span>📦 Doações Pendentes</span>
                        <span class="count-badge" id="donationsCount">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="books-section">
            <div class="books-header">
                <h2 class="books-title">Livros Disponíveis</h2>
                <button class="action-btn" onclick="showAddBookModal()">+ Adicionar Novo Livro</button>
            </div>
            <div class="books-grid" id="booksGrid">
                <?php foreach ($livros as $livro): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <?php if ($livro['imagem_capa']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($livro['imagem_capa']) ?>" alt="<?= htmlspecialchars($livro['titulo']) ?>">
                        <?php else: ?>
                            <div class="no-cover">📚</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($livro['titulo']) ?></h3>
                        <p class="book-author"><?= htmlspecialchars($livro['nome_autor']) ?></p>
                        <p class="book-stock">Estoque: <?= $livro['estoque'] ?></p>
                        <div class="book-actions">
                            <button class="edit-btn" onclick="showEditBookModal(this)" 
                                    data-livro-id="<?= $livro['id'] ?>"
                                    data-titulo="<?= htmlspecialchars($livro['titulo']) ?>"
                                    data-estoque="<?= $livro['estoque'] ?>"
                                    data-autor="<?= htmlspecialchars($livro['nome_autor']) ?>"
                                    data-ano="<?= $livro['ano_publicacao'] ?>"
                                    data-paginas="<?= $livro['numero_paginas'] ?>"
                                    data-editora="<?= htmlspecialchars($livro['editora']) ?>"
                                    data-isbn="<?= htmlspecialchars($livro['isbn']) ?>"
                                    data-idioma="<?= htmlspecialchars($livro['idioma']) ?>"
                                    data-categoria="<?= $livro['categoria_id'] ?>"
                                    data-descricao="<?= htmlspecialchars($livro['descricao']) ?>"
                                    data-imagem-capa="<?= $livro['imagem_capa'] ? base64_encode($livro['imagem_capa']) : '' ?>">
                                ✏️ Editar
                            </button>
                            <button class="delete-btn" onclick="deleteBook(<?= $livro['id'] ?>)">🗑️ Excluir</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

 <!-- Modal para adicionar/editar livro -->
    <div id="bookModal" class="modal" tabindex="-1" 
    data-backdrop="static" data-keyboard="false">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBookModal()">&times;</span>
            <h2 id="modalTitle">Adicionar Novo Livro</h2>
            <form id="bookForm" class="modal-form" method="POST" enctype="multipart/form-data" action="../PHP/cadastrarLivroPublico.php">
                <input type="hidden" id="bookId" name="bookId" >
                <input type="File" id="capa" name="capa" accept="image/*"  >
                <input type="text" id="titulo" name="titulo" placeholder="Título do livro"  required>
                <input type="number" id="estoque" name="estoque" placeholder="Quantidade em estoque" min="0"  required>
                <input list="listaAutores" id="autor" name="autor" placeholder="Autor do livro"  required>
                <datalist id="listaAutores">
                    <?php foreach ($autores as $autor):?>
                        <option value="<?=$autor['id']?>"><?= htmlspecialchars($autor['nome']) ?></option>
                    <?php endforeach; ?>
                </datalist>
                <input type="number" id="dataPublicacao" name="dataPublicacao" placeholder="Ano de publicação" min="1000" max="2024" required>
                <input type="number" id="numeroPaginas" name="numeroPaginas" placeholder="Número de páginas" min="1"  required>
                <input type="text" id="editora" name="editora" placeholder="Editora"  required>
                <input type="text" id="isbn" name="isbn" placeholder="International Standard Book Number (ISBN)"  required>
                <input type="text" id="idioma" name="idioma" placeholder="Idioma" required>
                <input list="listaCategorias" id="categoria" name="categoria" placeholder="Pesquise a categoria"  required>
            

            <datalist id="listaCategorias">
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?=$categoria['id']?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                <?php endforeach; ?>
            </datalist>
            
                <textarea id="descricao" name="descricao" placeholder="Sinopse do livro" rows="4" required></textarea>
                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>

    <script type="text/javascript" src="../JS/javaInicioAdmin.js"></script>
</body>
</html>
