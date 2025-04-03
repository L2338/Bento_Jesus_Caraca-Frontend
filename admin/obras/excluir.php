<?php
/**
 * API para excluir uma obra
 * Recebe o ID via GET/POST e retorna resultado em JSON
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Obter ID da obra a ser excluída
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da obra não fornecido']);
    exit;
}

// Incluir arquivo index para ter acesso à classe Obra
require_once 'index.php';

// Buscar a obra para verificar existência e obter caminhos dos arquivos
$obra = $obraModel->buscarPorId($id);

if (!$obra) {
    echo json_encode(['success' => false, 'message' => 'Obra não encontrada']);
    exit;
}

// Excluir a obra
$resultado = $obraModel->excluir($id);

if ($resultado) {
    // Se a exclusão foi bem sucedida, remover arquivos associados
    if (!empty($obra['pdf'])) {
        $caminho_pdf = '../../assets/pdf/Obras/' . $obra['pdf'];
        if (file_exists($caminho_pdf)) {
            unlink($caminho_pdf);
        }
    }
    
    if (!empty($obra['imagem_capa'])) {
        $caminho_imagem = '../../assets/img/obras/' . $obra['imagem_capa'];
        if (file_exists($caminho_imagem)) {
            unlink($caminho_imagem);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Obra excluída com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir obra']);
}
?> 