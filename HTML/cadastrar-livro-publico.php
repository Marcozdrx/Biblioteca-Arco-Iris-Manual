<?php
require_once '../PHP/conexao.php';

// Esta é uma versão pública para testes - não requer autenticação
try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $titulo = $_POST['titulo'];
        $estoque = $_POST['estoque'];
        $autor_nome = $_POST['autor'];
        $dataPublicacao = $_POST['dataPublicacao'];
        $numeroPaginas = $_POST['numeroPaginas'];
        $editora = $_POST['editora'];
        $isbn = $_POST['isbn'];
        $idioma = $_POST['idioma'];
        $categoria_nome = $_POST['categoria'];
        $descricao = $_POST['descricao'];

        // Criar ou buscar autor
        $sql_autor = "SELECT id FROM autores WHERE nome = :nome";
        $stmt_autor = $pdo->prepare($sql_autor);
        $stmt_autor->bindParam(":nome", $autor_nome);
        $stmt_autor->execute();
        $autor_id = $stmt_autor->fetchColumn();
        
        if (!$autor_id) {
            // Criar novo autor
            $sql_insert_autor = "INSERT INTO autores (nome) VALUES (:nome)";
            $stmt_insert_autor = $pdo->prepare($sql_insert_autor);
            $stmt_insert_autor->bindParam(":nome", $autor_nome);
            $stmt_insert_autor->execute();
            $autor_id = $pdo->lastInsertId();
        }

        // Criar ou buscar categoria
        $sql_categoria = "SELECT id FROM categorias WHERE nome = :nome";
        $stmt_categoria = $pdo->prepare($sql_categoria);
        $stmt_categoria->bindParam(":nome", $categoria_nome);
        $stmt_categoria->execute();
        $categoria_id = $stmt_categoria->fetchColumn();
        
        if (!$categoria_id) {
            // Criar nova categoria
            $sql_insert_categoria = "INSERT INTO categorias (nome) VALUES (:nome)";
            $stmt_insert_categoria = $pdo->prepare($sql_insert_categoria);
            $stmt_insert_categoria->bindParam(":nome", $categoria_nome);
            $stmt_insert_categoria->execute();
            $categoria_id = $pdo->lastInsertId();
        }
        
        // Inserir livro
        $sql = "INSERT INTO livros (titulo, estoque, autor_id, ano_publicacao, numero_paginas, editora, isbn, idioma, categoria_id, descricao) VALUES (:titulo, :estoque, :autor_id, :ano_publicacao, :numero_paginas, :editora, :isbn, :idioma, :categoria_id, :descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":estoque", $estoque);
        $stmt->bindParam(":autor_id", $autor_id);
        $stmt->bindParam(":ano_publicacao", $dataPublicacao);
        $stmt->bindParam(":numero_paginas", $numeroPaginas);
        $stmt->bindParam(":editora", $editora);
        $stmt->bindParam(":isbn", $isbn);
        $stmt->bindParam(":idioma", $idioma);
        $stmt->bindParam(":categoria_id", $categoria_id);
        $stmt->bindParam(":descricao", $descricao);
        
        if($stmt->execute()) {
            // Redireciona para uma página de sucesso
            header('Location: sucesso-livro.php');
            exit();
        } else {
            echo "<script>alert('Erro ao cadastrar livro');</script>";
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Erro ao cadastrar livro: " . $e->getMessage() . "');</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Livro - Teste</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .form-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .input-group label {
            font-weight: bold;
            color: #333;
        }
        
        .input-group input,
        .input-group textarea {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        
        .btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .voltar {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .voltar:hover {
            background: #da190b;
        }
    </style>
</head>
<body style="background-image: url(../IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;">
    
    <div>
        <a class="voltar" href="../index.php">Voltar</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <div class="arco-iris">
                <span>B</span><span>I</span><span>B</span><span>L</span><span>I</span><span>O</span><span>T</span><span>E</span><span>C</span><span>A</span>
                <br>
                <span>A</span><span>R</span><span>C</span><span>O</span><span>-</span><span>Í</span><span>R</span><span>I</span><span>S</span>
            </div>
            
            <h2 style="text-align: center; color: #333; margin-bottom: 30px;">Cadastro de Livro - Teste</h2>
            
            <form id="bookForm" class="form-box" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="capa">Capa do Livro (opcional):</label>
                    <input type="file" id="capa" name="capa" accept="image/*">
                </div>
                
                <div class="input-group">
                    <label for="titulo">Título do Livro:</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Digite o título do livro" required>
                </div>
                
                <div class="input-group">
                    <label for="estoque">Quantidade em Estoque:</label>
                    <input type="number" id="estoque" name="estoque" placeholder="Quantidade em estoque" min="0" required>
                </div>
                
                <div class="input-group">
                    <label for="autor">Autor:</label>
                    <input type="text" id="autor" name="autor" placeholder="Nome do autor" required>
                </div>
                
                <div class="input-group">
                    <label for="dataPublicacao">Ano de Publicação:</label>
                    <input type="number" id="dataPublicacao" name="dataPublicacao" placeholder="Ano de publicação" min="1000" max="2024" required>
                </div>
                
                <div class="input-group">
                    <label for="numeroPaginas">Número de Páginas:</label>
                    <input type="number" id="numeroPaginas" name="numeroPaginas" placeholder="Número de páginas" min="1" required>
                </div>
                
                <div class="input-group">
                    <label for="editora">Editora:</label>
                    <input type="text" id="editora" name="editora" placeholder="Nome da editora" required>
                </div>
                
                <div class="input-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" placeholder="International Standard Book Number" required>
                </div>
                
                <div class="input-group">
                    <label for="idioma">Idioma:</label>
                    <input type="text" id="idioma" name="idioma" placeholder="Idioma do livro" required>
                </div>
                
                <div class="input-group">
                    <label for="categoria">Categoria:</label>
                    <input type="text" id="categoria" name="categoria" placeholder="Categoria do livro" required>
                </div>
                
                <div class="input-group">
                    <label for="descricao">Sinopse:</label>
                    <textarea id="descricao" name="descricao" placeholder="Sinopse do livro" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn">Salvar Livro</button>
            </form>
        </div>
    </div>
</body>
</html>
