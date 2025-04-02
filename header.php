<?php
// Obter a página atual
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo_pagina) ? $titulo_pagina : 'Bento Jesus Caraça'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <?php if (isset($extra_css)): ?>
    <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="fixed-top">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/img/logo.png" alt="Bento Jesus Caraça" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>" href="index.php">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'obras.php' ? 'active' : ''; ?>" href="obras.php">Obras</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'escolas.php' ? 'active' : ''; ?>" href="escolas.php">Escolas Profissionais</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'timeline.php' ? 'active' : ''; ?>" href="timeline.php">Timeline</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'sobre.php' ? 'active' : ''; ?>" href="sobre.php">Sobre</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage == 'contato.php' ? 'active' : ''; ?>" href="contato.php">Contato</a>
                        </li>
                    </ul>
                    <div class="d-flex ms-lg-3">
                        <a href="admin/login.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-user me-1"></i> Área Administrativa
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Espaçamento para compensar o navbar fixo -->
    <div style="padding-top: 76px;"></div> 