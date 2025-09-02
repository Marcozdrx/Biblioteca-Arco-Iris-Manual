<?php
require_once 'conexao.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $titulo = $_POST['titulo'] ?? '';
    $sqlPesquisarLivros = "SELECT * FROM livros WHERE titulo LIKE '%$titulo%' AND ativo = TRUE";
    $stmt = $pdo->prepare($sqlPesquisarLivros);
    $stmt->execute();
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($livros);
    exit;
}
?>
