<?php
// Desativar a exibição de erros para o navegador (serão logados apenas)
ini_set('display_errors', 0);

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
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Isso faz o mysqli lançar exceções em vez de morrer
    $conn = mysqli_connect($host, $usuario, $senha, $banco);
    
    // Define o charset
    if (!mysqli_set_charset($conn, $charset)) {
        throw new Exception("Erro ao definir charset: " . mysqli_error($conn));
    }
    
    // Retorna a conexão para uso em outros arquivos
    return $conn;
} catch (Exception $e) {
    // Registrar erro no log
    error_log("Erro na conexão com a base de dados: " . $e->getMessage());
    
    // Em APIs, retorna null para que o chamador possa decidir o que fazer
    // Em vez de chamar die(), que impediria a resposta JSON
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return null;
    }
    
    // Em páginas normais, podemos mostrar uma mensagem de erro mais amigável
    // Mas sem mostrar detalhes técnicos
    header('HTTP/1.1 500 Internal Server Error');
    echo "<div class='alert alert-danger'>Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.</div>";
    exit;
}
?>