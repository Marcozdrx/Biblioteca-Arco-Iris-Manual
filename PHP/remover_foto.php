<?php
session_start();
require_once 'conexao.php'; // seu arquivo de conexão com o banco

// Supondo que você tenha o id do usuário na sessão
$usuario_id = $_SESSION['id'];

if (isset($_POST['remover_foto'])) {
    // Pegar o nome da foto atual no banco
    $sql = "SELECT foto FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && !empty($usuario['foto'])) {
        $caminho_foto = 'uploads/' . $usuario['foto'];

        // Atualiza o banco para remover referência da foto
        $sql_update = "UPDATE usuarios SET foto = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $usuario_id);
        $stmt_update->execute();
    }

    header("Location: ../HTML/perfil.php"); // volta para a página do perfil
    exit();
}
?>
