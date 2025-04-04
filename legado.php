<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';

// Funções para obter dados

// Função para obter e exibir condecorações
function obterCondecoracoes($conn) {
    $output = "";
    
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
            $output .= '<div class="card">';
            $output .= '<h3>' . htmlspecialchars($row["titulo"]) . '</h3>';
            $output .= '<p><strong>Data:</strong> ' . htmlspecialchars($row["data_formatada"]) . '</p>';
            $output .= '<p>' . htmlspecialchars($row["descricao"]) . '</p>';
            $output .= '</div>';
        }
    } else {
        $output = '<p>Nenhuma condecoração encontrada.</p>';
    }
    
    return $output;
}

// Função para obter e exibir monumentos
function obterMonumentos($conn) {
    $output = '<div class="gallery">';
    
    // Verifica se há termo de pesquisa
    $where = "";
    if (isset($_GET["search_monumentos"]) && !empty($_GET["search_monumentos"])) {
        $termo = $conn->real_escape_string($_GET["search_monumentos"]);
        $where = "WHERE nome LIKE '%$termo%' OR local LIKE '%$termo%' OR descricao LIKE '%$termo%'";
    }
    
    // Consulta SQL
    $sql = "SELECT id, nome, local, DATE_FORMAT(data_inauguracao, '%d/%m/%Y') as data_formatada, descricao, imagem FROM monumentos $where ORDER BY data_inauguracao DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Loop através dos resultados
        while ($row = $result->fetch_assoc()) {
            $output .= '<div class="gallery-item">';
            
            // Imagem (usar placeholder se não houver imagem)
            if (!empty($row["imagem"]) && file_exists($row["imagem"])) {
                $output .= '<img src="' . htmlspecialchars($row["imagem"]) . '" alt="' . htmlspecialchars($row["nome"]) . '">';
            } else {
                $output .= '<img src="/api/placeholder/250/200" alt="' . htmlspecialchars($row["nome"]) . '">';
            }
            
            $output .= '<div class="gallery-item-info">';
            $output .= '<h3>' . htmlspecialchars($row["nome"]) . '</h3>';
            $output .= '<p>' . htmlspecialchars($row["descricao"]) . '</p>';
            $output .= '<p><strong>Local:</strong> ' . htmlspecialchars($row["local"]) . '</p>';
            $output .= '<p><strong>Inauguração:</strong> ' . htmlspecialchars($row["data_formatada"]) . '</p>';
            $output .= '</div></div>';
        }
    } else {
        $output = '<p>Nenhum monumento encontrado.</p>';
    }
    
    $output .= '</div>';
    return $output;
}

// Função para obter e exibir toponímia
function obterToponimia($conn) {
    $output = "";
    
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
            
            $titulo_categoria = isset($categorias_nome[$categoria]) ? $categorias_nome[$categoria] : ucfirst($categoria);
            
            $output .= '<h3>' . $titulo_categoria . '</h3>';
            $output .= '<ul>';
            
            $itens = explode('||', $row["itens"]);
            foreach ($itens as $item) {
                $output .= '<li>' . htmlspecialchars($item) . '</li>';
            }
            
            $output .= '</ul>';
        }
    } else {
        $output = '<p>Nenhuma toponímia encontrada.</p>';
    }
    
    return $output;
}

// Define a aba ativa (obtém da URL ou define padrão)
$tab_ativa = isset($_GET['tab']) ? $_GET['tab'] : 'condecoracoes';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Legado</title>
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

  <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #ac062a;
        }
        
        .tab {
            padding: 10px 20px;
            background-color: #e0e0e0;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .tab.active {
            background-color:#ac062a;
            color: white;
        }
        
        .tab:not(:last-child) {
            margin-right: 5px;
        }
        
        .tab-content {
            display: none;
            background-color: white;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        h1 {
            margin-bottom: 20px;
        }
        
        h2 {
            color:#ac062a;
            margin: 20px 0 15px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        h3 {
            color:#ac062a;
            margin: 15px 0 10px 0;
        }
        
        p {
            margin-bottom: 15px;
        }
        
        ul {
            list-style-position: inside;
            margin-bottom: 15px;
        }
        
        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .card h3 {
            margin-top: 0;
        }
        
        

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .gallery-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery-item-info {
            padding: 15px;
        }

        .add-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .form-group textarea {
            height: 100px;
        }

        .btn {
            padding: 10px 15px;
            background-color:#ac062a;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .status-message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

<body class="events-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
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
              <p class="mb-0">Odio et unde deleniti. Deserunt numquam exercitationem. Officiis quo odio sint voluptas consequatur ut a odio voluptatem. Sit dolorum debitis veritatis natus dolores. Quasi ratione sint. Sit quaerat ipsum dolorem.</p>
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

    <section>
    <div class="container">
        <?php if (!empty($status_message)): ?>
            <div class="status-message status-<?php echo $status_type; ?>">
                <?php echo $status_message; ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab <?php echo $tab_ativa == 'condecoracoes' ? 'active' : ''; ?>" onclick="location.href='?tab=condecoracoes'">Condecorações</button>
            <button class="tab <?php echo $tab_ativa == 'monumentos' ? 'active' : ''; ?>" onclick="location.href='?tab=monumentos'">Monumentos</button>
            <button class="tab <?php echo $tab_ativa == 'toponimia' ? 'active' : ''; ?>" onclick="location.href='?tab=toponimia'">Toponímia</button>
            <button class="tab <?php echo $tab_ativa == 'biblioteca' ? 'active' : ''; ?>" onclick="location.href='?tab=biblioteca'">Biblioteca Cosmos</button>
        </div>
        
        <div id="condecoracoes" class="tab-content <?php echo $tab_ativa == 'condecoracoes' ? 'active' : ''; ?>">
            <h2>Condecorações</h2>          
            <?php echo obterCondecoracoes($conn); ?>
        </div>
        
        <div id="monumentos" class="tab-content <?php echo $tab_ativa == 'monumentos' ? 'active' : ''; ?>">
            <h2>Monumentos</h2>          
            <?php echo obterMonumentos($conn); ?>
        </div>
        
        <div id="toponimia" class="tab-content <?php echo $tab_ativa == 'toponimia' ? 'active' : ''; ?>">
            <h2>Toponímia</h2>         
            <?php echo obterToponimia($conn); ?>
        </div>
        <div id="biblioteca" class="tab-content <?php echo $tab_ativa == 'biblioteca' ? 'active' : ''; ?>">
            <section id="about" class="about section">

              <div class="container">

              <div class="row gy-4">

              <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
                <img src="assets/img/LogoCosmos.png" class="img-fluid" alt="">
              </div>

              <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
                <h2>Biblioteca Cosmos</h2>
                <p class="fst-italic">
                 Um projeto revolucionário que democratizou o saber em Portugal.
                </p>
                <ul>
                <li><i class="bi bi-check-circle"></i> <span>Fundada em <strong>1941</strong> por Bento de Jesus Caraça, a <strong>Biblioteca Cosmos</strong> foi uma das iniciativas editoriais mais ambiciosas da época.</span></li>
                <li><i class="bi bi-check-circle"></i> <span>Com o objetivo de levar cultura e ciência ao povo, publicou mais de <strong>114 títulos</strong> em <strong>145 Volumes</strong> cobrindo temas como matemática, literatura, história, filosofia e ciências naturais.</span></li>
                <li><i class="bi bi-check-circle"></i> <span>A coleção teve uma circulação massiva, distribuindo quase <strong>800.000 exemplares</strong> e tornando-se referência na divulgação do conhecimento.</span></li>
                <li><i class="bi bi-check-circle"></i> <span>Mesmo enfrentando censura durante o Estado Novo, a Biblioteca Cosmos marcou gerações e influenciou o pensamento crítico em Portugal.</span></li>
                </ul>
                <a href="http://www.bibliotecacosmos.com/" target="_blank" class="read-more"><span>Saber Mais</span><i class="bi bi-arrow-right"></i></a>
              </div>
        </div>

      </div>

    </section>
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

</body>

</html>
<?php
$conn->close();
?>