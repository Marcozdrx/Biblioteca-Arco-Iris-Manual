<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=biblioteca_arco_iris', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar o último livro inserido
    $stmt = $pdo->query('SELECT * FROM livros ORDER BY id DESC LIMIT 1');
    $ultimoLivro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ultimoLivro) {
        echo "Último livro: " . $ultimoLivro['titulo'] . "\n";
        echo "Tamanho da imagem: " . strlen($ultimoLivro['imagem_capa']) . " bytes\n";
        echo "Primeiros 20 bytes: " . bin2hex(substr($ultimoLivro['imagem_capa'], 0, 20)) . "\n";
        
        // Verificar se começa com RIFF
        if (substr($ultimoLivro['imagem_capa'], 0, 4) === 'RIFF') {
            echo "✓ É uma imagem WebP (começa com RIFF)\n";
        } else {
            echo "✗ Não é WebP\n";
        }
        
        // Testar diferentes MIME types
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $ultimoLivro['imagem_capa']);
        finfo_close($finfo);
        echo "MIME detectado: " . $mimeType . "\n";
        
        // Testar base64
        $base64 = base64_encode($ultimoLivro['imagem_capa']);
        echo "Base64 (primeiros 50 chars): " . substr($base64, 0, 50) . "...\n";
        
        // Criar HTML de teste
        echo "\n=== HTML DE TESTE ===\n";
        echo '<img src="data:image/webp;base64,' . $base64 . '" alt="Teste" style="width:200px;height:200px;border:1px solid red;">';
        
    } else {
        echo "Nenhum livro encontrado\n";
    }
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
