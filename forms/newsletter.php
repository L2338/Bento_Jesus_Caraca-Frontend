<?php
// Configuração do banco de dados
$host = "db4free.net";
$user = "julismosilva";
$pass = "FarinhaJulismo";
$dbname = "escolaepbjc3";

// Conectar à base de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Prevenir SQL Injection
    $email = $conn->real_escape_string($email);

    // Inserir na base de dados
    $sql = "INSERT INTO newsletter (email) VALUES ('$email')";

    if ($conn->query($sql) === TRUE) {
        echo "OK";
    } else {
        echo "Erro ao subscrever: " . $conn->error;
    }
}

// Fechar conexão
$conn->close();
?>