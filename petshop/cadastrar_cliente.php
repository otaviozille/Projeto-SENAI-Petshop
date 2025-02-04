<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';
require './PHPMailer-master/src/Exception.php';

// Habilitar relatórios de erro para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escapar dados enviados pelo formulário
    $nome = $conn->real_escape_string($_POST["nomeCompleto"]);
    $cpf = $conn->real_escape_string($_POST["cpf"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $telefone = $conn->real_escape_string($_POST["telefone"]);
    $endereco = $conn->real_escape_string($_POST["endereco"]);

    // Verificar se o CPF já existe na tabela clientes
    $check_cpf = "SELECT cpf FROM clientes WHERE cpf = '$cpf'";
    $result = $conn->query($check_cpf);

    if ($result->num_rows > 0) {
        echo "<script>alert('Erro: O CPF informado já está cadastrado.');</script>";
    } else {
        // Inserir cliente no banco de dados
        $sql = "INSERT INTO clientes (nomeCompleto, cpf, email, telefone, endereco) 
                VALUES ('$nome', '$cpf', '$email', '$telefone', '$endereco')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Cliente cadastrado com sucesso!');</script>";

            // Criar o e-mail de confirmação
            $assunto = "Confirmação de Cadastro - PetShop";
            $mensagem = "
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <title>Cadastro realizado com sucesso!</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        padding: 20px;
                        text-align: center;
                    }
                    .container {
                        background-color: #ffffff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                        max-width: 600px;
                        margin: auto;
                    }
                    h2 {
                        color: #5aa6a4;
                    }
                    p {
                        font-size: 16px;
                        line-height: 1.5;
                    }
                    .footer {
                        margin-top: 20px;
                        font-size: 14px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Olá, $nome!</h2>
                    <p>Seu cadastro no <strong>PetShop</strong> foi realizado com sucesso.</p>
                    <p>Agradecemos por confiar em nossos serviços!</p>
                    <hr>
                    <p><strong>Seus dados cadastrados:</strong></p>
                    <p><strong>Nome:</strong> $nome</p>
                    <p><strong>CPF:</strong> $cpf</p>
                    <p><strong>E-mail:</strong> $email</p>
                    <p><strong>Telefone:</strong> $telefone</p>
                    <p><strong>Endereço:</strong> $endereco</p>
                    <hr>
                    <p class='footer'>Atenciosamente,<br><strong>Equipe PetShop Happy Paws</strong></p>
                </div>
            </body>
            </html>
            ";

            // Configurar o envio de e-mail com PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'otaviozillefranco@gmail.com'; // Seu e-mail
                $mail->Password = 'zwnt suen vkir yvuf'; // Senha de Aplicativo do Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // Porta SMTP

                // Remetente e destinatário
                $mail->setFrom('otaviozillefranco@gmail.com', 'PetShop');
                $mail->addAddress($email, $nome);

                // Configurar charset para UTF-8
                $mail->CharSet = 'UTF-8';

                // Conteúdo do e-mail
                $mail->isHTML(true);
                $mail->Subject = $assunto;
                $mail->Body = $mensagem;

                // Enviar e-mail
                if ($mail->send()) {
                    echo "<script>alert('E-mail de confirmação enviado com sucesso!');</script>";
                } else {
                    echo "<script>alert('Erro ao enviar e-mail de confirmação.');</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Erro ao enviar e-mail: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('Erro ao cadastrar o cliente: " . $conn->error . "');</script>";
        }
    }
}

// Fechar conexão com o banco de dados
$conn->close();

// Redirecionar para a página de cadastro
echo "<script>window.location.href = 'cadastros.html';</script>";

?>
