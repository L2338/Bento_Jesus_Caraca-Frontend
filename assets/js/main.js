/**
* Template Name: Mentor
* Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
* 
* Adaptado para Biblioteca Digital Bento de Jesus Caraça
* Este arquivo contém todas as funcionalidades JavaScript do site
*/

(function() {
  "use strict";

  /**
   * Controle de Scroll
   * Adiciona a classe .scrolled ao body quando a página é rolada
   * Isso permite efeitos visuais como mudança de cor do header
   * @param {void}
   * @returns {void}
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    // Verifica se o header tem as classes necessárias para o efeito
    if (!selectHeader.classList.contains('scroll-up-sticky') && 
        !selectHeader.classList.contains('sticky-top') && 
        !selectHeader.classList.contains('fixed-top')) return;
    // Adiciona ou remove a classe com base na posição do scroll
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  // Event listeners para scroll e carregamento da página
  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  
  /**
   * Toggle do Menu Mobile
   * Controla a exibição do menu em dispositivos móveis
   * Alterna entre ícones de menu e fechar
   * @param {void}
   * @returns {void}
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Fechamento do Menu Mobile
   * Fecha o menu quando um link é clicado
   * Melhora a experiência do usuário em mobile
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });
  });

  /**
   * Dropdowns do Menu Mobile
   * Controla os submenus no modo mobile
   * Previne propagação de eventos para evitar conflitos
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   * Remove o preloader quando a página carrega completamente
   * Melhora a percepção de carregamento
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Botão Scroll Top
   * Controla a visibilidade e funcionalidade do botão "voltar ao topo"
   * Implementa rolagem suave ao topo
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animações AOS
   * Inicializa a biblioteca de animações on-scroll
   * Configura duração, easing e comportamento
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Lightbox
   * Inicializa o GLightbox para galerias de imagens
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Contador
   * Inicializa o Pure Counter para números animados
   */
  new PureCounter();

  /**
   * Sliders Swiper
   * Inicializa e configura todos os sliders do site
   * Suporta configurações personalizadas via JSON
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

})();
