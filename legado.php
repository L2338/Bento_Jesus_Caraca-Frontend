<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Legado</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/BJC_logo.png" rel="icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="events-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/epbjc-logo.png" alt="Logo Epbjc" > 
        

      </a>

      <?php
      include('Menu.php');
      ?>

      <a class="btn-getstarted" href="login.php">Login</a>

    </div>
  </header>
  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1>Legado</h1>
              <p class="mb-0">Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.</p>
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs">
        <div class="container">
          <ol>
            <li><a href="index.php">Início</a></li>
            <li class="current">Legado</li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/LogoCosmos.png" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h2>Biblioteca Cosmos</h2>
            <p class="fst-italic">
             Um projeto revolucionário que democratizou o saber em Portugal.
            </p>
            <ul>
            <li><i class="bi bi-check-circle"></i> <span>Fundada em <strong>1941</strong> por Bento de Jesus Caraça, a <strong>Biblioteca Cosmos</strong> foi uma das iniciativas editoriais mais ambiciosas da época.</span></li>
          <li><i class="bi bi-check-circle"></i> <span>Com o objetivo de levar cultura e ciência ao povo, publicou mais de <strong>114 títulos</strong> em <strong>145 Volumes</strong> cobrindo temas como matemática, literatura, história, filosofia e ciências naturais.</span></li>
          <li><i class="bi bi-check-circle"></i> <span>A coleção teve uma circulação massiva, distribuindo quase <strong>800.000 exemplares</strong> e tornando-se referência na divulgação do conhecimento.</span></li>
          <li><i class="bi bi-check-circle"></i> <span>Mesmo enfrentando censura durante o Estado Novo, a Biblioteca Cosmos marcou gerações e influenciou o pensamento crítico em Portugal.</span></li>
            </ul>
            <a href="http://www.bibliotecacosmos.com/" target="_blank" class="read-more"><span>Saber Mais</span><i class="bi bi-arrow-right"></i></a>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <section id="counts" class="section counts light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

        <?php
          // Buscar estatísticas do banco de dados
          $query_stats = "SELECT chave, valor, descricao FROM estatisticas ORDER BY id ASC";
          $result_stats = mysqli_query($conn, $query_stats);
          
          if ($result_stats && mysqli_num_rows($result_stats) > 0) {
              while ($stat = mysqli_fetch_assoc($result_stats)) {
                  ?>
                  <div class="col-lg-3 col-md-6">
                    <div class="stats-item text-center w-100 h-100">
                      <span data-purecounter-start="0" data-purecounter-end="<?php echo $stat['valor']; ?>" data-purecounter-duration="1" class="purecounter"></span>
                      <p><?php echo $stat['descricao']; ?></p>
                    </div>
                  </div>
                  <?php
              }
          }
          ?>

        </div>

      </div>

    </section><!-- /Counts Section -->

  </main>

  <?php
  include("footer.php");
  ?>
  
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>