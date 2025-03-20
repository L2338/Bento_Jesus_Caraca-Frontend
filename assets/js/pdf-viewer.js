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

function queueRenderPages(num) {
  if (pageRendering) {
    pageNumPending = num;
  } else {
    renderPages(num);
  }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
  // Prevenir arraste de imagens
  const images = document.querySelectorAll('.course-item img');
  images.forEach(function(img) {
    img.addEventListener('dragstart', function(e) {
      e.preventDefault();
    });
    img.addEventListener('mousedown', function(e) {
      e.preventDefault();
    });
  });

  // Botões de navegação do PDF
  document.getElementById('prevPage').addEventListener('click', () => {
    if (pageNum <= 1) return;
    turnPages('prev');
  });

  document.getElementById('nextPage').addEventListener('click', () => {
    if (pageNum >= pdfDoc.numPages) return;
    turnPages('next');
  });

  // Limpar o visualizador quando o modal for fechado
  document.getElementById('pdfModal').addEventListener('hidden.bs.modal', () => {
    const pdfViewer = document.getElementById('pdfViewer');
    pdfViewer.innerHTML = '';
    pdfDoc = null;
    pageNum = 1;
  });

  // Redimensionamento da janela
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