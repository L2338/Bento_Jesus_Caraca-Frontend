<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';

// Buscar informações de contato
$query_contato = "SELECT tipo, valor, chave FROM informacoes_contato";
$result_contato = mysqli_query($conn, $query_contato);
$contatos = array();

if ($result_contato && mysqli_num_rows($result_contato) > 0) {
    while ($row = mysqli_fetch_assoc($result_contato)) {
        $contatos[$row['chave']] = $row['valor'];
    }
}

// Buscar redes sociais
$query_social = "SELECT nome, url, icone FROM redes_sociais WHERE ativo = 1 ORDER BY ordem ASC";
$result_social = mysqli_query($conn, $query_social);
$redes_sociais = array();

if ($result_social && mysqli_num_rows($result_social) > 0) {
    while ($row = mysqli_fetch_assoc($result_social)) {
        $redes_sociais[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Início</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link rel="icon" href="assets/img/favicon.png" type="image/png">
  <link rel="shortcut icon" href="assets/img/favicon.png">
  <meta name="theme-color" content="#ac062a">

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

      <img src="assets/img/index/hero-bn.jpg" alt="" data-aos="fade-in">

      <div class="container">
        <h2 data-aos="fade-up" data-aos-delay="100">Conhecimento,<br> Liberdade e Transformação</h2>
        <p data-aos="fade-up" data-aos-delay="200">Bento de Jesus Caraça deixou um legado inestimável para a educação e cultura. <br>Explore sua história e impacto.</p>
        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
          <a href="vida.php" class="btn-get-started">Saber Mais</a>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Legado Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/index/about2.jpg" class="img-fluid" alt="">
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
            <a href="vida.php" class="read-more"><span>Saber Mais</span><i class="bi bi-arrow-right"></i></a>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Counts Section -->
    <!-- Counts Section -->
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

    <!-- ======= Professional Schools Section ======= -->
    <section id="escolas" class="section">
      <?php
      // Verificar se a coluna publicada_site existe
      $checkColumnExists = "SHOW COLUMNS FROM escolas_profissionais LIKE 'publicada_site'";
      $columnResult = mysqli_query($conn, $checkColumnExists);
      $columnExists = (mysqli_num_rows($columnResult) > 0);
      
      if (!$columnExists) {
          // Se a coluna não existir, adicionar automaticamente
          $alterTableQuery = "ALTER TABLE escolas_profissionais ADD COLUMN publicada_site TINYINT(1) DEFAULT 0";
          mysqli_query($conn, $alterTableQuery);
          
          // Fornecer feedback no log para administradores
          error_log("Coluna publicada_site adicionada automaticamente à tabela escolas_profissionais");
      }
      
      // Buscar escolas publicadas no site
      $query = "SELECT id, nome, cidade, endereco, telefone, email, website, ordem 
                FROM escolas_profissionais 
                WHERE publicada_site = 1 AND ativa = 1 
                ORDER BY ordem ASC";
      $result = mysqli_query($conn, $query);
      $escolas = array();
      
      if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              $escolas[] = $row;
          }
      }
      ?>
      <div class="card-deck">
        <div class="section-title text-center">
        <h2>As escolas profissionais com o nome de Bento de Jesus Caraça</h2>
        <p>Descubra onde estão presentes.</p>
      </div>

      <div class="row gy-4 justify-content-center">
      
      <?php
      // Definir classes CSS para os diferentes locais
      $cssClasses = array(
          'Barreiro' => 'barreiro',
          'Porto' => 'porto',
          'Beja' => 'beja',
          'Lisboa' => 'lisboa',
          'Seixal' => 'seixal'
      );
      
      // Definir delays de animação
      $delays = array(100, 200, 300, 400, 500);
      
      // Contador para os delays
      $count = 0;
      
      // Loop através das escolas
      foreach ($escolas as $escola):
          $cidade = $escola['cidade'];
          $cssClass = isset($cssClasses[$cidade]) ? $cssClasses[$cidade] : '';
          $delay = $delays[$count % count($delays)];
          $count++;
      ?>
      <div class="col-lg-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
        <div class="card escola-card">
          <div class="card-header <?php echo $cssClass; ?>">
            <h3><?php echo strtoupper($cidade); ?></h3>
          </div>
          <div class="card-body">
            <p class="morada">

              <?php if (!empty($escola['website'])): ?>
                <a href="<?php echo htmlspecialchars($escola['website']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($escola['endereco']); ?></a>
              <?php else: ?>
                <a href="#" target="_blank"><?php echo htmlspecialchars($escola['endereco']); ?></a>
              <?php endif; ?>
            </p>
            <br>
            <br>
            <br>
            <p><strong>Tel:</strong> <?php echo htmlspecialchars($escola['telefone']); ?></p>
            <p><?php echo htmlspecialchars($escola['email']); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (empty($escolas)): ?>
      <div class="col-12 text-center">
        <p>Não há escolas disponíveis para exibição no momento. Por favor, volte mais tarde.</p>
      </div>
      <?php endif; ?>

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

      <?php
      // Buscar cursos do banco de dados
      $query_cursos = "SELECT * FROM cursos WHERE ativo = 1 ORDER BY ordem ASC";
      $result_cursos = mysqli_query($conn, $query_cursos);
      
      if ($result_cursos && mysqli_num_rows($result_cursos) > 0) {
          $delay = 100;
          while ($curso = mysqli_fetch_assoc($result_cursos)) {
              // Calcular número de estrelas cheias baseado na avaliação
              $avaliacao = floatval($curso['avaliacao']);
              $estrelas_cheias = floor($avaliacao);
              $estrela_meia = ($avaliacao - $estrelas_cheias) >= 0.5 ? 1 : 0;
              $estrelas_vazias = 5 - $estrelas_cheias - $estrela_meia;
      ?>
      <!-- Curso Item -->
      <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
        <div class="course-item">
          <img src="<?php echo htmlspecialchars($curso['imagem']); ?>" class="img-fluid" alt="Imagem do curso <?php echo htmlspecialchars($curso['titulo']); ?>">
          <div class="course-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <p class="category"><?php echo htmlspecialchars($curso['categoria']); ?></p>
            </div>

            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
            <p class="description">
              <?php echo htmlspecialchars($curso['descricao']); ?>
            </p>

            <!-- Estrelas de Avaliação e Tempo -->
            <div class="d-flex align-items-center justify-content-between">
              <div class="rating">
                <?php 
                // Estrelas cheias
                for ($i = 0; $i < $estrelas_cheias; $i++) {
                    echo '<i class="bi bi-star-fill text-warning"></i>';
                }
                // Estrela meia
                if ($estrela_meia) {
                    echo '<i class="bi bi-star-half text-warning"></i>';
                }
                // Estrelas vazias
                for ($i = 0; $i < $estrelas_vazias; $i++) {
                    echo '<i class="bi bi-star text-warning"></i>';
                }
                ?>
                (<?php echo number_format($avaliacao, 1); ?>)
              </div>
              <div class="duration">
                <i class="bi bi-clock"></i> <?php echo htmlspecialchars($curso['duracao']); ?>
              </div>
            </div>

            <div class="trainer d-flex justify-content-between align-items-center mt-3">
              <div class="trainer-profile d-flex align-items-center">
                <a href="<?php echo htmlspecialchars($curso['link']); ?>" class="btn-sm btn-primary mt-2" target="_blank">Saber mais</a>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End Course Item -->
      <?php
              $delay += 100;
          }
      } else {
      ?>
      <!-- Se não houver cursos cadastrados -->
      <div class="col-12 text-center">
        <p>Não há cursos disponíveis para exibição no momento. Por favor, volte mais tarde.</p>
      </div>
      <?php } ?>
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