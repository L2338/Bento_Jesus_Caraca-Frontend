<?php
/**
 * Retorna lista de imagens de cursos em formato JSON
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Configuração de diretórios
$upload_dir = '../../assets/img/courses/';
$web_dir = 'assets/img/courses/';

// Lista de imagens
$images = [];
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $images[] = [
                'path' => $web_dir . $file,
                'name' => $file,
                'size' => filesize($upload_dir . $file),
                'modified' => filemtime($upload_dir . $file)
            ];
        }
    }
}

// Ordenar por data de modificação (mais recente primeiro)
usort($images, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

// Remover a propriedade 'modified' para a saída final
foreach ($images as &$image) {
    unset($image['modified']);
}

// Definir cabeçalho como JSON
header('Content-Type: application/json');

// Retornar array como JSON
echo json_encode($images);
?> 