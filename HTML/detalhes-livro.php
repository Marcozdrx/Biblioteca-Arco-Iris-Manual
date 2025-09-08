<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id']) || $_SESSION['is_admin'] != 0) {
    header("Location: login.php");
    exit();
}

// Verificar se o ID do livro foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: usuario.php");
    exit();
}

$livro_id = (int)$_GET['id'];

// Buscar dados do livro
$sql = "SELECT l.*, a.nome as nome_autor, c.nome as nome_categoria
        FROM livros l
        LEFT JOIN autores a ON l.autor_id = a.id
        LEFT JOIN categorias c ON l.categoria_id = c.id
        WHERE l.id = ? AND l.ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute([$livro_id]);
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o livro não foi encontrado, redirecionar
if (!$livro) {
    header("Location: usuario.php");
    exit();
}

// Verificar se o usuário já tem este livro emprestado
$sql = "SELECT COUNT(*) as total FROM emprestimos 
        WHERE usuario_id = ? AND livro_id = ? AND status = 'emprestado'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['id'], $livro_id]);
$ja_emprestado = $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($livro['titulo']) ?> - Biblioteca Arco-Íris</title>
    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="../CSS/detalhes-livro.css">
</head>
<body>
    <div>
        <a class="voltar" href="usuario.php">Voltar</a>
    </div>
    
    <header class="header">
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris</span>
        </div>
        <div class="header-buttons">
            <a href="emprestimos.php" class="header-btn">Meus Empréstimos</a>
            <a href="perfil.php" class="header-btn">Perfil</a>
            <a href="logout.php" class="header-btn">Sair</a>
        </div>
    </header>

    <div class="container">
        <div class="book-details-container">
            <div class="book-cover">
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
                    <img src="../IMG/default-avatar.svg" alt="Capa do livro">
                <?php endif; ?>
            </div>
            
            <div class="book-info">
                <h1 class="book-title"><?= htmlspecialchars($livro['titulo']) ?></h1>
                
                <div class="book-meta">
                    <div class="meta-item">
                        <span class="meta-label">Autor:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['nome_autor'] ?? 'Autor não informado') ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Editora:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['editora']) ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Ano de Publicação:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['ano_publicacao']) ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Páginas:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['numero_paginas']) ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Idioma:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['idioma']) ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Categoria:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['nome_categoria'] ?? 'Categoria não informada') ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">ISBN:</span>
                        <span class="meta-value"><?= htmlspecialchars($livro['isbn']) ?></span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Estoque:</span>
                        <span class="meta-value <?= $livro['estoque'] > 0 ? 'available' : 'unavailable' ?>">
                            <?= $livro['estoque'] > 0 ? $livro['estoque'] . ' disponível(is)' : 'Indisponível' ?>
                        </span>
                    </div>
                </div>
                
                <?php if(!empty($livro['descricao'])): ?>
                    <div class="book-description">
                        <h3>Sinopse</h3>
                        <p><?= nl2br(htmlspecialchars($livro['descricao'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="book-actions">
                    <?php if ($ja_emprestado): ?>
                        <div class="already-borrowed">
                            <p>Você já tem este livro emprestado!</p>
                            <a href="emprestimos.php" class="btn-secondary">Ver Meus Empréstimos</a>
                        </div>
                    <?php elseif ($livro['estoque'] > 0): ?>
                        <a href="../PHP/registrarEmprestimo.php?livro_id=<?= $livro['id'] ?>" class="btn-emprestar">
                            📚 Emprestar Livro
                        </a>
                    <?php else: ?>
                        <div class="unavailable">
                            <p>Este livro não está disponível no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
