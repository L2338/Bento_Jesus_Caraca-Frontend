<?php
// Carrega as variáveis de ambiente do arquivo .env
function loadEnv() {
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
}

// Carrega as variáveis de ambiente
loadEnv();

// Configurações do banco de dados
$host = $_ENV['DB_HOST'] ?? 'db4free.net';
$usuario = $_ENV['DB_USER'] ?? 'julismosilva';
$senha = $_ENV['DB_PASSWORD'] ?? 'FarinhaJulismo';
$banco = $_ENV['DB_DATABASE'] ?? 'escolaepbjc3';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

// Estabelece a conexão
$conn = mysqli_connect($host, $usuario, $senha, $banco);

// Verifica a conexão
if (!$conn) {
    error_log("Erro na conexão com a base de dados: " . mysqli_connect_error());
    die("Erro na conexão com a base de dados. Por favor, tente novamente mais tarde.");
}

// Define o charset
if (!mysqli_set_charset($conn, $charset)) {
    error_log("Erro ao definir charset: " . mysqli_error($conn));
}

// Retorna a conexão para uso em outros arquivos
return $conn;
?>