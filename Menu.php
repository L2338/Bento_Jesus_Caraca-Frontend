<?php
/**
 * Menu principal do site
 * Responsável por exibir a navegação principal com links para todas as seções
 */

// Obter diretório raiz para referências de caminho
$rootDir = $_SERVER["REQUEST_URI"];

// Obter o nome do arquivo atual para destacar o item de menu ativo
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="navmenu" class="navmenu">
    <ul>
        <!-- Item Início -->
        <li>
            <a href="index.php" <?php if ($current_page == "index.php") echo 'class="active"'; ?>>
                Início
            </a>
        </li>
        
        <!-- Item Vida -->
        <li>
            <a href="vida.php" <?php if ($current_page == "vida.php") echo 'class="active"'; ?>>
                Vida
            </a>
        </li>
        
        <!-- Item Obras -->
        <li>
            <a href="obras.php" <?php if ($current_page == "obras.php") echo 'class="active"'; ?>>
                Obras
            </a>
        </li>
        
        <!-- Item Legado -->
        <li>
            <a href="legado.php" <?php if ($current_page == "legado.php") echo 'class="active"'; ?>>
                Legado
            </a>
        </li>
        
        <!-- Menu dropdown Galeria -->
        <li class="dropdown">
            <a href="#">
                <span>Galeria</span> 
                <i class="bi bi-chevron-down toggle-dropdown"></i>
            </a>
            <ul>
                <li>
                    <a href="retratos.php">
                        <span>Retratos</span>
                    </a>             
                </li>
                <li>
                    <a href="amigos.php">
                        <span>Amigos</span>
                    </a>            
                </li>
                <li>
                    <a href="viagens.php">
                        <span>Viagens</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <!-- Botão de menu mobile -->
<i class="mobile-nav-toggle d-lg-none bi bi-list"></i>
</nav>



