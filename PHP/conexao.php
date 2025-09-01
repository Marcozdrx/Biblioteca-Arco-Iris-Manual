<?php
/**
 * ARQUIVO DE CONEXÃO COM BANCO DE DADOS
 * Biblioteca Arco-Íris
 */
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'biblioteca_arco_iris');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');


try {
    // String de conexão DSN
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // Opções do PDO para segurança
    $opcoes = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    // Criar conexão PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
    
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>
