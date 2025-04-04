<?php
include('ConfigBD.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Retratos</title>
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

  <style>       
    .carousel-container {
        max-width: 350px; 
        margin: 0 auto; 
        max-height: 500px; 
        overflow: hidden;
    }
    .carousel-item img {
        height: 500px; 
        object-fit: contain;
        width: 350px;
    }

    @media (max-width: 768px) {
        .carousel-item img {
            height: 300px; 
        }
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color:#ac062a;
        border-radius: 50%;
        padding: 10px;
    }

    h2 {
    margin-top: -9%; 
    }

  </style>

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="starter-page-page">

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

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

    <div class="carouser-container">
    <div class="container mt-5">
        <h2 class="mb-4" >Galeria de Imagens</h2>
        
        <!-- Carousel/Slide de Imagens -->
        <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
            
            <!-- Indicadores -->
            <div class="carousel-indicators">
                <?php
                  
                // Buscar imagens da base de dados
                $sql = "SELECT id_imagem, imagem, descricao FROM imagens where id_tema_imagem=2 ORDER BY id_imagem";
                $result = $conn->query($sql);
                
                $totalImagens = $result->num_rows;
                
                // Gerar indicadores
                for ($i = 0; $i < $totalImagens; $i++) {
                    $active = ($i == 0) ? "active" : "";
                    echo '<button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="'.$i.'" class="'.$active.'"></button>';
                }
                ?>
            </div>
            
            <!-- Slides -->
            <div class="carousel-inner">
                <?php
                // Resetar o ponteiro do resultado
                $result->data_seek(0);
                $descricoes=[];
                $contador = 0;
                while ($row = $result->fetch_assoc()) {
                    $active = ($contador == 0) ? "active" : "";
                    echo '
                    <div class="carousel-item '.$active.'">
                        <img src="assets/img/amigos/'.$row["imagem"].'" class="d-block w-100" alt="'.$row["descricao"].'">                        
                    </div>';

                    $descricoes[]=$row["descricao"];
                    $contador++;
                }
                
                ?>
            </div>
            <br>
            <h5 style="text-align:center;" id="descricao">
                <?php                
                    echo $descricoes[0] ?? '';
                ?>
            </h5>
            
            
            <!-- Controles de Navegação -->
            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>
    </div>
    </div>

    </section><!-- /Starter Section Section -->

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
        // Inicializar o carrossel com opções personalizadas
        document.addEventListener('DOMContentLoaded', function() {
            var myCarousel = new bootstrap.Carousel(document.getElementById('imageCarousel'), {
                interval: 3000, // Tempo entre slides em ms
                wrap: true,     // Ciclo contínuo
                keyboard: true  // Controle pelo teclado
            });
            
            // Exemplo de evento para quando um slide muda
            document.getElementById('imageCarousel').addEventListener('slide.bs.carousel', function (e) {
                // Código para executar quando o slide muda
                console.log('Mudando para o slide: ' + e.to);
            });
            var carousel = document.getElementById('imageCarousel'); 
            var descricao = document.getElementById("descricao");
            var descricoes = <?php echo json_encode($descricoes); ?>;

            carousel.addEventListener("slid.bs.carousel", function (event) {
                var activeItem = event.relatedTarget; // Obtém o item ativo do carrossel
                var index = Array.from(activeItem.parentNode.children).indexOf(activeItem); // Encontra o índice
                descricao.textContent = descricoes[index]; // Atualiza a descrição
            });

        });
  </script>

</body>

</html>