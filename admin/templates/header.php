<?php
/**
 * Template: Header
 * Descrição: Cabeçalho padrão para todas as páginas do painel administrativo
 */

// Verificar se o título da página foi definido, senão usar um padrão
$page_title = isset($page_title) ? $page_title . ' - Admin' : 'Painel Administrativo';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/img/favicon.png" type="image/png">
    <link rel="shortcut icon" href="<?php echo SITE_URL; ?>assets/img/favicon.png" type="image/x-icon">
    <meta name="theme-color" content="#ac062a">
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo SITE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="<?php echo SITE_URL; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link href="<?php echo ADMIN_URL; ?>assets/css/admin-style.css" rel="stylesheet">
    
    <!-- Tema personalizado -->
    <link href="<?php echo ADMIN_URL; ?>theme.php?v=<?php echo time(); ?>" rel="stylesheet">
    
    <!-- CSS Adicional específico de página, se houver -->
    <?php if(isset($extra_css)): ?>
    <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($_COOKIE['admin_theme_background']) && $_COOKIE['admin_theme_background'] !== 'light' ? 'theme-' . $_COOKIE['admin_theme_background'] : ''; ?>">
    <div class="d-flex">
        <!-- Sidebar (incluída separadamente) -->
        <?php include_once __DIR__ . '/sidebar.php'; ?>
        
        <!-- Conteúdo principal -->
        <div class="page-content">
            <!-- Barra superior -->
            <nav class="navbar navbar-expand navbar-light">
                <div class="container-fluid">
                    <!-- Botão de toggle da sidebar para mobile -->
                    <button id="sidebarToggle" class="btn">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <!-- Título da página -->
                    <h1 class="h3 mb-0 text-gray-800 d-none d-md-inline-block"><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?></h1>
                    
                    <div class="navbar-nav ms-auto">
                        <!-- Dropdown de notificações -->
                        <div class="nav-item dropdown me-3">
                            <?php 
                            // Obter contagem de notificações
                            $notification_count = get_notification_count();
                            
                            // Obter notificações recentes
                            $recent_notifications = get_unread_notifications(5);
                            ?>
                            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill"></i>
                                <?php if ($notification_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count">
                                    <?php echo $notification_count; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
                                <li><h6 class="dropdown-header">Central de Notificações</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <?php if (count($recent_notifications) > 0): ?>
                                    <?php foreach ($recent_notifications as $notification): ?>
                                    <li>
                                        <a class="dropdown-item notification-item" href="<?php echo $notification['link'] ? htmlspecialchars($notification['link']) : 'javascript:void(0)'; ?>" data-id="<?php echo $notification['id_notificacao']; ?>">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 <?php echo get_notification_color($notification['tipo']); ?>">
                                                    <i class="bi <?php echo get_notification_icon($notification['tipo']); ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="small fw-bold"><?php echo htmlspecialchars($notification['titulo']); ?></div>
                                                    <div class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($notification['mensagem']); ?></div>
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-clock-history"></i> <?php echo date('d/m H:i', strtotime($notification['created_at'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a class="dropdown-item text-center" href="#"><i class="bi bi-check-circle me-2"></i> Nenhuma notificação</a></li>
                                <?php endif; ?>
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <div class="dropdown-item d-flex justify-content-between">
                                        <a href="<?php echo ADMIN_URL; ?>notifications.php" class="text-decoration-none">Ver todas</a>
                                        <?php if ($notification_count > 0): ?>
                                        <a href="javascript:void(0)" class="text-decoration-none mark-all-read">Marcar como lidas</a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Dropdown de usuário -->
                        <div class="nav-item dropdown user-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-inline me-2"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Usuário'; ?></span>
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Perfil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-list-check me-2"></i> Atividade</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>admin/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Início do container principal -->
            <div class="dashboard-container">
                
                <!-- Avisos e mensagens flash -->
                <?php if (function_exists('display_flash_message')): ?>
                    <?php display_flash_message(); ?>
                <?php endif; ?>
                
                <!-- Breadcrumbs -->
                <?php if (function_exists('generate_breadcrumbs') && isset($breadcrumbs)): ?>
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <?php echo generate_breadcrumbs($breadcrumbs); ?>
                        </ol>
                    </nav>
                <?php endif; ?> 