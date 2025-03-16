<!DOCTYPE html>
<html lang="en">
<?php
include('ConfigBD.php');
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Obras </title>
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
  <!--novas bibliotecas -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
  <section id="obras" class="courses section">
    <div class="container">
      <div class="row">

        <?php
          // Consulta todas as obras
          $sql = "SELECT * FROM obras ORDER BY id ASC";
          $resultado = mysqli_query($conn, $sql);

          // Verifica se encontrou registros
          if (mysqli_num_rows($resultado) > 0) {
              // Loop para cada obra
              while ($obra = mysqli_fetch_assoc($resultado)) {
                  ?>
                  <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="course-item">
                      <!-- Imagem de capa da obra -->
                      <img src="<?php echo $obra['Galileo_Galilei.png']; ?>" class="img-fluid" alt="<?php echo $obra['titulo']; ?>">

                      <div class="course-content">
                        <!-- Se quiser exibir a categoria ou subtítulo -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <p class="category">Obra</p>
                        </div>

                        <!-- Título da obra -->
                        <h3><?php echo $obra['titulo']; ?></h3>

                        <!-- Descrição da obra -->
                        <p class="description"><?php echo $obra['descricao']; ?></p>

                        <div class="trainer d-flex justify-content-between align-items-center">
                          <div class="trainer-profile d-flex align-items-center">
                            <!-- Exemplo de imagem fixa para o “autor” (se quiser algo dinâmico, crie outro campo no BD) 
                            <img src="assets/img/trainers/trainer-1-2.jpg" class="img-fluid" alt="Autor">
                            -->
                            <!-- Botão que abre o modal para ver o PDF -->
                            <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalObra_<?php echo $obra['id']; ?>">
                              Ver PDF
                            </button>
                          </div>
                          <div class="trainer-rank d-flex align-items-center">
                            <!-- Link para baixar o PDF diretamente -->
                            <img src="<?php echo $obra['imagem_capa']; ?>" class="img-fluid" alt="<?php echo $obra['titulo']; ?>">
                          </div>
                        </div>
                      </div><!-- /course-content -->
                    </div><!-- /course-item -->
                  </div><!-- /col-lg-4 -->

                  <!-- Modal para exibir o PDF da obra atual -->
                  <div class="modal fade" id="modalObra_<?php echo $obra['id']; ?>" tabindex="-1" aria-labelledby="modalObraLabel_<?php echo $obra['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalObraLabel_<?php echo $obra['id']; ?>"><?php echo $obra['titulo']; ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                          <!-- Iframe para visualizar o PDF embutido -->
                          <iframe src="<?php echo $obra['pdf']; ?>" width="100%" height="600px" style="border:none;"></iframe>
                        </div>
                        <div class="modal-footer">
                          <a href="<?php echo $obra['pdf']; ?>" download class="btn btn-success ms-2">Baixar PDF</a>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php
              }
          } else {
              echo "<p>Nenhuma obra cadastrada.</p>";
          }

          // Fecha a conexão
          mysqli_close($conn);
        ?>

      </div><!-- /row -->
    </div><!-- /container -->
  </section><!-- /section -->


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