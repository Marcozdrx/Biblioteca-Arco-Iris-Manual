<?php
require_once '../PHP/conexao.php';
  try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $nome = $_POST['nome'];
      $cpfCnpj = $_POST['cpfCnpj'];
      $telefone = $_POST['telefone'];
      $senha = $_POST['senha'];
      $email = $_POST['email'];
      $cep = $_POST['cep'];
      $numCasa = $_POST['numCasa'];
      $complemento = $_POST['complemento'];
      $bairro = $_POST['bairro'];
      $cidade = $_POST['cidade'];
      $estado = $_POST['estado'];
      $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

      $sql = "INSERT INTO usuarios (nome, cpfCnpj, telefone, email, cep, numCasa, complemento, bairro, cidade, estado) VALUES (:nome, :cpfCnpj, :telefone, :email, :cep, :numCasa, :complemento, :bairro, :cidade, :estado)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(":nome", $nome);
      $stmt->bindParam(":cpfCnpj", $cpfCnpj);
      $stmt->bindParam(":telefone", $telefone);
      $stmt->bindParam(":senha", $senha_hash);
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":cep", $cep);
      $stmt->bindParam(":numCasa", $numCasa);
      $stmt->bindParam(":complemento", $complemento);
      $stmt->bindParam(":bairro", $bairro);
      $stmt->bindParam(":cidade", $cidade);
      $stmt->bindParam(":estado", $estado);
      
      if($stmt->execute()) {
        echo "<script>alert('Fornecedor cadastrado com sucesso');</script>";
      } else {
        echo "<script>alert('Erro ao cadastrar fornecedor');</script>";
      }

      header('Location: fornecedores.php');
      exit();
    }
  } catch (Exception $e) {
    echo "<script>alert('Erro ao cadastrar fornecedor: " . $e->getMessage() . "');</script>";
  }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro Fornecedor</title>
    <link rel="stylesheet" href="../CSS/cadastro_fornecedor.css" />
</head>
<body style="background-image: url(IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;
            ">
   <!-- <form id="meuForm" method="post" name="meuForm" action="PHP/cadastrofornecedor.php">
   -->        
   <form id="meuForm" method="post" name="meuForm" action="inicio-admin.php"> 
   <table align="center">
            <tr>
                <td><center><img src="IMG/logo.png" alt="Logo"></center></td>
                <th colspan="3">Cadastro de Fornecedor</th>
            </tr>
            <tr>
                <td><center>Nome:</center></td>
                <td class="a" colspan="3"><input type="text" id="nome" name="nome" /></td>
            </tr>
            <tr>
                <td><center>CEP:</center></td>
                <td><input type="text" id="cep" name="cep" /></td>
                <td>Nº Casa:</td>
                <td><input type="text" id="numCasa" name="numCasa" /></td>
                <td>Complemento:</td>
                <td><input type="text" id="complemento" name="complemento" /></td>
                <td>Bairro:</td>
                <td><input type="text" id="bairro" name="bairro" /></td>
                <td>Cidade:</td>
                <td><input type="text" id="cidade" name="cidade" /></td>
                <td>Estado:</td>
                <td><input type="text" id="estado" name="estado" /></td>
            </tr>
            <tr>
                <td><center>CPF/CNPJ:</center></td>
                <td colspan="3"><input type="text" id="cpfCnpj" name="cpfCnpj" /></td>
            </tr>
            <tr>
                <td><center>Telefone:</center></td>
                <td colspan="3"><input type="text" id="telefone" name="telefone" /></td>
            </tr>
            <tr>
                <td><center>E-mail:</center></td>
                <td colspan="3"><input type="email" id="email" name="email" /></td>
            </tr>
            <tr>
                <td colspan="4"><center><a href="php/cadastrofornecedor.php"><button type="submit">Salvar</button></a></center></td>
            </tr>
        </table>
    </form>
    <script type="text/javascript" src="JS/cadastrofornecedor.js"></script>
</body>
</html>