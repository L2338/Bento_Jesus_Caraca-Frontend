<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';

// Consultar os blocos da timeline que estão ativos
$query = "SELECT * FROM timeline_blocos WHERE ativo = 1 ORDER BY ordem ASC";
$result = mysqli_query($conn, $query);
$timeline_blocks = [];

// Verificar se há resultados
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $timeline_blocks[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Vida e Obra - Bento de Jesus Caraça</title>
  <meta content="Página dedicada à vida e obra de Bento de Jesus Caraça" name="description">
  <meta content="Bento de Jesus Caraça, matemática, Portugal, ciência, cultura" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/BJC_logo.png" rel="icon">
  <link href="assets/img/BJC_logo.png" rel="apple-touch-icon">

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
</head>
<body class="about-page">

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
              <h1>Vida e Obra<br></h1>
              <p class="mb-0">Pode aceder aqui a informações detalhadas sobre a vida e obra de Bento de Jesus Caraça. 
                Figura marcante da sua época, destacou-se pelo seu contributo ímpar enquanto intelectual, educador e cidadão empenhado. 
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs">
        <div class="container">
          <ol>
            <li><a href="index.php">Início</a></li>
            <li class="current">Vida e Obra<br></li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

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

    <!-- Timeline Section -->
    <section id="timeline" class="timeline section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Linha do Tempo</h2>
        <p>Sua Jornada</p>
      </div>

      <div class="container">
        <p class="section-description" data-aos="fade-up" data-aos-delay="100">
          Bento de Jesus Caraça (1901-1948) foi um matemático, professor, pensador e ativista português cuja vida e obra deixaram um legado duradouro na educação, matemática e na luta pela democratização da cultura em Portugal. Nascido em uma família humilde, alcançou os mais altos patamares acadêmicos por seu brilhantismo intelectual, tornando-se Professor Catedrático aos 28 anos. Sua vida foi marcada pela dedicação incansável à democratização do conhecimento e pela oposição ao regime salazarista, o que lhe custou a carreira universitária e, possivelmente, contribuiu para sua morte prematura aos 47 anos. A cronologia a seguir apresenta os momentos mais significativos de sua notável trajetória.
        </p>
        <div class="timeline-container" data-aos="fade-up" data-aos-delay="100">
          <?php 
          if (!empty($timeline_blocks)) {
              $count = 0;
              foreach ($timeline_blocks as $block) {
                  $count++;
                  $position = ($count % 2 == 0) ? 'left-item' : 'right-item';
                  $blockId = 'more-' . $block['id'];
          ?>
          <!-- <?php echo $block['titulo']; ?> -->
          <div class="timeline-item <?php echo $position; ?>">
            <div class="timeline-image">
              <img class="rounded-circle img-fluid" src="<?php echo $block['imagem']; ?>" alt="<?php echo $block['titulo']; ?>">
            </div>
            <span class="timeline-date"><?php echo $block['data_periodo']; ?></span>
            <div class="timeline-content">
              <h3><?php echo $block['titulo']; ?></h3>
              <p><?php echo $block['conteudo']; ?></p>
              <?php if (!empty($block['conteudo_expandido'])) { ?>
              <button class="btn-more" data-bs-toggle="collapse" data-bs-target="#<?php echo $blockId; ?>">Saiba mais</button>
              <div id="<?php echo $blockId; ?>" class="collapse timeline-more">
                <?php 
                // Dividir o conteúdo expandido em parágrafos
                $paragraphs = explode("\n\n", $block['conteudo_expandido']);
                foreach ($paragraphs as $paragraph) {
                    if (!empty(trim($paragraph))) {
                        echo "<p>" . trim($paragraph) . "</p>";
                    }
                }
                ?>
              </div>
              <?php } ?>
            </div>
          </div>
          <?php 
              }
          } else {
          ?>
          <div class="alert alert-info">
            Nenhum bloco de timeline encontrado.
          </div>
          <?php 
          }
          ?>
        </div>
      </div>

    </section><!-- /Timeline Section -->

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

  <!-- Main File -->
  <script src="assets/js/main.js"></script>

</body>
</html> 