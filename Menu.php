<?php
$rootDir = $_SERVER["REQUEST_URI"];
?>

<nav id="navmenu" class="navmenu">
    <ul>
<<<<<<< Updated upstream
    <li><a href="index.php" <?php if (basename($_SERVER['PHP_SELF']) == "index.php") echo 'class="active"'; ?>>Início</a></li>
    <li><a href="vida.php" <?php if (basename($_SERVER['PHP_SELF']) == "vida.php") echo 'class="active"'; ?>>Vida</a></li>
    <li><a href="obras.php" <?php if (basename($_SERVER['PHP_SELF']) == "obras.php") echo 'class="active"'; ?>>Obras</a></li>
    <li><a href="legado.php" <?php if (basename($_SERVER['PHP_SELF']) == "legado.php") echo 'class="active"'; ?>>Legado</a></li>

      <li class="dropdown">
        <a href="#"><span>Galeria</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li >
              <a href="retratos.php"><span>Retratos</span> </a>             
            </li>
            <li >
              <a href="amigos.php"><span>Amigos</span> </a>            
            </li>
            <li >
              <a href="viagens.php"><span>Viagens</span> </a>
            </li>  
=======
        <li><a href="index.php" <?php if (basename($_SERVER['PHP_SELF']) == "index.php") echo 'class="active"'; ?>>Início</a></li>
        <li><a href="vida.php" <?php if (basename($_SERVER['PHP_SELF']) == "vida.php") echo 'class="active"'; ?>>Vida</a></li>
        <li><a href="obras.php" <?php if (basename($_SERVER['PHP_SELF']) == "obras.php") echo 'class="active"'; ?>>Obras</a></li>
        <li><a href="legado.php" <?php if (basename($_SERVER['PHP_SELF']) == "legado.php") echo 'class="active"'; ?>>Legado</a></li>
        <li class="dropdown">
            <a href="#"><span>Galeria</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
                <li><a href="retratos.php">Retratos</a></li>
                <li><a href="#">Amigos</a></li>
                <li><a href="#">Viagens</a></li>
            </ul>
        </li>
>>>>>>> Stashed changes
    </ul>
</nav>
<i class="mobile-nav-toggle d-lg-none bi bi-list"></i>