<?php
/**
 * Configurações globais do painel administrativo
 */

// Iniciar sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definição de constantes globais
define('APP_NAME', 'Painel Administrativo - Bento de Jesus Caraça');
define('APP_VERSION', '1.0.0');

// URLs e caminhos do sistema
$base_path = '/Projeto/Bento_Jesus_Caraca-Frontend/';
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . $base_path);
define('ADMIN_URL', SITE_URL . 'admin/');
define('ASSETS_URL', SITE_URL . 'assets/');
define('ADMIN_ASSETS_URL', ADMIN_URL . 'assets/');

// Configurações de timezone
date_default_timezone_set('Europe/Lisbon');
setlocale(LC_TIME, 'pt_PT.utf8');

// Configurações de exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Função para gerar URLs para o admin
 * 
 * @param string $path Caminho relativo
 * @return string URL completa
 */
function admin_url($path = '') {
    return ADMIN_URL . $path;
}

/**
 * Função para gerar URLs para assets do site principal
 * 
 * @param string $path Caminho relativo
 * @return string URL completa
 */
function site_asset($path = '') {
    return ASSETS_URL . $path;
}

/**
 * Função para gerar URLs para assets do admin
 * 
 * @param string $path Caminho relativo
 * @return string URL completa
 */
function admin_asset($path = '') {
    return ADMIN_ASSETS_URL . $path;
}

/**
 * Verifica se uma página está ativa
 * 
 * @param string $page Nome da página para verificar
 * @return string Classe CSS 'active' se a página estiver ativa
 */
function is_active_page($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($page === 'dashboard' && $current_page === 'dashboard.php') {
        return 'active';
    }
    
    if (strpos($current_page, $page) !== false) {
        return 'active';
    }
    
    return '';
}

/**
 * Função para incluir recursos CSS do site principal
 * 
 * @param array $files Lista de arquivos CSS para incluir
 * @return string Tags HTML para inclusão dos CSS
 */
function include_site_css($files = []) {
    $output = '';
    
    // Se não foi especificado, usar Bootstrap e o CSS principal
    if (empty($files)) {
        $files = ['vendor/bootstrap/css/bootstrap.min.css', 'css/main.css'];
    }
    
    foreach ($files as $file) {
        $output .= '<link href="' . site_asset($file) . '" rel="stylesheet">' . PHP_EOL;
    }
    
    return $output;
}

/**
 * Função para incluir recursos JavaScript do site principal
 * 
 * @param array $files Lista de arquivos JS para incluir
 * @return string Tags HTML para inclusão dos JS
 */
function include_site_js($files = []) {
    $output = '';
    
    // Se não foi especificado, usar Bootstrap e o JS principal
    if (empty($files)) {
        $files = ['vendor/bootstrap/js/bootstrap.bundle.min.js', 'js/main.js'];
    }
    
    foreach ($files as $file) {
        $output .= '<script src="' . site_asset($file) . '"></script>' . PHP_EOL;
    }
    
    return $output;
}
?> 