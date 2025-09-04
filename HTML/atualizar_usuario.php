<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['id'];
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    
    // Remover máscaras para salvar no banco
    $cpf_limpo = preg_replace('/\D/', '', $cpf);
    $telefone_limpo = preg_replace('/\D/', '', $telefone);
    
    // Validações básicas
    if (empty($nome) || empty($cpf_limpo) || empty($telefone_limpo)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipo_mensagem = 'erro';
    } elseif (strlen($cpf_limpo) != 11) {
        $mensagem = 'CPF deve ter 11 dígitos.';
        $tipo_mensagem = 'erro';
    } elseif (strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
        $mensagem = 'Telefone deve ter 10 ou 11 dígitos.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            // Verificar se CPF já existe para outro usuário
            $sql_verificar = "SELECT id FROM usuarios WHERE cpf = ? AND id != ? AND ativo = 1";
            $stmt_verificar = $pdo->prepare($sql_verificar);
            $stmt_verificar->execute([$cpf_limpo, $usuario_id]);
            
            if ($stmt_verificar->fetch()) {
                $mensagem = 'Este CPF já está cadastrado para outro usuário.';
                $tipo_mensagem = 'erro';
            } else {
                // Atualizar dados do usuário
                $sql = "UPDATE usuarios SET nome = ?, cpf = ?, telefone = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $resultado = $stmt->execute([$nome, $cpf_limpo, $telefone_limpo, $usuario_id]);
                
                if ($resultado && $stmt->rowCount() > 0) {
                    // Atualizar dados na sessão
                    $_SESSION['nome_usuario'] = $nome;
                    $_SESSION['cpf_usuario'] = $cpf_limpo;
                    $_SESSION['telefone_usuario'] = $telefone_limpo;
                    
                    $mensagem = 'Dados atualizados com sucesso!';
                    $tipo_mensagem = 'sucesso';
                } else {
                    $mensagem = 'Nenhuma alteração foi feita.';
                    $tipo_mensagem = 'info';
                }
            }
        } catch (PDOException $e) {
            $mensagem = 'Erro ao atualizar dados. Tente novamente.';
            $tipo_mensagem = 'erro';
        }
    }
}

// Redirecionar de volta para o perfil com mensagem
$redirect_url = "perfil.php";
if ($mensagem) {
    $redirect_url .= "?mensagem=" . urlencode($mensagem) . "&tipo=" . urlencode($tipo_mensagem);
}

header("Location: " . $redirect_url);
exit();
?>
