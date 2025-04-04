<?php
// Desativar a exibição de erros para o navegador (serão logados apenas)
ini_set('display_errors', 0);

// Carrega as variáveis de ambiente do arquivo .env
if (!function_exists('loadEnv')) {
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
}

// Carrega as variáveis de ambiente
loadEnv();

// Configurações do banco de dados - usar banco local XAMPP
// Mantenha db4free.net como backup, mas priorize conexão local
$host = $_ENV['DB_HOST'] ?? 'localhost';
$usuario = $_ENV['DB_USER'] ?? 'root';
$senha = $_ENV['DB_PASSWORD'] ?? '';
$banco = $_ENV['DB_DATABASE'] ?? 'escolaepbjc3';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

// Estabelece a conexão
try {
    // Desativa relatório de erro padrão do mysqli para evitar que ele termine a execução
    mysqli_report(MYSQLI_REPORT_OFF); 
    
    // Tenta a conexão com timeout reduzido (2 segundos) para não bloquear a página
    // Definindo timeout para evitar que a página fique travada
    $conn = @mysqli_connect($host, $usuario, $senha, $banco);
    
    // Se a conexão falhar, tentar conectar ao db4free.net como backup
    if (!$conn) {
        // Registrar erro de conexão primária
        error_log("Erro na conexão primária ao banco de dados: " . mysqli_connect_error());
        
        // Tenta a conexão com o banco de dados remoto
        $host_backup = 'db4free.net';
        $usuario_backup = 'julismosilva';
        $senha_backup = 'FarinhaJulismo';
        
        $conn = @mysqli_connect($host_backup, $usuario_backup, $senha_backup, $banco);
    }
    
    // Define o charset se a conexão for bem-sucedida
    if ($conn && !mysqli_set_charset($conn, $charset)) {
        throw new Exception("Erro ao definir charset: " . mysqli_error($conn));
    }
    
    // Retorna a conexão para uso em outros arquivos
    return $conn;
} catch (Exception $e) {
    // Registrar erro no log
    error_log("Erro excepcional na conexão com a base de dados: " . $e->getMessage());
    
    // Em APIs, retorna null para que o chamador possa decidir o que fazer
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return null;
    }
    
    // Em páginas normais, podemos mostrar uma mensagem de erro mais amigável
    // Mas sem mostrar detalhes técnicos
    //header('HTTP/1.1 500 Internal Server Error');
    //echo "<div class='alert alert-danger'>Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.</div>";
    //exit;
    
    // Retornamos false para permitir que as páginas tratem o erro
    return false;
}
?>