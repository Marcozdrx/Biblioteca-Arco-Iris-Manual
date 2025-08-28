<?php
require_once '../PHP/conexao.php';
  try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
      $nome = $_POST['nome'];
      $cpfCnpj = $_POST['cpfCnpj'];
      $telefone = $_POST['telefone'];
      $email = $_POST['email'];
      $cep = $_POST['cep'];
      $numCasa = $_POST['numCasa'];
      $complemento = $_POST['complemento'];
      $bairro = $_POST['bairro'];
      $cidade = $_POST['cidade'];
      $estado = $_POST['estado'];

      // Construir o endereço completo
      $endereco = $numCasa . " - " . $complemento . " - " . $bairro;

      $sql = "INSERT INTO fornecedores (nome, cpf_cnpj, telefone, email, cep, endereco, cidade, estado) VALUES (:nome, :cpf_cnpj, :telefone, :email, :cep, :endereco, :cidade, :estado)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(":nome", $nome);
      $stmt->bindParam(":cpf_cnpj", $cpfCnpj);
      $stmt->bindParam(":telefone", $telefone);
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":cep", $cep);
      $stmt->bindParam(":endereco", $endereco);
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
<body style="background-image: url(../IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;
            ">      
   <form id="meuForm" method="POST" name="meuForm" action="cadastrar-fornecedores.php"> 
   <table align="center">
            <tr>
                <td><center><img src="../IMG/logo.png" alt="Logo"></center></td>
                <th colspan="6">Cadastro de Fornecedor</th>
            </tr>
            <tr>
                <td><center>Nome:</center></td>
                <td colspan="5"><input type="text" id="nome" name="nome" required /></td>
            </tr>
            <tr>
                <td><center>CPF/CNPJ:</center></td>
                <td colspan="5"><input type="text" id="cpfCnpj" name="cpfCnpj" required /></td>
            </tr>
            <tr>
                <td><center>Telefone:</center></td>
                <td colspan="5"><input type="text" id="telefone" name="telefone" required /></td>
            </tr>
            <tr>
                <td><center>E-mail:</center></td>
                <td colspan="5"><input type="email" id="email" name="email" /></td>
            </tr>
            <tr>
                <td><center>CEP:</center></td>
                <td><input type="text" id="cep" name="cep" /></td>
                <td>Nº Casa:</td>
                <td><input type="text" id="numCasa" name="numCasa" /></td>
                <td>Complemento:</td>
                <td><input type="text" id="complemento" name="complemento" /></td>
            </tr>
            <tr>
                <td><center>Bairro:</center></td>
                <td><input type="text" id="bairro" name="bairro" /></td>
                <td>Cidade:</td>
                <td><input type="text" id="cidade" name="cidade" /></td>
                <td>Estado:</td>
                <td><input type="text" id="estado" name="estado" maxlength="2" /></td>
            </tr>
            <tr>
                <td colspan="6"><center><button type="submit">Salvar</button></center></td>
            </tr>
        </table>
    </form>
    <script type="text/javascript" src="../JS/cadastrofornecedor.js"></script>
</body>
</html