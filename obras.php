<?php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

include('ConfigBD.php');

// Consulta SQL para obter todas as obras
$sql = "SELECT * FROM obras ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Obras - Bento de Jesus Caraça</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- PDF.js CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
</head>

<body>
  <!-- ======= Header ======= -->
  <?php include 'header.php'; ?>

  <main id="main">
    <!-- ======= Breadcrumbs ======= -->
    <div class="breadcrumbs" data-aos="fade-in">
      <div class="container">
        <h2>Obras</h2>
        <p>Aqui pode encontrar todas as obras de Bento de Jesus Caraça.</p>
      </div>
    </div>

    <!-- ======= Courses Section ======= -->
    <section id="courses" class="courses">
      <div class="container" data-aos="fade-up">
        <div class="row" data-aos="zoom-in" data-aos-delay="100">
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo '<div class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4">';
              echo '<div class="course-item">';
              echo '<img src="assets/img/obras/' . $row["imagem"] . '" class="img-fluid" alt="' . $row["titulo"] . '">';
              echo '<div class="course-content">';
              echo '<h3><a href="javascript:void(0);" onclick="openPdfModal(\'' . $row["pdf"] . '\')">' . $row["titulo"] . '</a></h3>';
              echo '<p>' . $row["descricao"] . '</p>';
              echo '</div>';
              echo '</div>';
              echo '</div>';
            }
          } else {
            echo "Nenhuma obra encontrada.";
          }
          ?>
        </div>
      </div>
    </section>
  </main>

  <!-- PDF Modal -->
  <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center">
            <button type="button" class="btn btn-outline-light me-2" id="prevPage" disabled>
              <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="btn btn-outline-light me-2" id="nextPage">
              <i class="bi bi-chevron-right"></i>
            </button>
            <span class="text-white">Página <span id="currentPage">1</span> de <span id="totalPages">0</span></span>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div id="pdfViewer" class="pdf-viewer"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ======= Footer ======= -->
  <?php include 'footer.php'; ?>

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
</body>

</html>
<?php $conn->close(); ?>