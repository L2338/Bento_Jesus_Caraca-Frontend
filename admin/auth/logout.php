<?php
/**
 * Logout do usuário
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Efetuar logout
logout_user();

// Redirecionar para a página de login na raiz do site
header("Location: " . SITE_URL . "login.php?logout=success");
exit;
?> 