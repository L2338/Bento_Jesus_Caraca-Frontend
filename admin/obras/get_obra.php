<?php
/**
 * API para buscar dados de uma obra específica
 * Retorna os dados em formato JSON para uso no modal
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
$conn = require_once '../../ConfigBD.php';

// Buscar a obra pelo ID diretamente
$sql = "SELECT o.*, t.Nome_tema 
        FROM obras o 
        LEFT JOIN Temas t ON o.id_tema = t.id_tema 
        WHERE o.id = $id";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $obra = $result->fetch_assoc();
    echo json_encode($obra);
} else {
    echo json_encode(['error' => 'Obra não encontrada']);
} 