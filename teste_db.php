<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=biblioteca_arco_iris', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar total de livros
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM livros');
    $result = $stmt->fetch();
    echo "Total de livros: " . $result['total'] . "\n";
    
    // Verificar último livro inserido
    $stmt = $pdo->query('SELECT * FROM livros ORDER BY id DESC LIMIT 1');
    $ultimoLivro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ultimoLivro) {
        echo "Último livro inserido:\n";
        echo "ID: " . $ultimoLivro['id'] . "\n";
        echo "Título: " . $ultimoLivro['titulo'] . "\n";
        echo "Autor ID: " . $ultimoLivro['autor_id'] . "\n";
        echo "Categoria ID: " . $ultimoLivro['categoria_id'] . "\n";
        echo "Ativo: " . $ultimoLivro['ativo'] . "\n";
    }
    
    // Verificar se há problemas com o JOIN
    $stmt = $pdo->query('SELECT l.*, a.nome as nome_autor FROM livros l INNER JOIN autores a ON l.autor_id = a.id');
    $livrosComAutor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Livros com autor (JOIN): " . count($livrosComAutor) . "\n";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
