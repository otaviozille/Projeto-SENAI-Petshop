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
    // Coletar e escapar os dados do formulário
    $nome = $conn->real_escape_string($_POST["nome"]);
    $tipo = $conn->real_escape_string($_POST["tipo"]);
    $idade = $conn->real_escape_string($_POST["idade"]);
    $dataNascimento = $conn->real_escape_string($_POST["dataNascimento"]);
    $observacoes = $conn->real_escape_string($_POST["observacoes"]);
    $cpf = $conn->real_escape_string($_POST["cpf"]);

    // Verificar se o CPF existe na tabela clientes e buscar o e-mail do dono
    $check_cpf = "SELECT email, nomeCompleto FROM clientes WHERE cpf = '$cpf'";
    $result = $conn->query($check_cpf);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $emailDono = $row["email"];
        $nomeDono = $row["nomeCompleto"];

        // Inserir o pet no banco de dados
        $sql = "INSERT INTO pets (nome, tipo, idade, dataNascimento, observacoes, cpf) 
                VALUES ('$nome', '$tipo', '$idade', '$dataNascimento', '$observacoes', '$cpf')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Pet cadastrado com sucesso!');</script>";

            // Criar o e-mail de confirmação
            $assunto = "Confirmação de Cadastro do Pet - PetShop";
            $mensagem = "
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <title>Cadastro de Pet Confirmado!</title>
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
                    <h2>Olá, $nomeDono!</h2>
                    <p>Seu pet foi cadastrado com sucesso no nosso sistema.</p>
                    <p>Agradecemos por confiar no <strong>PetShop</strong>!</p>
                    <hr>
                    <p><strong>Dados do Pet:</strong></p>
                    <p><strong>Nome:</strong> $nome</p>
                    <p><strong>Tipo:</strong> $tipo</p>
                    <p><strong>Idade:</strong> $idade </p>
                    <p><strong>Data de Nascimento:</strong> $dataNascimento</p>
                    <p><strong>Observações:</strong> $observacoes</p>
                    <hr>
                    <p class='footer'>Atenciosamente,<br><strong>Equipe PetShop Happy Paws</strong></p>
                </div>
            </body>
            </html>
            ";

            // Configurar PHPMailer para envio de e-mail
            $mail = new PHPMailer(true);

            try {
                // Configuração do servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'otaviozillefranco@gmail.com'; // Seu e-mail
                $mail->Password = 'zwnt suen vkir yvuf'; // Senha de Aplicativo do Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // Porta SMTP

                // Remetente e destinatário
                $mail->setFrom('otaviozillefranco@gmail.com', 'PetShop');
                $mail->addAddress($emailDono, $nomeDono); 

                // Configurar charset para UTF-8
                $mail->CharSet = 'UTF-8';

                // Conteúdo do e-mail
                $mail->isHTML(true);
                $mail->Subject = $assunto;
                $mail->Body = $mensagem;

                // Enviar e-mail
                if ($mail->send()) {
                    echo "<script>alert('E-mail de confirmação enviado para $emailDono!');</script>";
                } else {
                    echo "<script>alert('Erro ao enviar e-mail de confirmação.');</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Erro ao enviar e-mail: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('Erro ao cadastrar o pet: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Erro: O CPF informado não está cadastrado. Cadastre o cliente primeiro.');</script>";
    }
}

// Fechar conexão com o banco de dados
$conn->close();

// Redirecionar para a página de cadastro
echo "<script>window.location.href = 'cadastros.html';</script>";

?>
