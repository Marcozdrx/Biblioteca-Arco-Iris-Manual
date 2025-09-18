<?php
    require_once '../PHP/conexao.php';
    session_start();
    $autores = [];
    $sqlBuscaAutor = "SELECT * FROM autores ORDER BY nome ASC";
    $stmt = $pdo->prepare($sqlBuscaAutor);
    $stmt->execute();
    $autores = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if($_SESSION['cargo'] != 1){
        echo "Acesso negado, apenas usuarios com permissão podem acessar essa pagina";
    }else{

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            // Adicionar novo livro
            $nome_autor = $_POST['nome_autor'];
            $biografia = $_POST['biografia_autor'];
            $nacionalidade = $_POST['nacionalidade_autor'];
            $data_nascimento = $_POST['data_nascimento'];
            $botaoAcao = $_POST['botao'];

            $sqlInsereAutor = "INSERT INTO autores (nome, biografia, nacionalidade, data_nascimento, ativo) 
            VALUES (:nome_autor, :biografia_autor, :nacionalidade_autor, :data_nascimento, TRUE)";
        
            $stmt = $pdo->prepare($sqlInsereAutor);
            $stmt->bindParam(':nome_autor', $nome_autor);
            $stmt->bindParam(':biografia_autor', $biografia);
            $stmt->bindParam(':nacionalidade_autor', $nacionalidade);
            $stmt->bindParam(':data_nascimento', $data_nascimento);
        
            if($stmt->execute() && $_SESSION['cargo'] == 1){
                echo "<script>
                alert('Autor cadastrado com sucesso!');
                window.location.href = '../HTML/cadastro-autor.php';
                </script>";
                exit;
            }else{
                echo "<script>
                alert('Erro ao cadastrar autor!');
                window.location.href = '../HTML/cadastro-autor.php';
                </script>";
                exit;
            }
        }
       
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Autor</title>

    <link rel="stylesheet" href="../CSS/cadastro-autor.css">

</head>
<body>
<div>
    <a class="voltar" href="fornecedores.php">Voltar</a>
</div>
<header class="header">
        <div class="header-title">
            <img src="../IMG/logo.png" alt="Logo" style="height: 30px;">
            <span>Biblioteca Arco-Íris - Gestão de Autores</span>
        </div>
    </header>
   

    <div class="users-table-container">
        <div class="table-wrapper">
            <form id="meuForm" method="POST" name="meuForm" class="formAutor" action="cadastro-autor.php"> 
                <table align="center">
                    <tr>
                        <td><center><img src="../IMG/logo.png" alt="Logo"></center></td>
                        <th colspan="6">Cadastro de Autor</th>
                    </tr>
                    <tr>
                        <td><center>Nome:</center></td>
                        <td colspan="5"><input type="text" id="nome" name="nome_autor" placeholder="Nome Autor" oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '')" maxlength="120" required /></td>
                    </tr>
                    <tr>
                        <td><center>Biografia:</center></td>
                        <td colspan="5"><input type="text" id="cpfCnpj" name="biografia_autor" placeholder="Biografia" required /></td>
                    </tr>
                    <tr>
                        <td><center>Nacionalidade:</center></td>
                        <td colspan="5"><input type="text" id="telefone" name="nacionalidade_autor" placeholder="Nacionalidade"required /></td>
                    </tr>
                    <tr>
                        <td><center>Data de nascimento:</center></td>
                        <td colspan="5"><input type="date" id="email" name="data_nascimento" required /></td>
                    </tr>
                    <tr>
                        <td colspan="6"><center><button type="submit">Salvar</button></center></td>
                        <td colspan="6"><center><button type="reset">Cancelar</button></center></td>
                    </tr>
                </table>
            </form>
                <table id="usersTable">
                    <thead>
                        
                        <tr>
                            <th>Nome</th>
                            <th>biografia</th>
                            <th>nacionalidade</th>
                            <th>Data de Nascimento</th>
                            <th>Ativo</th>
                        
                        </tr>
                        </thead>
                        <tbody id="usersTableBody">
                        <?php foreach($autores as $autor):?>
                            <tr>
                                
                                    <th><?=htmlspecialchars($autor['nome']) ?></th>
                                    <th><?=htmlspecialchars($autor['biografia']) ?></th>
                                    <th><?=htmlspecialchars($autor['nacionalidade']) ?></th>
                                    <th><?=htmlspecialchars($autor['data_nascimento']) ?></th>
                                    <?php if($autor['ativo'] == 1): ?>
                                    <th>Ativo</th>
                                    <?php else: ?>
                                    <th>Desativado</th>
                                    <?php endif; ?>
                                   
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>
