<?php
/**
 * Template: Sidebar
 * Descrição: Barra lateral padrão para todas as páginas do painel administrativo
 */

// Obter o nome do arquivo atual para marcar o item de menu correto como ativo
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <!-- Logo e Título -->
    <a class="sidebar-brand" href="<?php echo ADMIN_URL; ?>dashboard.php">
        <div class="sidebar-brand-text">Bento J. Caraça</div>
    </a>
    
    <hr class="sidebar-divider">
    
    <!-- Navegação principal -->
    <div class="sidebar-heading">Principal</div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>dashboard.php">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
    </div>
    
    <hr class="sidebar-divider">
    
    <!-- Conteúdo -->
    <div class="sidebar-heading">Conteúdo</div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo strpos($current_page, 'obras') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>obras/">
            <i class="bi bi-book"></i>
            <span>Obras</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo strpos($current_page, 'vida') !== false || strpos($current_page, 'timeline') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>vida/">
            <i class="bi bi-person-lines-fill"></i>
            <span>Vida</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo strpos($current_page, 'legado') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>legado/">
            <i class="bi bi-award"></i>
            <span>Legado</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo strpos($current_page, 'galeria') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>galeria/">
            <i class="bi bi-images"></i>
            <span>Galeria</span>
        </a>
    </div>
    
    <hr class="sidebar-divider">
    
    <!-- Instituição -->
    <div class="sidebar-heading">Instituição</div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo strpos($current_page, 'escolas') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>escolas/">
            <i class="bi bi-buildings"></i>
            <span>Escolas</span>
        </a>
    </div>
    
    <hr class="sidebar-divider">
    
    <!-- Configurações -->
    <div class="sidebar-heading">Sistema</div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>users.php">
            <i class="bi bi-people"></i>
            <span>Usuários</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_URL; ?>settings.php">
            <i class="bi bi-gear"></i>
            <span>Configurações</span>
        </a>
    </div>
    
    <hr class="sidebar-divider">
    
    <!-- Utilitários -->
    <div class="nav-item">
        <a class="nav-link" href="<?php echo SITE_URL; ?>" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Ver Site</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a class="nav-link" href="<?php echo ADMIN_URL; ?>auth/logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>
    </div>
</div> 