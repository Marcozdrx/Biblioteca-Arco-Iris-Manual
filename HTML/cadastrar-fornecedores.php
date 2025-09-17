<?php
session_start();
require_once '../PHP/conexao.php';

// Verificar se o usuário está logado e é admin
if (!isset($_SESSION['id']) || $_SESSION['cargo'] != 1) {
    header("Location: login.php");
    exit();
}
  try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
      $nome = $_POST['nome'];
      $cpfCnpj = $_POST['cpfCnpj'];
      $telefone = $_POST['telefone'];
      $email = $_POST['email'];
      $cep = $_POST['cep'];
      $cidade = $_POST['cidade'];
      $estado = $_POST['estado'];

      // Construir o endereço completo
      $endereco = $cidade . " - " . $estado;

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
        echo "<script>
                alert('Fornecedor cadastrado com sucesso!');
                window.location.href = 'fornecedores.php';
                </script>";
                exit;
      } else {
        echo "<script>
                alert('Erro ao cadastrar forncedores!');
                window.location.href = 'fonecedores.php';
                </script>";
                exit;
      }
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
    <script src="../JS/mascaras.js"></script>
    <script> 
        // BUSCAR PELO CEP //
        function buscarCEP(cep) {
            cep = cep.replace(/\D/g, '');
            if (cep.length !== 8) {
                return; // Não mostra alerta, apenas retorna silenciosamente
            }

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(res => {
                    if (!res.ok) throw new Error("Erro ao buscar o CEP");
                    return res.json();
                })
                .then(data => {
                    if (data.erro) {
                        alert("CEP não encontrado! Verifique o número do CEP informado.");
                        return;
                    }

                    // Preencher os campos automaticamente
                    document.querySelector('input[name="estado"]').value = data.uf || '';
                    document.querySelector('input[name="cidade"]').value = data.localidade || '';
                    
                    // Focar no campo cidade após preencher
                    document.querySelector('input[name="cidade"]').focus();
                })
                .catch(error => {
                    alert("Erro ao buscar o CEP. Tente novamente.");
                    console.error("Erro ao buscar o CEP:", error);
                });
        }

        function validarFormulario() {
            var nome = document.getElementById('nome').value;
            var cpfCnpj = document.getElementById('cpfCnpj').value;
            var telefone = document.getElementById('telefone').value;
            var email = document.getElementById('email').value;
            
            if (!nome || !cpfCnpj || !telefone) {
                alert("Por favor, preencha todos os campos obrigatórios!");
                return false;
            }
            return true;
        }

        // Adicionar evento para buscar CEP automaticamente
        document.addEventListener('DOMContentLoaded', function() {
            const cepInput = document.getElementById('cep');
            
            cepInput.addEventListener('blur', function() {
                if (this.value.length === 8) {
                    buscarCEP(this.value);
                }
            });
            
            cepInput.addEventListener('input', function() {
                // Formatar CEP enquanto digita (opcional)
                let value = this.value.replace(/\D/g, '');
                if (value.length > 8) {
                    value = value.substring(0, 8);
                }
                this.value = value;
            });
        });
    </script>
</head>
<body style="background-image: url(../IMG/fundo.png);
             background-size: cover;
             background-position: center;
             background-repeat: no-repeat;
            ">      
<div>
    <a class="voltar" href="fornecedores.php">Voltar</a>
</div>
   <form id="meuForm" method="POST" name="meuForm" action="cadastrar-fornecedores.php"> 
   <table align="center">
            <tr>
                <td><center><img src="../IMG/logo.png" alt="Logo"></center></td>
                <th colspan="6">Cadastro de Fornecedor</th>
            </tr>
            <tr>
                <td><center>Nome:</center></td>
                <td colspan="5"><input type="text" id="nome" name="nome" oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '')" maxlength="120" required /></td>
            </tr>
            <tr>
                <td><center>CPF/CNPJ:</center></td>
                <td colspan="5"><input type="text" id="cpfCnpj" name="cpfCnpj" data-mascara="cpfCnpj" placeholder="000.000.000-00 ou 00.000.000/0000-00" maxlength="18" required /></td>
            </tr>
            <tr>
                <td><center>Telefone:</center></td>
                <td colspan="5"><input type="text" id="telefone" name="telefone" data-mascara="telefone" placeholder="(00) 00000-0000" maxlength="15" required /></td>
            </tr>
            <tr>
                <td><center>E-mail:</center></td>
                <td colspan="5"><input type="email" id="email" name="email" maxlength="100" required /></td>
            </tr>
            <tr>
                <td><center>CEP:</center></td>
                <td><input type="text" id="cep" name="cep" data-mascara="cep" placeholder="00000-000" maxlength="9" onblur="buscarCEP(this.value)" required/></td>
                <td>Cidade:</td>
                <td><input type="text" id="cidade" name="cidade"  oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '')"  required/></td>
                <td>Estado:</td>
                <td><input type="text" id="estado" name="estado" maxlength="2"  oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '')" required /></td>
            </tr>
            <tr>
                <td colspan="6"><center><button type="submit">Salvar</button></center></td>
            </tr>
        </table>
    </form>
    <script type="text/javascript" src="../JS/cadastrofornecedor.js"></script>
</body>
</html>         

