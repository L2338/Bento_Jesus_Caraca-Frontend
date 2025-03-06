<?php
      $rootDir = $_SERVER["REQUEST_URI"];
    
      ?>

<nav id="navmenu" class="navmenu">
    <ul>
      <li><a href="index.php" <?php if ($rootDir == "/Projeto/Bento_Jesus_Caraca-Frontend/index.php"){?> class="active" <?php } ?>>Início</a></li>
      <li><a href="sobre.php" <?php if ($rootDir == "/Projeto/Bento_Jesus_Caraca-Frontend/sobre.php"){?> class="active" <?php } ?>>Sobre</a></li>
      <li><a href="obras.php" <?php if ($rootDir == "/Projeto/Bento_Jesus_Caraca-Frontend/obras.php"){?> class="active" <?php } ?>>Obras</a></li>
      <li><a href="legado.php" <?php if ($rootDir == "/Projeto/Bento_Jesus_Caraca-Frontend/legado.php"){?> class="active" <?php } ?>>Legado</a></li>
      <li class="dropdown">
        <a href="#"><span>Galeria</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li class="dropdown">
              <a href="#"><span>Fotos Históricas</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Imagens de Arquivo</a></li>
                <li><a href="#">Documentos Raros</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#"><span>Eventos Importantes</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Palestras e Conferências</a></li>
                <li><a href="#">Participações Acadêmicas</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#"><span>Publicações e Contribuições</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Livros e Artigos</a></li>
                <li><a href="#">Contribuições Científicas</a></li>
              </ul>
            </li>
            <li><a href="#">Acervo Digital</a></li>
          </ul>
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>