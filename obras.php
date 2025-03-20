<?php
include('ConfigBD.php');
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
            <p class="description"><?php echo substr($post['descricao'], 0, 150). '...'; ?></p>
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
      
    </div>  
  </div>
</section>

<!-- Modal do PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfModalLabel"><?php echo $post['titulo']; ?></h5>
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
<?php }
} ?>
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