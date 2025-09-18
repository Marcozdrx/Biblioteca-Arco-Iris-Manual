<?php
    session_start();
    require_once 'conexao.php';

// Verificar se o usuário está logado e é admin
    if (!isset($_SESSION['id']) || $_SESSION['cargo'] == 0) {
        header("Location: ../HTML/login.php");
        exit();
    }

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
    $sqlDoacoes = "SELECT d.*, u.nome as nome_usuario, u.email as email_usuario 
    FROM doacoes d 
    INNER JOIN usuarios u ON d.usuario_id = u.id 
    WHERE d.status = 'pendente' 
    ORDER BY d.data_doacao DESC";
    $stmt = $pdo->prepare($sqlDoacoes);
    $stmt->execute();
    $doacoesPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar devoluções pendentes
    $devolucoesPendentes = [];
    $sqlDevolucoes = "SELECT e.*, l.titulo as titulo_livro, u.nome as nome_usuario, u.email as email_usuario 
    FROM emprestimos e 
    INNER JOIN livros l ON e.livro_id = l.id 
    INNER JOIN usuarios u ON e.usuario_id = u.id 
    WHERE e.status = 'emprestado' AND e.data_devolucao_prevista < CURDATE() 
    ORDER BY e.data_devolucao_prevista ASC";
    $stmt = $pdo->prepare($sqlDevolucoes);
    $stmt->execute();
    $devolucoesPendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estatísticas para gráficos
    $stats = [];

    // Total de livros
    $sql = "SELECT COUNT(*) as total FROM livros WHERE ativo = TRUE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['totalLivros'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Livros emprestados
    $sql = "SELECT COUNT(*) as total FROM emprestimos WHERE status = 'emprestado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['livrosEmprestados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de usuários ativos
    $sql = "SELECT COUNT(*) as total FROM usuarios WHERE ativo = TRUE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats['totalUsuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Taxa de empréstimo
    $stats['taxaEmprestimo'] = $stats['totalLivros'] > 0 ? round(($stats['livrosEmprestados'] / $stats['totalLivros']) * 100, 1) : 0;

    // Top 10 livros mais emprestados
    $sql = "SELECT l.titulo, a.nome as autor, COUNT(e.id) as total_emprestimos, l.estoque
    FROM livros l 
    LEFT JOIN autores a ON l.autor_id = a.id
    LEFT JOIN emprestimos e ON l.id = e.livro_id
    WHERE l.ativo = TRUE
    GROUP BY l.id, l.titulo, a.nome, l.estoque
    ORDER BY total_emprestimos DESC
    LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $topLivros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Empréstimos por mês (últimos 6 meses)
    $sql = "SELECT DATE_FORMAT(data_emprestimo, '%Y-%m') as mes, COUNT(*) as total
    FROM emprestimos 
    WHERE data_emprestimo >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(data_emprestimo, '%Y-%m')
    ORDER BY mes DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $emprestimosMensais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Últimos empréstimos
    $sql = "SELECT e.data_emprestimo, l.titulo, u.nome as usuario, e.status
    FROM emprestimos e
    INNER JOIN livros l ON e.livro_id = l.id
    INNER JOIN usuarios u ON e.usuario_id = u.id
    ORDER BY e.data_emprestimo DESC
    LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ultimosEmprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($_SESSION['cargo'] == 0){
        echo "Acesso negado, apenas usuarios com permissão podem acessar essa pagina";
    }else{

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            // Adicionar novo livro
            $titulo = $_POST['titulo'];
            $capa = file_get_contents($_FILES['capa']['tmp_name']);
            $nomeCapa = $_FILES['capa']['name'];
            
            // DEBUG: Verificar tamanho do arquivo
            echo "<script>console.log('Tamanho do arquivo: " . strlen($capa) . " bytes');</script>";
            echo "<script>console.log('Nome do arquivo: " . $nomeCapa . "');</script>";
            
            $estoque = $_POST['estoque'];
            $autor = $_POST['autor'];
            $dataPublicacao = $_POST['dataPublicacao'];
            $numeroPaginas = $_POST['numeroPaginas'];
            $categoria = $_POST['categoria'];
            $descricao = $_POST['descricao'];
            $editora = $_POST['editora'];
            $isbn = $_POST['isbn'];
            $idioma = $_POST['idioma'];
        
            $sqlInsereLivro = "INSERT INTO livros (titulo, autor_id, categoria_id, isbn, ano_publicacao, numero_paginas, descricao, imagem_capa, estoque, editora, idioma, ativo) 
            VALUES (:titulo, :autor, :categoria, :isbn, :dataPublicacao, :numeroPaginas, :descricao, :capa, :estoque, :editora, :idioma, TRUE)";
        
            $stmt = $pdo->prepare($sqlInsereLivro);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':dataPublicacao', $dataPublicacao);
            $stmt->bindParam(':numeroPaginas', $numeroPaginas);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':capa', $capa, PDO::PARAM_LOB);
            $stmt->bindParam(':estoque', $estoque);
            $stmt->bindParam(':editora', $editora);
            $stmt->bindParam(':idioma', $idioma);
        
            if($stmt->execute() && $_SESSION['cargo'] == 1){
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Livro cadastrado com sucesso!']);
                exit;
            }elseif($stmt->execute() && $_SESSION['cargo'] == 2){
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Livro cadastrado com sucesso!']);
                exit;
            }elseif($_SESSION['cargo'] == 1){
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erro ao cadastrar livro!']);
                exit;
            }else{
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erro ao cadastrar livro!']);
                exit;
            }
        }
    }
?>