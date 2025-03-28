<?php
      $rootDir = $_SERVER["REQUEST_URI"];
    
      ?>

<nav id="navmenu" class="navmenu">
    <ul>
    <li><a href="index.php" <?php if (basename($_SERVER['PHP_SELF']) == "index.php") echo 'class="active"'; ?>>Início</a></li>
    <li><a href="vida.php" <?php if (basename($_SERVER['PHP_SELF']) == "vida.php") echo 'class="active"'; ?>>Vida</a></li>
    <li><a href="obras.php" <?php if (basename($_SERVER['PHP_SELF']) == "obras.php") echo 'class="active"'; ?>>Obras</a></li>
    <li><a href="legado.php" <?php if (basename($_SERVER['PHP_SELF']) == "legado.php") echo 'class="active"'; ?>>Legado</a></li>

      <li class="dropdown">
        <a href="#"><span>Galeria</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li >
              <a href="#"><span>Retratos</span> </a>             
            </li>
            <li >
              <a href="#"><span>Amigos</span> </a>            
            </li>
            <li >
              <a href="#"><span>Viagens</span> </a>
            </li>  
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>