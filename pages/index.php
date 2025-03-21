<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Início</title>
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

<body class="index-page">

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

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <img src="assets/img/hero-bn.jpg" alt="" data-aos="fade-in">

      <div class="container">
        <h2 data-aos="fade-up" data-aos-delay="100">Conhecimento,<br> Liberdade e Transformação</h2>
        <p data-aos="fade-up" data-aos-delay="200">Bento de Jesus Caraça deixou um legado inestimável para a educação e cultura. <br>Explore sua história e impacto.</p>
        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
          <a href="courses.html" class="btn-get-started">Saber Mais</a>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Legado Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/about2.jpg" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h2>O Legado de Bento de Jesus Caraça</h2>
            <p class="fst-italic">
             Conhecimento para Todos, Transformação para o Futuro.
            </p>
            <ul>
              <li><i class="bi bi-check-circle"></i> <span>Defensor incansável da educação e da cultura, acreditava no poder do conhecimento para transformar vidas.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Autor de obras fundamentais, foi responsável por democratizar o acesso à ciência e à matemática em Portugal.</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Criou a <strong>Biblioteca Cosmos</strong>, que distribuiu quase <strong>800.000 exemplares</strong>, tornando o saber acessível a milhares de leitores.</span></li>
            </ul>
            <a href="#" class="read-more"><span>Saber Mais</span><i class="bi bi-arrow-right"></i></a>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Counts Section -->
    <section id="counts" class="section counts light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="114" data-purecounter-duration="1" class="purecounter"></span>
              <p>Títulos Publicados</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="793500" data-purecounter-duration="1" class="purecounter"></span>
              <p> Exemplares Distribuídos</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="9" data-purecounter-duration="1" class="purecounter"></span>
              <p>Livros Publicados</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="2" data-purecounter-duration="1" class="purecounter"></span>
              <p>Condecorações</p>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Counts Section -->

    <!-- Why Us Section -->
    <section id="why-us" class="section why-us">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="why-box">
              <h3>Por que Bento de Jesus Caraça é Importante?</h3>
              <p>
                Matemático, professor e intelectual português, Bento de Jesus Caraça destacou-se pelo seu contributo para a democratização do conhecimento e pela defesa da educação como pilar fundamental da sociedade. 
                A sua obra e pensamento continuam a influenciar gerações.
              </p>
              <div class="text-center">
                <a href="legado.php" class="more-btn"><span>Explorar Legado</span> <i class="bi bi-chevron-right"></i></a>
              </div>
            </div>
          </div><!-- End Why Box -->

          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">

              <div class="col-xl-4">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-mortarboard-fill"></i>
                  <h4>Educação e Cultura</h4>
                  <p>Defensor da educação acessível, acreditava no ensino como motor de transformação social.</p>
                </div>
              </div><!-- End Icon Box -->

              <div class="col-xl-4" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-book-half"></i>
                  <h4>Biblioteca Cosmos</h4>
                  <p>Criou a Biblioteca Cosmos, que distribuiu 800.000 exemplares de livros educativos em Portugal</p>
                </div>
              </div><!-- End Icon Box -->

              <div class="col-xl-4" data-aos="fade-up" data-aos-delay="400">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-subscript"></i>
                  <h4>Matemática e Ciência</h4>
                  <p>Foi um dos principais divulgadores da matemática em Portugal, publicando obras de referência.</p>
                </div>
              </div><!-- End Icon Box -->

            </div>

          </div>

        </div>

      </div>
    </section>

    <section id="escolas" class="section">
      <div class="card-deck">
        <div class="section-title text-center">
        <h2>As nossas escolas profissionais</h2>
        <p>Descubra onde estamos presentes.</p>
      </div>

      <div class="row gy-4 justify-content-center">

      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
        <div class="card escola-card">
          <div class="card-header barreiro">
            <h3>BARREIRO</h3>
          </div>
          <div class="card-body">
            <p class="morada">
              <a href="#" target="_blank">Rua Stinville, nº14, Bairro Santa Bárbara, 2830-144 Barreiro</a>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> 212 064 790</p>
            <p>geral.barreiro@epbjc.pt</p>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
        <div class="card escola-card">
          <div class="card-header porto">
            <h3>PORTO</h3>
          </div>
          <div class="card-body">
            <p class="morada">
              <a href="#" target="_blank">Rua do Bonjardim, nº 497-1º, 4000 – 126 Porto</a>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> 222 054 713</p>
            <p>geral.porto@epbjc.pt</p>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="300">
        <div class="card escola-card">
          <div class="card-header beja">
            <h3>BEJA</h3>
          </div>
          <div class="card-body">
            <p class="morada">
              <a href="#" target="_blank">Rua D. Manuel I, nº19, 1º, 7800-306 Beja</a>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> 213 255 326</p>
            <p>geral.beja@epbjc.pt</p>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="400">
        <div class="card escola-card">
          <div class="card-header lisboa">
            <h3>LISBOA</h3>
          </div>
          <div class="card-body">
            <p class="morada">
              <a href="#" target="_blank">Rua Vitor Cordon, Nº1 – 1º, 1200-482 Lisboa</a>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> 212 064 790</p>
            <p>geral.lisboa@epbjc.pt</p>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="500">
        <div class="card escola-card">
          <div class="card-header seixal">
            <h3>SEIXAL</h3>
          </div>
          <div class="card-body">
            <p class="morada">
              <a href="#" target="_blank">Rua Júlio Augusto Henriques, nº 53, Arrentela, 2840-212 Seixal</a>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> 212 064 790</p>
            <p>geral.seixal@epbjc.pt</p>
          </div>
        </div>
      </div>

      </div>

      <p style="text-align:center; margin-top: 20px;">* Chamada para a rede fixa nacional</p>
      </div>
      
    </section>

    <!-- Courses Section -->
    <section id="courses" class="courses section">

     <!-- Título da Seção -->
      <div class="container section-title" data-aos="fade-up">
      <h2>Cursos</h2>
      <p>Cursos Profissionais</p>
      </div><!-- End Section Title -->

      <div class="container">

      <div class="row">

      <!-- Curso 1 - Gestão e Programação de Sistemas Informáticos -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
        <div class="course-item">
          <img src="assets/img/curso1.jpg" class="img-fluid" alt="...">
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <p class="category">Gestão e Programação de Sistemas Informáticos</p>
            </div>

            <h3>Desenvolve, otimiza e protege sistemas digitais</h3>
            <p class="description">
              Torna-te especialista na criação e gestão de sistemas informáticos. Aprende sobre linguagens de programação, bases de dados e segurança cibernética, garantindo uma carreira sólida na tecnologia.
            </p>

            <!-- Estrelas de Avaliação e Tempo -->
            <div class="d-flex align-items-center justify-content-between">
              <div class="rating">
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-half text-warning"></i> (4.7)
              </div>
              <div class="duration">
                <i class="bi bi-clock"></i> 3 anos
              </div>
            </div>

            <div class="trainer d-flex justify-content-between align-items-center mt-3">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-1-2.jpg" class="img-fluid" alt="">
                <a href="" class="trainer-link">Saber mais</a>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End Course Item -->

      <!-- Curso 2 - Gestão de Comunicação, Marketing e Publicidade -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
        <div class="course-item">
          <img src="assets/img/curso2.jpg" class="img-fluid" alt="...">
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <p class="category">Gestão de Comunicação, Marketing e Publicidade</p>
            </div>

            <h3>Domina estratégias de comunicação e marketing digital</h3>
            <p class="description">
              Aprende a criar campanhas eficazes, gerenciar redes sociais e otimizar a presença digital de marcas. Inclui módulos de SEO, branding e análise de mercado.
            </p>

            <!-- Estrelas de Avaliação e Tempo -->
            <div class="d-flex align-items-center justify-content-between">
              <div class="rating">
              <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-half text-warning"></i>(4.5)
              </div>
              <div class="duration">
                <i class="bi bi-clock"></i> 3 anos
              </div>
            </div>

            <div class="trainer d-flex justify-content-between align-items-center mt-3">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-2-2.jpg" class="img-fluid" alt="">
                <a href="" class="trainer-link">Saber Mais</a>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End Course Item -->

      <!-- Curso 3 - Técnica de Gestão de Equipamento Informático -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in" data-aos-delay="300">
        <div class="course-item">
          <img src="assets/img/curso3.png" class="img-fluid" alt="...">
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <p class="category">Técnica de Gestão de Equipamentos Informáticos</p>
            </div>

            <h3>Mantém e otimiza infraestruturas tecnológicas</h3>
            <p class="description">
              Aprende a instalar, reparar e configurar equipamentos informáticos e redes. O curso inclui formação em diagnóstico de falhas, segurança digital e suporte técnico, essencial para qualquer empresa.
            </p>

            <!-- Estrelas de Avaliação e Tempo -->
            <div class="d-flex align-items-center justify-content-between">
              <div class="rating">
              <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-fill text-warning"></i>
                <i class="bi bi-star-half text-warning"></i> (4.5)
              </div>
              <div class="duration">
                <i class="bi bi-clock"></i> 3 anos
              </div>
            </div>

            <div class="trainer d-flex justify-content-between align-items-center mt-3">
              <div class="trainer-profile d-flex align-items-center">
                <img src="assets/img/trainers/trainer-3-2.jpg" class="img-fluid" alt="">
                <a href="" class="trainer-link">Saber Mais</a>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End Course Item -->
    </div>
  </section><!-- /Courses Section -->

    
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