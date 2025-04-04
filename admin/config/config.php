<?php
// Configurações básicas do site
$baseUrl = '/Projeto/Bento_Jesus_Caraca-Frontend/';
$siteTitle = 'Bento de Jesus Caraça - Painel Administrativo';

// Importar a conexão do banco de dados já configurada
$conn = require_once $_SERVER['DOCUMENT_ROOT'] . $baseUrl . 'ConfigBD.php';

// Verificar conexão
if (!$conn) {
    die("Falha na conexão com o banco de dados. Verifique se o serviço MySQL está ativo.");
}

// Configurar charset para UTF-8
$conn->set_charset("utf8");

// Funções de sessão e autenticação já são incluídas através do arquivo functions.php

// Configurações de timezone
date_default_timezone_set('Europe/Lisbon');

// Caminhos de diretórios importantes
$adminPath = $_SERVER['DOCUMENT_ROOT'] . $baseUrl . 'admin/';
$uploadsPath = $_SERVER['DOCUMENT_ROOT'] . $baseUrl . 'uploads/';
$imagesPath = $_SERVER['DOCUMENT_ROOT'] . $baseUrl . 'assets/img/';

// Configurações de segurança
$securitySalt = 'bjc_2024_secure_salt';
$sessionTimeout = 3600; // 1 hora

// Definir variáveis de ambiente
define('ENVIRONMENT', 'development'); // development, testing, production

// Configurações de debug
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
}
?> 