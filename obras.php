<?php
include('ConfigBD.php');

// Consulta SQL para buscar os dados da obra
$sql = "SELECT * FROM obras WHERE id = 1";
$result = mysqli_query($conn, $sql);
$obra = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Obras</title>

  <!-- Favicons -->
  <link href="assets/img/BJC_logo.png" rel="icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  
  <!-- PDF.js CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css">
  
  <!-- Custom CSS -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- jQuery (necessário para Bootstrap e outros plugins) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- PDF.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    // Configuração do PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  </script>

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="courses-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/epbjc-logo.png" alt="Logo EPBJC" > 
        
      </a>

      <?php
      include('Menu.php');
      ?>
      
      <a class="btn-getstarted" href="courses.php">Entrar</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1>Obras</h1>
              <p class="mb-0">Iniciamos aqui a catalogação das obras de Bento de Jesus Caraça. Progressivamente, integraremos informações sobre suas diversas intervenções científicas, 
                culturais e políticas — sobretudo em órgãos de imprensa —, enriquecendo o panorama de sua produção intelectual e permitindo uma visão mais completa de seu legado.</p>
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs">
        <div class="container">
          <ol>
            <li><a href="index.php">Início</a></li>
            <li class="current">Obras</li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <!-- Courses Section -->
    <section id="courses" class="courses section">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mb-4">
            <div class="section-title">
              <h2>Catálogo</h2>
              <p>Obras de Bento de Jesus Caraça</p>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Item da Obra -->
          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up">
            <div class="course-item">
              <div class="course-image-container">
                <img src="assets/img/<?php echo htmlspecialchars($obra['imagem_capa']); ?>" class="img-fluid obra-imagem" alt="Capa da Obra">
              </div>
              <div class="course-content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="category">Obra</span>
                </div>
                <div class="autor-section">
                  <span class="autor-label">Autor</span>
                  <h4 class="autor-nome"><?php echo $obra['autor']; ?></h4>
                </div>
                <h3><?php echo $obra['titulo']; ?></h3>
                <p class="description"><?php echo substr($obra['descricao'], 0, 150) . '...'; ?></p>
                <div class="obra-footer">
                  <a href="assets/pdf/obras/<?php echo $obra['pdf']; ?>" download class="btn-download-direct">
                    <i class="bi bi-file-pdf"></i>
                  </a>
                  <button class="btn btn-ler" onclick="openPdfModal('<?php echo $obra['pdf']; ?>')">
                    Ler
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modal do PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="pdfModalLabel"><?php echo $obra['titulo']; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body p-0">
            <div class="pdf-container">
              <div id="pdfViewer" class="pdf-viewer"></div>
              <div class="pdf-controls">
                <button id="prevPage" class="btn btn-primary rounded-circle">
                  <i class="bi bi-chevron-left"></i>
                </button>
                <span id="pageInfo" class="page-info">
                  Página <span id="currentPage">0</span> de <span id="totalPages">0</span>
                </span>
                <button id="nextPage" class="btn btn-primary rounded-circle">
                  <i class="bi bi-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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