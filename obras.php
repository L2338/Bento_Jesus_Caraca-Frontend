<?php
include('ConfigBD.php');

// Buscar todos os temas disponíveis
$temasSql = "SELECT DISTINCT id_tema, Nome_tema FROM Temas ORDER BY id_tema";
$temasResult = $conn->query($temasSql);


// Consulta SQL para buscar os dados da obra
$temaSelecionado = isset($_GET['tema']) ? intval($_GET['tema']) : '';

$sql = "SELECT id, titulo, descricao, pdf, imagem_capa, autor, Nome_tema, ano 
        FROM obras 
        INNER JOIN Temas ON obras.id_tema = Temas.id_tema";

if (!empty($temaSelecionado)) {
    $sql .= " WHERE obras.id_tema = $temaSelecionado";
}

$sql .= " ORDER BY id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Obras</title>

  <!-- Favicons -->
  <link rel="icon" href="assets/img/favicon.png" type="image/png">
  <link rel="shortcut icon" href="assets/img/favicon.png">
  <meta name="theme-color" content="#ac062a">

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

<!-- Obras Section -->
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
    <div class="container">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="tema-filter">
                <label class="form-label">
                    <i class="bi bi-filter-circle"></i>
                    Filtrar por Tema:
                </label>
                <select id="temaFilter" class="form-select" onchange="filtrarPorTema()">
                    <option value="">Todos os Temas</option>
                    <?php 
                    if($temasResult->num_rows > 0) {
                      while($tema = $temasResult->fetch_assoc()) {
                        $selected = ($temaSelecionado == $tema['id_tema']) ? 'selected' : '';
                        echo "<option value='{$tema['id_tema']}' $selected>{$tema['Nome_tema']}</option>";
                      }
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</div>

    <div class="row">
    <?php
if($result->num_rows>0){
    while($post=$result->fetch_assoc()){

?>
      <!-- Item da Obra -->
      <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-tema="<?php echo $post['Nome_tema']; ?>" data-aos="fade-up">
        <div class="course-item">
          <div class="course-image-container">
            <img src="assets/img/obras/<?php echo htmlspecialchars($post['imagem_capa']); ?>" class="img-fluid obra-imagem" alt="Capa da Obra">
          </div>
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="category"><?php echo $post['Nome_tema']; ?></span>
              <?php if (isset($post['ano']) && !empty($post['ano'])): ?>
              <span class="obra-ano"><i class="bi bi-calendar3"></i> <?php echo $post['ano']; ?></span>
              <?php endif; ?>
            </div>
            <div class="autor-section">
              <span class="autor-label">Autor</span>
              <h4 class="autor-nome"><?php echo $post['autor']; ?></h4>
            </div>
            <h3><?php echo $post['titulo']; ?></h3>
            <p class="description"><?php echo substr($post['descricao'], 0, 200); ?></p>
            <div class="obra-footer">
              <a href="assets/pdf/obras/<?php echo $post['pdf']; ?>" download class="btn-download-direct">
                <i class="bi bi-file-pdf"></i>
              </a>
              <button class="btn btn-ler" onclick="openPdfModal('<?php echo $post['pdf']; ?>', '<?php echo addslashes($post['titulo']); ?>')">
                Ler
              </button>
            </div>
          </div>
        </div>
      </div>
      <?php }
} ?>
    </div>  
  </div>
</section>

<!-- Modal do PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfModalLabel"></h5>
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

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
  // Configuração do PDF.js
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
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

  async function openPdfModal(pdfPath, tituloObra) {
    try {
      if (!pdfPath.startsWith('assets/pdf/obras/')) {
        pdfPath = 'assets/pdf/obras/' + pdfPath;
      }
      
      const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
      const pdfViewer = document.getElementById('pdfViewer');
      
      // Definir o título no modal
      document.getElementById('pdfModalLabel').textContent = tituloObra;
      
      // Limpar o visualizador e mostrar loading
      pdfViewer.innerHTML = `
        <div class="book-container">
          <div class="book">
            <div class="page-wrapper">
              <div class="loading text-center p-5 text-white">
                <div class="spinner-border text-light mb-3" role="status">
                  <span class="visually-hidden">A carregar...</span>
                </div>
                <div>A carregar PDF...</div>
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
      
      // Verificar se é dispositivo móvel (largura < 768px)
      const isMobile = window.innerWidth < 768;
      
      const firstPage = await pdfDoc.getPage(startPage);
      const viewport = firstPage.getViewport({ scale: 1.0 });
      
      // Ajustar escala dependendo se é mobile ou desktop
      const scaleW = containerWidth / ((isCover || isMobile) ? 1 : 2) / viewport.width;
      const scaleH = containerHeight / viewport.height;
      scale = Math.min(scaleW, scaleH, 1.5);
      
      const scaledViewport = firstPage.getViewport({ scale });
      
      const pageWrapper = document.createElement('div');
      pageWrapper.className = 'page-wrapper';
      
      const pages = [];
      
      if (isCover) {
        // Página de capa (primeira página)
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
      } else if (isMobile) {
        // No mobile, mostrar apenas uma página por vez
        const singleCanvas = document.createElement('canvas');
        singleCanvas.height = scaledViewport.height;
        singleCanvas.width = scaledViewport.width;
        singleCanvas.classList.add('page', 'single-page');
        
        await firstPage.render({
          canvasContext: singleCanvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;
        
        pages.push(singleCanvas);
        document.getElementById('currentPage').textContent = startPage.toString();
      } else {
        // No desktop, mostrar duas páginas lado a lado
        document.getElementById('currentPage').textContent = `${startPage}-${Math.min(startPage + 1, pdfDoc.numPages)}`;
        
        // Garantir que a página da esquerda seja sempre par e a da direita sempre ímpar
        // Se startPage for ímpar (exceto capa), ajustar para exibir a página par anterior à esquerda
        let leftPageNum = startPage;
        if (startPage > 1 && startPage % 2 !== 0) {
          leftPageNum = startPage - 1;
        }
        
        // Página esquerda (sempre número par, exceto para a capa)
        const leftPage = await pdfDoc.getPage(leftPageNum);
        const leftCanvas = document.createElement('canvas');
        leftCanvas.height = scaledViewport.height;
        leftCanvas.width = scaledViewport.width;
        leftCanvas.classList.add('page', 'left');
        leftCanvas.dataset.pageNum = leftPageNum.toString();
        
        await leftPage.render({
          canvasContext: leftCanvas.getContext('2d'),
          viewport: scaledViewport
        }).promise;
        
        pages.push(leftCanvas);
        
        // Página direita (sempre número ímpar, exceto quando leftPageNum é a última página)
        const rightPageNum = leftPageNum + 1;
        if (rightPageNum <= pdfDoc.numPages) {
          const rightPage = await pdfDoc.getPage(rightPageNum);
          const rightCanvas = document.createElement('canvas');
          rightCanvas.height = scaledViewport.height;
          rightCanvas.width = scaledViewport.width;
          rightCanvas.classList.add('page', 'right');
          rightCanvas.dataset.pageNum = rightPageNum.toString();
          
          await rightPage.render({
            canvasContext: rightCanvas.getContext('2d'),
            viewport: scaledViewport
          }).promise;
          
          pages.push(rightCanvas);
        }
        
        // Atualizar o pageNum para o valor correto (primeira página exibida)
        pageNum = leftPageNum;
      }
      
      // Ajustar o gap entre as páginas dependendo do dispositivo
      if (!isMobile && !isCover && pages.length > 1) {
        pageWrapper.style.gap = '40px';
      } else {
        pageWrapper.style.gap = '0';
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
      prevButton.disabled = pageNum <= 1;
      nextButton.disabled = pageNum >= pdfDoc.numPages || (pages.length > 1 && parseInt(pages[pages.length-1].dataset.pageNum) >= pdfDoc.numPages);
      
      pageRendering = false;
      
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
    // Prevenir múltiplas chamadas durante animação
    if (pageRendering || document.querySelector('.page-wrapper.turning')) {
        return;
    }

    const currentWrapper = document.querySelector('.page-wrapper');
    if (!currentWrapper) return;

    // Verificar se é dispositivo móvel
    const isMobile = window.innerWidth < 768;
    
    // Gerenciamento especial para a capa
    if (pageNum === 1 && direction === 'next') {
        pageNum = 2;
        await renderPages(pageNum);
        return;
    }
    
    if (pageNum === 2 && direction === 'prev') {
        pageNum = 1;
        await renderPages(pageNum);
        return;
    }

    // Comportamento mobile
    if (isMobile) {
        if (direction === 'next' && pageNum < pdfDoc.numPages) {
            pageNum++;
        } else if (direction === 'prev' && pageNum > 1) {
            pageNum--;
        }
        await renderPages(pageNum);
        return;
    }

    // Comportamento desktop com animação
    const isForward = direction === 'next';
    const animationClass = isForward ? 'turning-forward' : 'turning-backward';
    
    // Calcular próxima página
    const nextPageNum = isForward ? 
        Math.min(pageNum + 2, pdfDoc.numPages) : 
        Math.max(pageNum - 2, 1);

    // Verificar se podemos avançar/retroceder
    if (nextPageNum === pageNum) return;

    // Iniciar animação
    currentWrapper.classList.add('turning', animationClass);

    // Usar Promise para garantir sequência correta
    await new Promise(resolve => setTimeout(resolve, 350));
    
    // Atualizar página e renderizar
    pageNum = nextPageNum;
    await renderPages(pageNum);

    // Remover classes de animação após renderização completa
    await new Promise(resolve => setTimeout(resolve, 50));
    currentWrapper.classList.remove('turning', animationClass);
  }

  document.getElementById('prevPage').addEventListener('click', () => {
    if (pageNum <= 1) return;
    turnPages('prev');
  });

  document.getElementById('nextPage').addEventListener('click', () => {
    if (pageNum >= pdfDoc.numPages) return;
    turnPages('next');
  });

  document.getElementById('pdfModal').addEventListener('hidden.bs.modal', () => {
    const pdfViewer = document.getElementById('pdfViewer');
    // Limpeza completa de recursos
    const canvases = pdfViewer.querySelectorAll('canvas');
    canvases.forEach(canvas => {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        canvas.width = canvas.height = 0;
    });
    pdfViewer.innerHTML = '';
    pdfDoc = null;
    pageNum = 1;
    pageRendering = false;
    pageNumPending = null;
  });

  let resizeTimeout;
  window.addEventListener('resize', () => {
    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
    resizeTimeout = setTimeout(async () => {
        if (!pdfDoc) return;
        
        const wasMobile = document.querySelector('.page.single-page') !== null;
        const isMobile = window.innerWidth < 768;
        
        // Recarregar apenas se houver mudança de modo
        if (wasMobile !== isMobile) {
            await renderPages(pageNum);
        }
    }, 250);
  });

  document.addEventListener('DOMContentLoaded', function() {
    const temaFilter = document.getElementById('temaFilter');
    const obras = document.querySelectorAll('.col-lg-3.col-md-6[data-tema]');
    const mensagemSemResultados = document.getElementById('semResultados');

    if (temaFilter) {
        temaFilter.addEventListener('change', function() {
            const selectedTema = this.value;
            let encontrouObras = false;

            obras.forEach(function(obra) {
                const temaDaObra = obra.getAttribute('data-tema');
                
                if (selectedTema === '' || temaDaObra === selectedTema) {
                    obra.style.display = '';
                    encontrouObras = true;
                } else {
                    obra.style.display = 'none';
                }
            });

            // Mostrar mensagem se nenhuma obra for encontrada
            if (mensagemSemResultados) {
                mensagemSemResultados.style.display = encontrouObras ? 'none' : 'block';
            }
        });
    }
});

function filtrarPorTema() {
    var temaSelecionado = document.getElementById('temaFilter').value;
    window.location.href = "obras.php?tema=" + temaSelecionado;
}
</script>

</body>

</html>