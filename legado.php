<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';

// Funções para obter dados

// Função para obter e exibir condecorações
function obterCondecoracoes($conn) {
    $output = '<div class="row" data-aos="fade-up">';
    
    // Verifica se há termo de pesquisa
    $where = "";
    if (isset($_GET["search_condecoracoes"]) && !empty($_GET["search_condecoracoes"])) {
        $termo = $conn->real_escape_string($_GET["search_condecoracoes"]);
        $where = "WHERE titulo LIKE '%$termo%' OR descricao LIKE '%$termo%'";
    }
    
    // Consulta SQL
    $sql = "SELECT id, titulo, DATE_FORMAT(data, '%d/%m/%Y') as data_formatada, descricao FROM condecoracoes $where ORDER BY data DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Loop através dos resultados
        while ($row = $result->fetch_assoc()) {
            $output .= '<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">';
            $output .= '<div class="card border-0 h-100 shadow-sm">';
            $output .= '<div class="card-body">';
            $output .= '<div class="d-flex align-items-center mb-3">';
            $output .= '<i class="bi bi-award text-primary me-2" style="font-size: 1.5rem; color: var(--accent-color) !important;"></i>';
            $output .= '<h4 class="card-title mb-0">' . htmlspecialchars($row["titulo"]) . '</h4>';
            $output .= '</div>';
            $output .= '<p class="card-date mb-3"><i class="bi bi-calendar-event me-2"></i>' . htmlspecialchars($row["data_formatada"]) . '</p>';
            $output .= '<p class="card-text">' . htmlspecialchars($row["descricao"]) . '</p>';
            $output .= '</div></div></div>';
        }
    } else {
        $output = '<div class="col-12 text-center"><p class="no-results">Nenhuma condecoração encontrada.</p></div>';
    }
    
    $output .= '</div>';
    return $output;
}

// Função para obter e exibir monumentos
function obterMonumentos($conn) {
    $output = '<div class="row gallery-container" data-aos="fade-up">';
    
    // Verifica se há termo de pesquisa
    $where = "";
    if (isset($_GET["search_monumentos"]) && !empty($_GET["search_monumentos"])) {
        $termo = $conn->real_escape_string($_GET["search_monumentos"]);
        $where = "WHERE nome LIKE '%$termo%' OR local LIKE '%$termo%' OR descricao LIKE '%$termo%'";
    }
    
    // Consulta SQL
    $sql = "SELECT id, nome, local, descricao, imagem FROM monumentos $where ORDER BY id ";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Loop através dos resultados
        while ($row = $result->fetch_assoc()) {
            $output .= '<div class="col-lg-4 col-md-6 mb-5" data-aos="zoom-in" data-aos-delay="150">';
            $output .= '<div class="card monumento-card border-0 h-100 shadow-sm overflow-hidden" style="border-radius: 10px;">';
            
            // Container de imagem com proporção fixa e estilo avançado
            $output .= '<div class="monumento-img-container">';
            
            // Imagem (usar placeholder se não houver imagem)
            if (!empty($row["imagem"]) && file_exists($row["imagem"])) {
                $output .= '<img src="' . htmlspecialchars($row["imagem"]) . '" alt="' . htmlspecialchars($row["nome"]) . '" class="monumento-img">';
            } else {
                $output .= '<img src="assets/img/monumentos/monumento-placeholder.jpg" alt="' . htmlspecialchars($row["nome"]) . '" class="monumento-img">';
            }
            
            // Overlay com gradiente e ação de ampliação
            $output .= '<div class="monumento-overlay">';
            $output .= '<a href="' . (!empty($row["imagem"]) && file_exists($row["imagem"]) ? htmlspecialchars($row["imagem"]) : 'assets/img/monumentos/monumento-placeholder.jpg') . '" class="btn-ampliar" data-gallery="monumentos-gallery">';
            $output .= '<i class="bi bi-search"></i>';
            $output .= '</a>';
            $output .= '</div>';
            
            $output .= '</div>'; // fim monumento-img-container
            
            $output .= '<div class="card-body p-4">';
            $output .= '<h4 class="card-title fw-bold mb-2">' . htmlspecialchars($row["nome"]) . '</h4>';
            $output .= '<p class="card-text mb-3">' . htmlspecialchars($row["descricao"]) . '</p>';
            $output .= '<p class="location d-flex align-items-center mt-3">';
            $output .= '<i class="bi bi-geo-alt-fill me-2" style="color: var(--accent-color);"></i>';
            $output .= '<span>' . htmlspecialchars($row["local"]) . '</span>';
            $output .= '</p>';
            $output .= '</div></div></div>';
        }
    } else {
        $output = '<div class="col-12 text-center"><p class="no-results">Nenhum monumento encontrado.</p></div>';
    }
    
    $output .= '</div>';
    return $output;
}

// Função para obter e exibir toponímia
function obterToponimia($conn) {
    $output = '<div class="row toponimia-container" data-aos="fade-up">';
    
    // Verifica se há termo de pesquisa
    $where = "";
    if (isset($_GET["search_toponimia"]) && !empty($_GET["search_toponimia"])) {
        $termo = $conn->real_escape_string($_GET["search_toponimia"]);
        $where = "WHERE nome LIKE '%$termo%' OR cidade LIKE '%$termo%' OR categoria LIKE '%$termo%'";
    }
    
    // Consulta SQL agrupada por categoria
    $sql = "SELECT categoria, GROUP_CONCAT(CONCAT(nome, ' - ', cidade) SEPARATOR '||') as itens 
            FROM toponimia $where 
            GROUP BY categoria 
            ORDER BY categoria";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Loop através dos resultados agrupados por categoria
        while ($row = $result->fetch_assoc()) {
            $categoria = $row["categoria"];
            $categorias_nome = array(
                'rua' => 'Ruas',
                'avenida' => 'Avenidas',
                'praca' => 'Praças',
                'escola' => 'Escolas',
                'instituicao' => 'Instituições',
                'outro' => 'Outros'
            );
            
            $categorias_icone = array(
                'rua' => 'bi-signpost-2',
                'avenida' => 'bi-signpost',
                'praca' => 'bi-tree',
                'escola' => 'bi-building',
                'instituicao' => 'bi-bank',
                'outro' => 'bi-pin-map'
            );
            
            $titulo_categoria = isset($categorias_nome[$categoria]) ? $categorias_nome[$categoria] : ucfirst($categoria);
            $icone = isset($categorias_icone[$categoria]) ? $categorias_icone[$categoria] : 'bi-pin-map';
            
            $output .= '<div class="col-lg-4 col-md-6 mb-4">';
            $output .= '<div class="toponimia-card">';
            
            $output .= '<h3 class="toponimia-title"><i class="bi ' . $icone . ' me-2" aria-hidden="true"></i>' . $titulo_categoria . '</h3>';
            $output .= '<ul class="toponimia-list">';
            
            $itens = explode('||', $row["itens"]);
            foreach ($itens as $item) {
                $output .= '<li class="toponimia-item">' . htmlspecialchars($item) . '</li>';
            }
            
            $output .= '</ul>';
            $output .= '</div>'; // fim toponimia-card
            $output .= '</div>'; // fim col
        }
    } else {
        $output = '<div class="col-12"><p class="text-center">Nenhuma toponímia encontrada.</p></div>';
    }
    
    $output .= '</div>'; // fim row
    return $output;
}

// Define a aba ativa (obtém da URL ou define padrão)
$tab_ativa = isset($_GET['tab']) ? $_GET['tab'] : 'condecoracoes';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Legado | Bento de Jesus Caraça</title>
  <meta name="description" content="Legado de Bento de Jesus Caraça - condecorações, monumentos, toponímia e Biblioteca Cosmos">
  <meta name="keywords" content="Bento de Jesus Caraça, legado, condecorações, monumentos, toponímia, Biblioteca Cosmos">

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
  /* Estilos adicionais para a seção de toponímia */
  :root {
      --accent-color-rgb: 172, 6, 42; /* Versão RGB da cor de destaque para uso em rgba() */
  }

  /* Estilos para monumentos */
  .monumento-card {
      transition: all 0.3s ease;
      position: relative;
  }

  .monumento-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
  }

  .monumento-img-container {
      position: relative;
      height: 240px;
      overflow: hidden;
      margin: -1px;
      border-radius: 10px 10px 0 0;
  }

  .monumento-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: all 0.5s ease;
  }

  .monumento-card:hover .monumento-img {
      transform: scale(1.1);
  }

  .monumento-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 60%);
      opacity: 0;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
  }

  .monumento-card:hover .monumento-overlay {
      opacity: 1;
  }

  .btn-ampliar {
      background-color: rgba(var(--accent-color-rgb), 0.8);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.3s ease 0.1s;
  }

  .monumento-card:hover .btn-ampliar {
      transform: translateY(0);
      opacity: 1;
  }

  .btn-ampliar:hover {
      background-color: var(--accent-color);
      color: white;
  }

  .monumento-card .card-body {
      z-index: 1;
      position: relative;
  }

  .lista-toponimia {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
  }

  .item-toponimia {
      font-size: 0.95rem;
      color: #4a4a4a;
      transition: all 0.3s ease;
  }

  .item-toponimia:hover {
      color: var(--accent-color);
  }

  .item-toponimia:last-child {
      border-bottom: none !important;
  }

  /* Estilos para as abas */
  .nav-tabs .nav-link {
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
  }

  .nav-tabs .nav-link i {
      margin-right: 0.5rem;
  }

  .nav-tabs .nav-link:hover {
      background-color: rgba(var(--accent-color-rgb), 0.05);
  }

  /* Botão Ver Mais */
  .btn-ver-mais {
      background: transparent;
      color: var(--accent-color);
      border: 1px solid var(--accent-color);
      border-radius: 20px;
      padding: 6px 16px;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
  }

  .btn-ver-mais:hover {
      background: var(--accent-color);
      color: white;
      box-shadow: 0 3px 6px rgba(var(--accent-color-rgb), 0.2);
  }

  .btn-ver-mais .bi {
      transition: transform 0.3s ease;
  }

  .btn-ver-mais.active .bi {
      transform: rotate(180deg);
  }

  .collapse.show {
      display: block;
  }

  .itens-ocultos {
      overflow: hidden;
      transition: all 0.3s ease;
  }

  /* Estilos para a seção de toponímia */
  .toponimia-container {
      margin-top: 1.5rem;
  }

  .toponimia-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      padding: 1.5rem;
      height: 100%;
      transition: all 0.3s ease;
  }

  .toponimia-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .toponimia-title {
      color: var(--accent-color);
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
      border-bottom: 2px solid #f0f0f0;
      display: flex;
      align-items: center;
  }

  .toponimia-title i {
      color: var(--accent-color);
      font-size: 1.2rem;
  }

  .toponimia-list {
      list-style-type: none;
      padding-left: 0;
      margin-bottom: 0;
  }

  .toponimia-item {
      position: relative;
      padding: 0.5rem 0 0.5rem 1.5rem;
      border-bottom: 1px solid #f5f5f5;
      font-size: 0.95rem;
      transition: all 0.2s ease;
  }

  .toponimia-item:last-child {
      border-bottom: none;
  }

  .toponimia-item:before {
      content: "";
      position: absolute;
      left: 0;
      top: 15px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background-color: var(--accent-color);
  }

  .toponimia-item:hover {
      color: var(--accent-color);
      transform: translateX(5px);
  }

  @media (max-width: 768px) {
      .toponimia-card {
          margin-bottom: 1rem;
      }
  }
  </style>
</head>

<body class="legado-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <img src="assets/img/epbjc-logo.png" alt="Logo Epbjc" > 
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
              <h1>Legado</h1>
              <p class="mb-0">Aqui mostramos as várias formas como Bento de Jesus Caraça foi e é homenageado e o que é a Biblioteca Cosmos, criada pelo próprio.</p>
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs">
        <div class="container">
          <ol>
            <li><a href="index.php">Início</a></li>
            <li class="current">Legado</li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <section class="legado-section section">
        <div class="container">
            <?php if (!empty($status_message)): ?>
                <div class="alert alert-<?php echo $status_type == 'success' ? 'success' : 'danger'; ?> mb-4">
                    <?php echo $status_message; ?>
                </div>
            <?php endif; ?>

            <!-- Tabs navegação com Bootstrap -->
            <ul class="nav nav-tabs d-flex justify-content-center mb-5" id="legadoTabs" role="tablist" style="border-bottom: none;">
                <li class="nav-item" role="presentation">
                    <a href="?tab=condecoracoes" class="nav-link <?php echo $tab_ativa == 'condecoracoes' ? 'active' : ''; ?>" id="condecoracoes-tab" style="color: <?php echo $tab_ativa == 'condecoracoes' ? 'var(--accent-color)' : '#272828'; ?>; border: none;">
                        <i class="bi bi-award" style="color: var(--accent-color);"></i> Condecorações
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="?tab=monumentos" class="nav-link <?php echo $tab_ativa == 'monumentos' ? 'active' : ''; ?>" id="monumentos-tab" style="color: <?php echo $tab_ativa == 'monumentos' ? 'var(--accent-color)' : '#272828'; ?>; border: none;">
                        <i class="bi bi-building-fill" style="color: var(--accent-color);"></i> Monumentos
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="?tab=toponimia" class="nav-link <?php echo $tab_ativa == 'toponimia' ? 'active' : ''; ?>" id="toponimia-tab" style="color: <?php echo $tab_ativa == 'toponimia' ? 'var(--accent-color)' : '#272828'; ?>; border: none;">
                        <i class="bi bi-pin-map-fill" style="color: var(--accent-color);"></i> Toponímia
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="?tab=biblioteca" class="nav-link <?php echo $tab_ativa == 'biblioteca' ? 'active' : ''; ?>" id="biblioteca-tab" style="color: <?php echo $tab_ativa == 'biblioteca' ? 'var(--accent-color)' : '#272828'; ?>; border: none;">
                        <i class="bi bi-book-half" style="color: var(--accent-color);"></i> Biblioteca Cosmos
                    </a>
                </li>
            </ul>

            <!-- Conteúdo das abas -->
            <div class="tab-content" id="legadoTabsContent">
                <!-- Aba de Condecorações -->
                <div class="tab-pane fade <?php echo $tab_ativa == 'condecoracoes' ? 'show active' : ''; ?>" 
                     id="condecoracoes-content" 
                     role="tabpanel" 
                     aria-labelledby="condecoracoes-tab">
                    <div class="section-header" data-aos="fade-up">
                        <h2>Condecorações</h2>
                        <p>Honrarias e reconhecimentos concedidos a Bento de Jesus Caraça</p>
                    </div>
                    <?php echo obterCondecoracoes($conn); ?>
                </div>
                
                <!-- Aba de Monumentos -->
                <div class="tab-pane fade <?php echo $tab_ativa == 'monumentos' ? 'show active' : ''; ?>" 
                     id="monumentos-content" 
                     role="tabpanel" 
                     aria-labelledby="monumentos-tab">
                    <div class="section-header" data-aos="fade-up">
                        <h2>Monumentos</h2>
                        <p>Tributos em pedra e bronze à memória de Bento de Jesus Caraça</p>
                    </div>
                    <?php echo obterMonumentos($conn); ?>
                </div>
                
                <!-- Aba de Toponímia -->
                <div class="tab-pane fade <?php echo $tab_ativa == 'toponimia' ? 'show active' : ''; ?>" 
                     id="toponimia-content" 
                     role="tabpanel" 
                     aria-labelledby="toponimia-tab">
                    <div class="section-header" data-aos="fade-up">
                        <h2>Toponímia</h2>
                        <p>Lugares que levam o nome de Bento de Jesus Caraça</p>
                    </div>
                    <?php echo obterToponimia($conn); ?>
                </div>
                
                <!-- Aba da Biblioteca Cosmos -->
                <div class="tab-pane fade <?php echo $tab_ativa == 'biblioteca' ? 'show active' : ''; ?>" 
                     id="biblioteca-content" 
                     role="tabpanel" 
                     aria-labelledby="biblioteca-tab">
                    <div class="section-header" data-aos="fade-up">
                        <h2>Biblioteca Cosmos</h2>
                        <p>O legado literário que democratizou o conhecimento em Portugal</p>
                    </div> 

                    <div class="row align-items-center biblioteca-cosmos-section">
                        <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
                            <img src="assets/img/LogoCosmos.png" class="img-fluid rounded shadow-sm" alt="Logo Biblioteca Cosmos">
                        </div>

                        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                            <div class="content ps-lg-5">
                                <h3>Um projeto revolucionário que democratizou o saber em Portugal</h3>
                                <ul class="cosmos-features">
                                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--accent-color);"></i> <span>Fundada em <strong>1941</strong> por Bento de Jesus Caraça, a <strong>Biblioteca Cosmos</strong> foi uma das iniciativas editoriais mais ambiciosas da época.</span></li>
                                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--accent-color);"></i> <span>Com o objetivo de levar cultura e ciência ao povo, publicou mais de <strong>114 títulos</strong> em <strong>145 Volumes</strong> cobrindo temas como matemática, literatura, história, filosofia e ciências naturais.</span></li>
                                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--accent-color);"></i> <span>A coleção teve uma circulação massiva, distribuindo quase <strong>800.000 exemplares</strong> e tornando-se referência na divulgação do conhecimento.</span></li>
                                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--accent-color);"></i> <span>Mesmo enfrentando censura durante o Estado Novo, a Biblioteca Cosmos marcou gerações e influenciou o pensamento crítico em Portugal.</span></li>
                                </ul>
                                <div class="mt-4">
                                    <a href="http://www.bibliotecacosmos.com/" target="_blank" class="btn-saber-mais">
                                        Saber Mais
                                        <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <section id="counts" class="section counts light-background">

                        <div class="container" data-aos="fade-up" data-aos-delay="100">

                        <div class="row gy-4">

                            <?php
                            // Buscar estatísticas do banco de dados
                            $query_stats = "SELECT chave, valor, descricao FROM estatisticas WHERE id IN (4, 5, 6) ORDER BY id ASC";
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
            </div>
        </div>
    </section>

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
    // Ativar animações AOS
    document.addEventListener('DOMContentLoaded', function() {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
      
      // Ativar contador de estatísticas
      new PureCounter();
      
      // Adicionar animação suave à troca de abas
      const navLinks = document.querySelectorAll('.nav-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function() {
          const tabId = this.getAttribute('href').split('=')[1];
          localStorage.setItem('activeTab', tabId);
        });
        
        // Adicionar efeito hover
        link.addEventListener('mouseenter', function() {
          if (!this.classList.contains('active')) {
            this.style.color = 'var(--accent-color)';
          }
        });
        
        link.addEventListener('mouseleave', function() {
          if (!this.classList.contains('active')) {
            this.style.color = '#272828';
          }
        });
      });
      
      // Verificar se há uma aba ativa no localStorage
      const activeTab = localStorage.getItem('activeTab');
      if (activeTab) {
        document.querySelector(`[href="?tab=${activeTab}"]`).classList.add('active');
      }
      
      // Aplicar animação à aba ativa atual
      setTimeout(function() {
        const activePane = document.querySelector('.tab-pane.active');
        if (activePane) {
          activePane.classList.add('animate-fade-in');
        }
      }, 100);
      
      // Inicializar GLightbox para as imagens na galeria de monumentos
      GLightbox({
        selector: '.btn-ampliar',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
      });
    });
    
    // Função para alterar o texto do botão Ver Mais/Ver Menos
    function toggleVerMais(button, totalItens, itensVisiveis) {
      const isExpanded = button.getAttribute('aria-expanded') === 'true';
      
      if (isExpanded) {
        button.innerHTML = 'Ver menos <i class="bi bi-chevron-up ms-1"></i>';
        button.classList.add('active');
      } else {
        button.innerHTML = 'Ver mais <i class="bi bi-chevron-down ms-1"></i>';
        button.classList.remove('active');
      }
    }
  </script>

</body>

</html>
<?php
$conn->close();
?>