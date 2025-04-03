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

// Incluir arquivo index para ter acesso à classe Obra
require_once 'index.php';

// Buscar a obra pelo ID
$obra = $obraModel->buscarPorId($id);

if (!$obra) {
    echo json_encode(['error' => 'Obra não encontrada']);
    exit;
}

// Retornar os dados da obra
echo json_encode($obra); 