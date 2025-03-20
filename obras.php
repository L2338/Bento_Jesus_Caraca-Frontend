<?php
include('ConfigBD.php');

// Consulta SQL para buscar os dados da obra
$sql = "SELECT * FROM obras WHERE id = 1";
$result = mysqli_query($conn, $sql);
$obra = mysqli_fetch_assoc($result);
// Consulta SQL para buscar os dados da obra
$sql = "SELECT * FROM obras order by id";
$result=$conn-> query($sql);
//$obra = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Obras</title>
  <title>Obras</title>

  <!-- Favicons -->
  <link href="assets/img/BJC_logo.png" rel="icon">
  <link href="assets/img/BJC_logo.png" rel="icon">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  
  <!-- PDF.js CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css">
  
  <!-- Custom CSS -->
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  
  <!-- PDF.js CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css">
  
  <!-- Custom CSS -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    .pdf-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 90vh;
      background: linear-gradient(135deg, rgba(139, 0, 0, 0.95) 0%, rgba(220, 53, 69, 0.9) 100%);
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
      position: relative;
      overflow: hidden;
    }

    .modal-dialog {
      max-width: 95vw !important;
      margin: 0.5rem auto;
      transition: transform 0.4s ease-out;
    }

    .modal-content {
      background: transparent;
      border: none;
      border-radius: 0;
      overflow: hidden;
      height: 98vh;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .modal-header {
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: none;
      padding: 1rem;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      z-index: 10;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-header .btn-close {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background-color: transparent;
      border: 2px solid rgba(255, 255, 255, 0.8);
      opacity: 1;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      margin: 0;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-header .btn-close:hover {
      background-color: rgba(255, 255, 255, 0.9);
      transform: translateY(-50%) rotate(180deg);
      border-color: transparent;
    }

    .modal-header .btn-close::before {
      content: "×";
      color: rgba(255, 255, 255, 0.9);
      font-size: 24px;
      line-height: 1;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      transition: color 0.3s ease;
    }

    .modal-header .btn-close:hover::before {
      color: #8b0000;
    }

    .modal-header .modal-title {
      color: rgba(255, 255, 255, 0.95);
      font-size: 1.2rem;
      margin: 0;
      font-weight: 500;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
      letter-spacing: 0.5px;
    }

    .pdf-viewer {
      width: 100%;
      height: 100%;
      position: relative;
      overflow: hidden;
      background: linear-gradient(135deg, rgba(139, 0, 0, 0.97) 0%, rgba(220, 53, 69, 0.95) 100%);
    }

    /* Estilo do Loading */
    .loading-container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      background: rgba(0, 0, 0, 0.7);
      padding: 2.5rem 3.5rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      z-index: 1000;
    }

    .loading-spinner {
      width: 60px;
      height: 60px;
      margin-bottom: 1.5rem;
      border: 3px solid rgba(255, 255, 255, 0.2);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    .loading-text {
      color: #fff;
      font-size: 1.1rem;
      font-weight: 500;
      margin: 0;
      opacity: 0.9;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .loading-subtext {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.9rem;
      margin-top: 0.5rem;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .book-container {
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      perspective: 2000px;
      padding: 80px 40px 40px;
    }

    .book {
      position: relative;
      width: 100%;
      height: 100%;
      transform-style: preserve-3d;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .page-wrapper {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      transform-origin: center;
      transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1);
      background: white;
      border-radius: 5px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.3);
    }

    .page {
      background: white;
      margin: 0 15px;
      border-radius: 8px;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.25);
      transition: all 0.3s ease;
      max-height: calc(90vh - 160px);
    }

    .page.cover {
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
    }

    .pdf-controls {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0.75rem 1.5rem;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 50px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      z-index: 10;
    }

    .pdf-controls .page-info {
      color: rgba(255, 255, 255, 0.95);
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .pdf-controls .page-input {
      width: 50px;
      height: 32px;
      background: rgba(255,255,255,0.15);
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      color: white;
      text-align: center;
      font-size: 1rem;
      padding: 0 0.5rem;
      transition: all 0.3s ease;
      -moz-appearance: textfield;
    }

    .pdf-controls .page-input::-webkit-outer-spin-button,
    .pdf-controls .page-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    .pdf-controls .page-input:focus {
      outline: none;
      border-color: rgba(255,255,255,0.9);
      background: rgba(255,255,255,0.2);
      box-shadow: 0 0 0 2px rgba(255,255,255,0.1);
    }

    .loading {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      background: rgba(0, 0, 0, 0.6);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    @keyframes turnForward {
      0% { transform: rotateY(0); }
      100% { transform: rotateY(-180deg); }
    }

    @keyframes turnBackward {
      0% { transform: rotateY(-180deg); }
      100% { transform: rotateY(0); }
    }

    .page-wrapper.turning {
      transform-origin: left center;
    }

    .page-wrapper.turning-forward {
      animation: turnForward 0.7s cubic-bezier(0.645, 0.045, 0.355, 1) forwards;
    }

    .page-wrapper.turning-backward {
      animation: turnBackward 0.7s cubic-bezier(0.645, 0.045, 0.355, 1) forwards;
    }

    /* Remover o botão de scroll top */
    .scroll-top {
      display: none !important;
    }
  </style>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- PDF.js -->
  <!-- jQuery (necessário para Bootstrap e outros plugins) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- PDF.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

  <!-- Turn.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js"></script>

  <!-- Main JS -->
  <script src="assets/js/main.js"></script>
  <script>
    // Configuração do PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  </script>
</head>

<body class="courses-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/epbjc-logo.png" alt="Logo EPBJC">
      </a>

      <?php include('Menu.php'); ?>
      
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
  </div>

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
                <button class="btn btn-ler" data-pdf="<?php echo $obra['pdf']; ?>">
                    Ler
                  </button>
                </div>
              </div>
            </div>
          </div>
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
    <?php
if($result->num_rows>0){
    while($post=$result->fetch_assoc()){

?>
    <div class="row">
      <!-- Item da Obra -->
      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up">
        <div class="course-item">
          <div class="course-image-container">
            <img src="assets/img/<?php echo htmlspecialchars($post['imagem_capa']); ?>" class="img-fluid obra-imagem" alt="Capa da Obra">
          </div>
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="category">Obra</span>
            </div>
            <div class="autor-section">
              <span class="autor-label">Autor</span>
              <h4 class="autor-nome"><?php echo $post['autor']; ?></h4>
            </div>
            <h3><?php echo $post['titulo']; ?></h3>
            <p class="description"><?php $post['descricao'] . '...'; ?></p>
            <div class="obra-footer">
              <a href="assets/pdf/obras/<?php echo $post['pdf']; ?>" download class="btn-download-direct">
                <i class="bi bi-file-pdf"></i>
              </a>
              <button class="btn btn-ler" onclick="openPdfModal('<?php echo $post['pdf']; ?>')">
                Ler
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
      
    </div>  
  </div>
  <?php }
} ?>
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
                <span class="page-info">
                  Página <input type="number" id="currentPage" class="page-input" value="0" min="1"> de <span id="totalPages">0</span>
                </span>
              </div>
            </div>
          </div>
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
  </main>

<?php include("footer.php"); ?>

<!-- Preloader -->
<div id="preloader"></div>
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
<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<script>
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;
  let scale = 1.0;

  async function openPdfModal(pdfPath) {
    try {
      if (!pdfPath.startsWith('assets/pdf/obras/')) {
        pdfPath = 'assets/pdf/obras/' + pdfPath;
      }
      
      const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
      const pdfViewer = document.getElementById('pdfViewer');
      
      pdfViewer.innerHTML = `
        <div class="book-container">
          <div class="book">
            <div class="page-wrapper">
              <div class="loading-container">
                <div class="loading-spinner"></div>
                <p class="loading-text">Carregando PDF</p>
                <p class="loading-subtext">Por favor, aguarde...</p>
              </div>
            </div>
          </div>
        </div>
      `;
      
      pdfModal.show();
      
      const loadingTask = pdfjsLib.getDocument({
        url: pdfPath,
        cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
        cMapPacked: true
      });
      
      pdfDoc = await loadingTask.promise;
      document.getElementById('totalPages').textContent = pdfDoc.numPages;
      
      await new Promise(resolve => setTimeout(resolve, 300));
      await renderPages(1);
    } catch (error) {
      console.error('Erro ao carregar PDF:', error);
      alert('Erro ao carregar o PDF. Por favor, verifique se o arquivo existe e tente novamente.');
    }
  }

  async function renderPages(newPage) {
    if (pageRendering) {
      pageNumPending = newPage;
      return;
    }

    try {
      pageRendering = true;
      
      const direction = newPage > pageNum ? 'next' : 'prev';
      const currentWrapper = document.querySelector('.page-wrapper');
      
      if (currentWrapper) {
        currentWrapper.classList.add('turning', direction === 'next' ? 'turning-forward' : 'turning-backward');
        await new Promise(resolve => setTimeout(resolve, 350));
      }
      
      const container = document.querySelector('.pdf-viewer');
      const containerWidth = container.clientWidth - 100;
      const containerHeight = container.clientHeight - 100;
      
      const page = await pdfDoc.getPage(newPage);
      const viewport = page.getViewport({ scale: 1.0 });
      
      const scaleW = containerWidth / viewport.width;
      const scaleH = containerHeight / viewport.height;
      scale = Math.min(scaleW, scaleH, 1.2);
      
      const scaledViewport = page.getViewport({ scale });
      
      const pageWrapper = document.createElement('div');
      pageWrapper.className = 'page-wrapper';
      
      const canvas = document.createElement('canvas');
      canvas.height = scaledViewport.height;
      canvas.width = scaledViewport.width;
      canvas.classList.add('page');
      
      await page.render({
        canvasContext: canvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;
        
      pageWrapper.appendChild(canvas);
      
      const book = document.createElement('div');
      book.className = 'book';
      book.appendChild(pageWrapper);
      
      const bookContainer = document.querySelector('.book-container');
      bookContainer.innerHTML = '';
      bookContainer.appendChild(book);
      
      document.getElementById('currentPage').value = newPage.toString();
      pageNum = newPage;
      pageRendering = false;
      
      if (pageNumPending !== null) {
        renderPages(pageNumPending);
        pageNumPending = null;
      }
    } catch (error) {
      console.error('Erro ao renderizar página:', error);
      pageRendering = false;
    }
  }

  // Event Listeners
  document.addEventListener('DOMContentLoaded', function() {
    const pdfModalElement = document.getElementById('pdfModal');
    if (pdfModalElement) {
      const pdfModalInstance = new bootstrap.Modal(pdfModalElement);
      
      pdfModalElement.addEventListener('hidden.bs.modal', () => {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
          pdfViewer.innerHTML = '';
          pdfDoc = null;
          pageNum = 1;
        }
      });
    }

    const btnLer = document.querySelector('.btn-ler');
    if (btnLer) {
      btnLer.addEventListener('click', function(e) {
        const pdfPath = this.getAttribute('data-pdf');
        if (pdfPath) {
          openPdfModal(pdfPath);
        }
      });
    }

    const pageInput = document.getElementById('currentPage');
    if (pageInput) {
      pageInput.addEventListener('change', function() {
        const newPage = parseInt(this.value);
        if (!isNaN(newPage) && newPage >= 1 && newPage <= pdfDoc.numPages) {
          renderPages(newPage);
        } else {
          this.value = pageNum;
        }
      });

      pageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          const newPage = parseInt(this.value);
          if (!isNaN(newPage) && newPage >= 1 && newPage <= pdfDoc.numPages) {
            renderPages(newPage);
    } else {
            this.value = pageNum;
          }
        }
      });
    }

  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (pdfDoc) {
        renderPages(pageNum);
      }
    }, 250);
    });
  });
</script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<script>
  // Prevenir arraste de imagens
  document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.course-item img');
    images.forEach(function(img) {
      img.addEventListener('dragstart', function(e) {
        e.preventDefault();
      });
      img.addEventListener('mousedown', function(e) {
        e.preventDefault();
      });
    });
  });
</script>

<!-- Turn.js e jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js"></script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
  // Configuração do PDF.js
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;
  let scale = 1.0;

  async function openPdfModal(pdfPath) {
    try {
      if (!pdfPath.startsWith('assets/pdf/obras/')) {
        pdfPath = 'assets/pdf/obras/' + pdfPath;
      }
      
      const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
      const pdfViewer = document.getElementById('pdfViewer');
      
      // Limpar o visualizador e mostrar loading
      pdfViewer.innerHTML = `
        <div class="book-container">
          <div class="book">
            <div class="page-wrapper">
              <div class="loading text-center p-5 text-white">
                <div class="spinner-border text-light mb-3" role="status">
                  <span class="visually-hidden">Carregando...</span>
                </div>
                <div>Carregando PDF...</div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Mostrar o modal
      pdfModal.show();
      
      // Carregar o PDF
      const loadingTask = pdfjsLib.getDocument({
        url: pdfPath,
        cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
        cMapPacked: true
      });
      
      pdfDoc = await loadingTask.promise;
      document.getElementById('totalPages').textContent = pdfDoc.numPages;
      
      // Garantir que o modal está totalmente visível antes de renderizar
      await new Promise(resolve => setTimeout(resolve, 300));
      
      // Renderizar a primeira página
      await renderPages(1);
    } catch (error) {
      console.error('Erro ao carregar PDF:', error);
      alert('Erro ao carregar o PDF. Por favor, verifique se o arquivo existe e tente novamente.');
    }
  }

  async function renderPages(startPage) {
    try {
      pageRendering = true;
      
      const isCover = startPage === 1;
      const container = document.querySelector('.pdf-viewer');
      const containerWidth = container.clientWidth - 80;
      const containerHeight = container.clientHeight - 60;
      
      const firstPage = await pdfDoc.getPage(startPage);
      const viewport = firstPage.getViewport({ scale: 1.0 });
      
      const scaleW = containerWidth / (isCover ? 1 : 2) / viewport.width;
      const scaleH = containerHeight / viewport.height;
      scale = Math.min(scaleW, scaleH, 1.5);
      
      const scaledViewport = firstPage.getViewport({ scale });
      
      const pageWrapper = document.createElement('div');
      pageWrapper.className = 'page-wrapper';
      
      const pages = [];
      
      if (isCover) {
        const coverCanvas = document.createElement('canvas');
        coverCanvas.height = scaledViewport.height;
        coverCanvas.width = scaledViewport.width;
        coverCanvas.classList.add('page', 'cover');
        
        await firstPage.render({
          canvasContext: coverCanvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;
        
        pages.push(coverCanvas);
        document.getElementById('currentPage').textContent = '1';
      } else {
        document.getElementById('currentPage').textContent = `${startPage}-${Math.min(startPage + 1, pdfDoc.numPages)}`;
        
        const leftCanvas = document.createElement('canvas');
        leftCanvas.height = scaledViewport.height;
        leftCanvas.width = scaledViewport.width;
        leftCanvas.classList.add('page', 'left');
        
        await firstPage.render({
          canvasContext: leftCanvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;
        
        pages.push(leftCanvas);
        
        if (startPage + 1 <= pdfDoc.numPages) {
          const rightPage = await pdfDoc.getPage(startPage + 1);
          const rightCanvas = document.createElement('canvas');
          rightCanvas.height = scaledViewport.height;
          rightCanvas.width = scaledViewport.width;
          rightCanvas.classList.add('page', 'right');
          
          await rightPage.render({
            canvasContext: rightCanvas.getContext('2d'),
            viewport: scaledViewport
          }).promise;
          
          pages.push(rightCanvas);
        }
      }
      
      pages.forEach(canvas => pageWrapper.appendChild(canvas));
      
      const book = document.createElement('div');
      book.className = 'book';
      book.appendChild(pageWrapper);
      
      const bookContainer = document.querySelector('.book-container');
      bookContainer.innerHTML = '';
      bookContainer.appendChild(book);
      
      const prevButton = document.getElementById('prevPage');
      const nextButton = document.getElementById('nextPage');
      prevButton.disabled = startPage <= 1;
      nextButton.disabled = startPage >= pdfDoc.numPages;
      
      pageRendering = false;
      pageNum = startPage;
      
      if (pageNumPending !== null) {
        renderPages(pageNumPending);
        pageNumPending = null;
      }
    } catch (error) {
      console.error('Erro ao renderizar páginas:', error);
      pageRendering = false;
    }
  }

  async function turnPages(direction) {
    if (pageRendering) return;
    
    const currentWrapper = document.querySelector('.page-wrapper');
    if (!currentWrapper) return;
    
    currentWrapper.classList.add('turning');
    
    if (direction === 'next') {
      currentWrapper.classList.add('turning-forward');
      const leftPage = currentWrapper.querySelector('.page.left');
      if (leftPage) {
        leftPage.style.transformOrigin = 'right center';
        leftPage.style.boxShadow = '-15px 0 35px rgba(0,0,0,0.4)';
      }
    } else {
      currentWrapper.classList.add('turning-backward');
      const rightPage = currentWrapper.querySelector('.page.right');
      if (rightPage) {
        rightPage.style.transformOrigin = 'left center';
        rightPage.style.boxShadow = '15px 0 35px rgba(0,0,0,0.4)';
      }
    }
    
    if (direction === 'next' && pageNum < pdfDoc.numPages) {
      setTimeout(() => {
        pageNum = Math.min(pageNum + (pageNum === 1 ? 1 : 2), pdfDoc.numPages);
        queueRenderPages(pageNum);
        
        setTimeout(() => {
          currentWrapper.classList.remove('turning', 'turning-forward', 'turning-backward');
          const pages = currentWrapper.querySelectorAll('.page');
          pages.forEach(page => {
            page.style.boxShadow = '';
            page.style.transformOrigin = '';
          });
        }, 700);
      }, 350);
    } else if (direction === 'prev' && pageNum > 1) {
      setTimeout(() => {
        pageNum = Math.max(pageNum - 2, 1);
        queueRenderPages(pageNum);
        
        setTimeout(() => {
          currentWrapper.classList.remove('turning', 'turning-forward', 'turning-backward');
          const pages = currentWrapper.querySelectorAll('.page');
          pages.forEach(page => {
            page.style.boxShadow = '';
            page.style.transformOrigin = '';
          });
        }, 700);
      }, 350);
    }
  }

  document.getElementById('prevPage').addEventListener('click', () => {
    if (pageNum <= 1) return;
    turnPages('prev');
  });

  document.getElementById('nextPage').addEventListener('click', () => {
    if (pageNum >= pdfDoc.numPages) return;
    turnPages('next');
  });

  function queueRenderPages(num) {
    if (pageRendering) {
      pageNumPending = num;
    } else {
      renderPages(num);
    }
  }

  document.getElementById('pdfModal').addEventListener('hidden.bs.modal', () => {
    const pdfViewer = document.getElementById('pdfViewer');
    pdfViewer.innerHTML = '';
    pdfDoc = null;
    pageNum = 1;
  });

  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (pdfDoc) {
        renderPages(pageNum);
      }
    }, 250);
  });
</script>

<style>
.pdf-container {
  display: flex;
  flex-direction: column;
  height: 92vh;
  position: relative;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
  background: linear-gradient(135deg,
    rgba(94, 37, 99, 0.98) 0%,
    rgba(196, 18, 48, 0.98) 100%
  );
  margin: 0;
}

.modal-dialog {
  max-width: 95vw !important;
  margin: 1rem auto;
}

.modal-content {
  background: transparent;
  border: none;
  height: 95vh;
}

.modal-header {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 15px 15px 0 0;
  backdrop-filter: blur(5px);
}

.modal-header .btn-close {
  position: relative;
  width: 40px;
  height: 40px;
  padding: 0;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  transition: all 0.3s ease;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header .btn-close:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.modal-header .btn-close::before,
.modal-header .btn-close::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 2px;
  background: white;
  transform-origin: center;
}

.modal-header .btn-close::before {
  transform: translate(-50%, -50%) rotate(45deg);
}

.modal-header .btn-close::after {
  transform: translate(-50%, -50%) rotate(-45deg);
}

.modal-header .modal-title {
  color: white;
  font-weight: 500;
  font-size: 1.25rem;
  text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.pdf-viewer {
  flex: 1;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  perspective: 3000px;
  min-height: calc(100vh - 200px);
  background: rgba(255, 255, 255, 0.05);
  border-radius: 15px;
  margin: 10px 20px;
  box-shadow: inset 0 0 30px rgba(0,0,0,0.2);
}

.book-container {
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  perspective: 4000px;
}

.book {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform 0.6s ease-in-out;
  display: flex;
  justify-content: center;
  align-items: center;
}

.page-wrapper {
  position: relative;
  transform-origin: center;
  transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1);
  transform-style: preserve-3d;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 40px;
}

.page-wrapper.turning {
  transition: transform 0.7s cubic-bezier(0.645, 0.045, 0.355, 1);
}

.page {
  background: white;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
  border-radius: 3px;
  transform-origin: center;
  transition: all 0.7s cubic-bezier(0.645, 0.045, 0.355, 1);
  backface-visibility: hidden;
  position: relative;
}

.page::after {
  content: '';
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.page.left::after {
  right: 0;
  background: linear-gradient(to left, rgba(0,0,0,0.2) 0%, transparent 20%);
}

.page.right::after {
  left: 0;
  background: linear-gradient(to right, rgba(0,0,0,0.2) 0%, transparent 20%);
}

.page.cover {
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.4);
  transform: translateZ(1px);
}

.page.left {
  transform-origin: right center;
  box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
}

.page.right {
  transform-origin: left center;
  box-shadow: 5px 0 25px rgba(0, 0, 0, 0.2);
}

.page-wrapper.turning-forward .page.left {
  transform: rotateY(-180deg);
  transform-origin: right center;
}

.page-wrapper.turning-backward .page.right {
  transform: rotateY(180deg);
  transform-origin: left center;
}

.pdf-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 30px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 0 0 15px 15px;
  backdrop-filter: blur(5px);
}

.pdf-controls button {
  width: 50px;
  height: 50px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  transition: all 0.3s ease;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.pdf-controls button:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.pdf-controls button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.page-info {
  font-size: 16px;
  color: white;
  background: 
}
</body>
</html>
