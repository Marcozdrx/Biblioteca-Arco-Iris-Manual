<?php
require_once '../PHP/PHPincioAdmin.php';

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    header("Location: login.php");
    exit();
}
$nome = $_SESSION['nome_usuario'];
$cargo = $_SESSION['cargo'];

// Puxando os empréstimos
$sql = "SELECT e.id as emprestimo_id, l.titulo as nome_livro, l.imagem_capa, 
               u.nome as nome_usuario, u.email as email_usuario, 
               e.data_devolucao_prevista, e.data_emprestimo, e.status
        FROM emprestimos e
        INNER JOIN livros l ON e.livro_id = l.id
        INNER JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.status IN ('aguardando_devolucao')";

$stmt = $pdo->prepare($sql);
$stmt->execute(); // Sem parâmetros
$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC)
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Arco-Íris - Administração</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/inicioadmin.css">
    <link rel="stylesheet" href="../CSS/modais.css">
</head>
<body>
    
    <div>
        <a class="voltar" href="../index.php">Voltar</a>
    </div>
    <header class="header">
        <div>
            <a class="header-btn" href="../index.php">Voltar</a>
          </div>
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">


            <?php if($_SESSION['cargo'] == 1): ?>
            <span>Bem vindo <?= htmlspecialchars($nome)?> - adm</span>
            <?php else: ?>
            <span> Bem-vindo <?= htmlspecialchars($nome) ?> - Secretaria</span>
            <?php endif; ?>
        </div>
        <div class="header-buttons">
            <div class="dropdown-menu">
                <a href="fornecedores.php" class="header-btn dropdown-trigger">Fornecedores ▼</a>
                <div class="dropdown-content">
                    <a href="fornecedores.php" class="dropdown-item">👥 Ver Fornecedores</a>
                    <a href="cadastro-autor.php" class="dropdown-item">👥 Cadastrar Autor</a>

                    <a href="cadastrar-fornecedores.php" class="dropdown-item">➕ Cadastrar Fornecedor</a></div>
            </div>
            
            <a href="graficos.php" style="text-decoration: none;">
                <button class="header-btn">
                    <span>Gráficos</span>
                </button>
            </a>
            
            <div class="dropdown-menu dropdown-usuarios">
                <a href="usuarios.php" class="header-btn dropdown-trigger">Usuários ▼</a>
                <div class="dropdown-content">
                    <a href="usuarios.php" class="dropdown-item">👥 Gerenciar Usuários</a>
                  
                        
                

                    <!-- Seu botão existente -->
                    <button class="dropdown-item-btn" onclick="showDevolucoes()">
                        <span>📚 Devoluções Pendentes</span>
                    </button>

                    <!-- Modal (mesmo de antes, adaptado) -->
                    <div id="modalDevolucoes" class="modal">
                        <div class="modal-conteudo">
                            <span class="fechar" onclick="fecharModal()">&times;</span>
                            <h2>Devoluções Pendentes</h2>
                            <ul id="listaDevolucoes">
                                <?php if(!empty($emprestimos)): ?>
                                    <?php foreach ($emprestimos as $emprestimo): ?>
                                        <li>
                                            <strong><?= htmlspecialchars($emprestimo['nome_livro']) ?></strong><br>
                                            Usuário: <?= htmlspecialchars($emprestimo['nome_usuario']) ?><br>
                                            Data Prevista: <?= date('d/m/Y', strtotime($emprestimo['data_devolucao_prevista'])) ?><br>
                                            E-mail: <?= htmlspecialchars($emprestimo['email_usuario']) ?>
                                            <form method="POST" action="../PHP/aceitarDevolucao.php">
                                                <input type="hidden" name="IdEmprestimo" value="<?=htmlspecialchars($emprestimo['emprestimo_id'])?>" >
                                            <button name="aceitarDevo" class="btn-accept-devolucao">Aceitar devolução</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>Nenhuma devolução pendente</li>
                                <?php endif; ?>
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <a href="logout.php" class="header-btn">Sair</a>
        </div>
    </header>

    <div class="admin-container">
        <div class="books-section">
            <div class="books-header">
                <h2 class="books-title">Livros Disponíveis</h2>
                <button class="action-btn" onclick="showAddBookModal()">+ Adicionar Novo Livro</button>
            </div>
            <div class="books-grid" id="booksGrid">
                <?php foreach ($livros as $livro): ?>
                    <div class="book-card">
                        <?php if(!empty($livro['imagem_capa'])): ?>
                            <?php
                                $imagemData = $livro['imagem_capa'];
                                // Verificar se é WebP 
                                if (substr($imagemData, 0, 4) === 'RIFF') {
                                    $mimeType = 'image/webp';
                                } else {
                                    // Usar finfo para outros formatos
                                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                    $mimeType = finfo_buffer($finfo, $imagemData);
                                    finfo_close($finfo);
                                }
                                
                                // Verificar se o MIME foi detectado corretamente
                                if (!$mimeType || $mimeType === 'application/octet-stream') {
                                    $mimeType = 'image/webp'; // Fallback para WebP
                                }
                            ?>
                        <img src="data:<?= $mimeType ?>;base64,<?= base64_encode($imagemData) ?>" alt="Capa do livro">
                        <?php else: ?>
                            <img src="../IMG/default-avatar.svg" alt="capa do livro">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($livro['titulo']) ?></h3>
                        <p>Autor: <?= htmlspecialchars($livro['nome_autor']) ?></p>
                        <p>Estoque: <?= htmlspecialchars($livro['estoque']) ?></p>
                        <form method="POST" class="book-actions" action="../PHP/DeletarLivros.php">
                            <input type="hidden" name="id" value="<?= $livro['id'] ?>">
                            <button type="button" class="book-btn btn-edit" 
                                data-livro-id="<?= $livro['id'] ?>"
                                data-titulo="<?= htmlspecialchars($livro['titulo']) ?>"
                                data-estoque="<?= $livro['estoque'] ?>"
                                data-autor="<?= $livro['autor_id'] ?>"
                                data-ano="<?= $livro['ano_publicacao'] ?>"
                                data-paginas="<?= $livro['numero_paginas'] ?>"
                                data-editora="<?= htmlspecialchars($livro['editora']) ?>"
                                data-isbn="<?= htmlspecialchars($livro['isbn']) ?>"
                                data-idioma="<?= htmlspecialchars($livro['idioma']) ?>"
                                data-categoria="<?= $livro['categoria_id'] ?>"
                                data-descricao="<?= htmlspecialchars($livro['descricao']) ?>"
                                onclick="showEditBookModal(this)">
                                ✏️ Editar
                            </button>
                            <button type="button" class="book-btn btn-delete" onclick="confirmarExclusaoLivroAdmin(<?= $livro['id'] ?>, '<?= htmlspecialchars($livro['titulo']) ?>')">🗑️ Excluir</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
 <!-- Modal para adicionar livro -->
    <div id="bookModal" class="modal" tabindex="-1" 
    data-backdrop="static" data-keyboard="false">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBookModal()">&times;</span>
            <h2 id="modalTitle">Adicionar Novo Livro</h2>
            <form id="bookForm" class="modal-form" onsubmit="confirmarAdicaoLivro(event)">
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
                <input type="text" id="isbn" name="isbn" placeholder="International Standard Book Number (ISBN)" maxlength="13"  required>
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
    <!-- Modal para editar livro -->
    <div id="editModal" class="modal" tabindex="-1" 
    data-backdrop="static" data-keyboard="false">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
            <h2 id="modalTitle">Editar Livro</h2>
            <form id="editForm" class="modal-form" onsubmit="confirmarEdicaoLivro(event)">
                <input type="hidden" id="idLivroEdit" name="idLivroEdit">
                <input type="hidden" id="imagemAtual" name="imagemAtual" value="">
                <input type="File" id="capaEdit" name="capaEdit" accept="image/*">
                <small>Deixe em branco para manter a imagem atual</small>
                <input type="text" id="tituloEdit" name="tituloEdit" placeholder="Título do livro" value="<?= htmlspecialchars($livro['titulo']) ?>" required>
                <input type="number" id="estoqueEdit" name="estoqueEdit" placeholder="Quantidade em estoque" min="0" value="<?= htmlspecialchars($livro['estoque']) ?>" required>
                <input list="listaAutoresEdit" id="autorEdit" name="autorEdit" placeholder="Autor do livro" value="<?= htmlspecialchars($livro['autor_id']) ?>" required>
                <datalist id="listaAutoresEdit">
                    <?php foreach ($autores as $autor):?>
                        <option value="<?=$autor['id']?>"><?= htmlspecialchars($autor['nome']) ?></option>
                    <?php endforeach; ?>
                </datalist>
                <input type="number" id="dataPublicacaoEdit" name="dataPublicacaoEdit" placeholder="Ano de publicação" min="1000" max="2024" value="<?= htmlspecialchars($livro['ano_publicacao']) ?>" required>
                <input type="number" id="numeroPaginasEdit" name="numeroPaginasEdit" placeholder="Número de páginas" min="1" value="<?= htmlspecialchars($livro['numero_paginas']) ?>" required>
                <input type="text" id="editoraEdit" name="editoraEdit" placeholder="Editora" value="<?= htmlspecialchars($livro['editora']) ?>" required>
                <input type="text" id="isbnEdit" name="isbnEdit" placeholder="International Standard Book Number (ISBN)" value="<?= htmlspecialchars($livro['isbn']) ?>" required>
                <input type="text" id="idiomaEdit" name="idiomaEdit" placeholder="Idioma" value="<?= htmlspecialchars($livro['idioma']) ?>" required>
                
                <input list="listaCategoriasEdit" id="categoriaEdit" name="categoriaEdit" placeholder="Pesquise a categoria" value="<?= htmlspecialchars($livro['categoria_id']) ?>" required>
            
                
                <datalist id="listaCategoriasEdit">
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?=$categoria['id']?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                    <?php endforeach; ?>
                </datalist>
            
                <textarea id="descricaoEdit" name="descricaoEdit" placeholder="Sinopse do livro" rows="1" required><?= htmlspecialchars($livro['descricao']) ?></textarea>
                <button type="submit">Salvar</button>
            </form>
        </div>
    </div>

    <!-- Modal de confirmação para exclusão -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirmar Exclusão</h2>
            <p>Tem certeza que deseja excluir este livro? Esta ação não pode ser desfeita.</p>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="confirmDelete()">Sim, Excluir</button>
                <button class="btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
            </div>
        </div>
    </div>



    <script src="../JS/javaInicioAdmin.js"></script>
    <script src="../JS/modais.js"></script>
    <script>
        // Função para confirmar exclusão de livro na página inicio-admin
        function confirmarExclusaoLivroAdmin(livroId, tituloLivro) {
            showDeleteConfirmation(
                'Confirmar Exclusão de Livro',
                `Tem certeza que deseja excluir o livro "${tituloLivro}"? Esta ação não pode ser desfeita.`,
                function() {
                    const formData = new FormData();
                    formData.append('id', livroId);
                    
                    fetch('../PHP/DeletarLivros.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            location.reload();
                        } else {
                            showNotification('Erro: ' + data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao excluir livro:', error);
                        showNotification('Erro ao excluir livro', 'error');
                    });
                }
            );
        }
        
        // Função para confirmar edição de livro
        function confirmarEdicaoLivro(event) {
            event.preventDefault();
            
            const tituloLivro = document.getElementById('tituloEdit').value;
            
            showEditConfirmation(
                'Confirmar Edição de Livro',
                `Tem certeza que deseja salvar as alterações do livro "${tituloLivro}"?`,
                function() {
                    // Criar formulário para enviar os dados
                    const formData = new FormData();
                    const form = document.getElementById('editForm');
                    
                    // Adicionar todos os campos do formulário
                    formData.append('idLivroEdit', document.getElementById('idLivroEdit').value);
                    formData.append('tituloEdit', document.getElementById('tituloEdit').value);
                    formData.append('estoqueEdit', document.getElementById('estoqueEdit').value);
                    formData.append('autorEdit', document.getElementById('autorEdit').value);
                    formData.append('dataPublicacaoEdit', document.getElementById('dataPublicacaoEdit').value);
                    formData.append('numeroPaginasEdit', document.getElementById('numeroPaginasEdit').value);
                    formData.append('editoraEdit', document.getElementById('editoraEdit').value);
                    formData.append('isbnEdit', document.getElementById('isbnEdit').value);
                    formData.append('idiomaEdit', document.getElementById('idiomaEdit').value);
                    formData.append('categoriaEdit', document.getElementById('categoriaEdit').value);
                    formData.append('descricaoEdit', document.getElementById('descricaoEdit').value);
                    
                    // Adicionar arquivo se houver
                    const capaFile = document.getElementById('capaEdit').files[0];
                    if (capaFile) {
                        formData.append('capaEdit', capaFile);
                    }
                    
                    fetch('../PHP/editarLivros.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            closeEditModal();
                            // Recarregar a página após um pequeno delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Erro: ' + data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao editar livro:', error);
                        showNotification('Erro ao editar livro', 'error');
                    });
                }
            );
        }
            
        // Função para confirmar adição de livro
        function confirmarAdicaoLivro(event) {
            event.preventDefault();
                
            const tituloLivro = document.getElementById('titulo').value;
                
            showEditConfirmation(
                'Confirmar Adição de Livro',
                `Tem certeza que deseja adicionar o livro "${tituloLivro}" ao acervo?`,
                function() {
                    // Criar formulário para enviar os dados
                    const formData = new FormData();
                        
                    // Adicionar todos os campos do formulário
                    formData.append('capa', document.getElementById('capa').files[0]);
                    formData.append('titulo', document.getElementById('titulo').value);
                    formData.append('estoque', document.getElementById('estoque').value);
                    formData.append('autor', document.getElementById('autor').value);
                    formData.append('dataPublicacao', document.getElementById('dataPublicacao').value);
                    formData.append('numeroPaginas', document.getElementById('numeroPaginas').value);
                    formData.append('editora', document.getElementById('editora').value);
                    formData.append('isbn', document.getElementById('isbn').value);
                    formData.append('idioma', document.getElementById('idioma').value);
                    formData.append('categoria', document.getElementById('categoria').value);
                    formData.append('descricao', document.getElementById('descricao').value);
                        
                    fetch('../PHP/PHPincioAdmin.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            closeBookModal();
                            // Recarregar a página após um pequeno delay
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification('Erro: ' + data.error, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao adicionar livro:', error);
                        showNotification('Erro ao adicionar livro', 'error');
                    });
                }
            );
        }
    </script>
</body>
</html> 