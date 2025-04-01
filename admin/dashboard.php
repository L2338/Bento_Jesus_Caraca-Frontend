<?php
session_start();

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Incluir conexão com banco de dados
$conn = require '../ConfigBD.php';

// Contar total de obras
$queryTotalObras = "SELECT COUNT(*) as total FROM obras";
$resultTotalObras = mysqli_query($conn, $queryTotalObras);
$totalObras = mysqli_fetch_assoc($resultTotalObras)['total'] ?? 0;

// Buscar obras recentes
$queryObras = "SELECT * FROM obras ORDER BY id DESC LIMIT 10";
$resultObras = mysqli_query($conn, $queryObras);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Painel Administrativo - Obras de Bento Jesus Caraça</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    :root {
      --primary-color: #ac062a;
      --primary-hover: #8c0523;
      --sidebar-bg: #343a40;
      --sidebar-text: #f8f9fa;
      --content-bg: #f8f9fa;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--content-bg);
    }
    
    #sidebar {
      background-color: var(--sidebar-bg);
      color: var(--sidebar-text);
      min-height: 100vh;
      transition: all 0.3s;
    }
    
    #sidebar .sidebar-header {
      padding: 20px;
      background-color: rgba(0, 0, 0, 0.1);
    }
    
    #sidebar ul.components {
      padding: 20px 0;
    }
    
    #sidebar ul li a {
      padding: 10px 20px;
      font-size: 1.1em;
      display: block;
      color: var(--sidebar-text);
      border-left: 3px solid transparent;
      transition: all 0.3s;
      text-decoration: none;
    }
    
    #sidebar ul li a:hover,
    #sidebar ul li a.active {
      background-color: rgba(255, 255, 255, 0.1);
      border-left: 3px solid var(--primary-color);
    }
    
    #sidebar ul li a i {
      margin-right: 10px;
    }
    
    .dashboard-content {
      padding: 20px;
    }
    
    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }
    
    .card-header {
      background-color: rgba(0, 0, 0, 0.03);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      font-weight: 600;
    }
    
    .stats-card {
      text-align: center;
      padding: 15px;
    }
    
    .stats-card i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: var(--primary-color);
    }
    
    .stats-card .number {
      font-size: 1.5rem;
      font-weight: bold;
    }
    
    .table-obras th {
      background-color: rgba(0, 0, 0, 0.03);
    }
    
    .action-buttons .btn {
      padding: .25rem .5rem;
      font-size: .875rem;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
      background-color: var(--primary-hover);
      border-color: var(--primary-hover);
    }
    
    @media (max-width: 768px) {
      #sidebar {
        min-height: auto;
        margin-bottom: 20px;
      }
      
      .stats-card {
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 px-0" id="sidebar">
      <div class="sidebar-header">
        <h3>Painel Admin</h3>
        <p class="mb-0 text-light">Bento Jesus Caraça</p>
      </div>
      
      <ul class="nav flex-column components">
        <li class="nav-item">
          <a href="#" class="nav-link active">
            <i class="fas fa-book"></i> Gerenciar Obras
          </a>
        </li>
        <li class="nav-item mt-5">
          <a href="../index.php" class="nav-link">
            <i class="fas fa-home"></i> Voltar ao Site
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt"></i> Sair
          </a>
        </li>
      </ul>
    </div>
    
    <!-- Page Content -->
    <div class="col-md-9 col-lg-10 ml-auto dashboard-content">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Obras</h1>
        <div class="user-info">
          <span class="mr-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          <i class="fas fa-user-circle fa-lg"></i>
        </div>
      </div>
      
      <!-- Stats Row -->
      <div class="row">
        <div class="col-md-4">
          <div class="card stats-card">
            <i class="fas fa-book"></i>
            <div class="number"><?php echo $totalObras; ?></div>
            <div class="text-muted">Total de Obras</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stats-card">
            <i class="fas fa-file-pdf"></i>
            <div class="number"><?php echo $totalObras; ?></div>
            <div class="text-muted">Documentos PDF</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stats-card">
            <i class="fas fa-eye"></i>
            <div class="number">--</div>
            <div class="text-muted">Visualizações</div>
          </div>
        </div>
      </div>
      
      <!-- Main Content Section -->
      <div class="row mt-4">
        <!-- Obras Listagem -->
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span>Catálogo de Obras</span>
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#obraModal">
                <i class="fas fa-plus"></i> Nova Obra
              </button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-obras">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Título</th>
                      <th>Autor</th>
                      <th>Ano</th>
                      <th>Categoria</th>
                      <th>Arquivo</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (mysqli_num_rows($resultObras) > 0): ?>
                      <?php while ($obra = mysqli_fetch_assoc($resultObras)): ?>
                        <tr>
                          <td><?php echo $obra['id']; ?></td>
                          <td><?php echo htmlspecialchars($obra['titulo']); ?></td>
                          <td><?php echo htmlspecialchars($obra['autor'] ?? 'Bento de Jesus Caraça'); ?></td>
                          <td><?php echo isset($obra['ano']) ? htmlspecialchars($obra['ano']) : 'N/A'; ?></td>
                          <td><?php echo isset($obra['categoria']) ? htmlspecialchars($obra['categoria']) : 'N/A'; ?></td>
                          <td>
                            <?php if (!empty($obra['pdf'])): ?>
                              <i class="fas fa-file-pdf text-danger"></i>
                            <?php else: ?>
                              <i class="fas fa-times text-muted"></i>
                            <?php endif; ?>
                          </td>
                          <td class="action-buttons">
                            <button class="btn btn-info btn-sm view-obra" data-id="<?php echo $obra['id']; ?>">
                              <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm edit-obra" data-id="<?php echo $obra['id']; ?>">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-obra" data-id="<?php echo $obra['id']; ?>">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-center">Nenhuma obra cadastrada.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para adicionar/editar obra -->
<div class="modal fade" id="obraModal" tabindex="-1" aria-labelledby="obraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="obraModalLabel">Adicionar Nova Obra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="obraForm" method="post" action="processar_obra.php" enctype="multipart/form-data">
          <input type="hidden" name="obra_id" id="obra_id" value="">
          
          <div class="row mb-3">
            <div class="col-md-8">
              <label for="titulo" class="form-label">Título da Obra</label>
              <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="col-md-4">
              <label for="ano" class="form-label">Ano de Publicação</label>
              <input type="number" class="form-control" id="ano" name="ano" min="1800" max="2030">
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="autor" class="form-label">Autor</label>
              <input type="text" class="form-control" id="autor" name="autor" value="Bento de Jesus Caraça">
            </div>
            <div class="col-md-6">
              <label for="categoria" class="form-label">Categoria</label>
              <select class="form-select" id="categoria" name="categoria">
                <option value="Matemática">Matemática</option>
                <option value="Filosofia">Filosofia</option>
                <option value="Educação">Educação</option>
                <option value="Ciências">Ciências</option>
                <option value="Outros">Outros</option>
              </select>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
          </div>
          
          <div class="mb-3">
            <label for="arquivo" class="form-label">Arquivo PDF</label>
            <input type="file" class="form-control" id="arquivo" name="pdf" accept=".pdf">
            <div id="arquivo_atual" class="form-text d-none">
              Arquivo atual: <span id="nome_arquivo"></span>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="imagem" class="form-label">Imagem de Capa</label>
            <input type="file" class="form-control" id="imagem" name="imagem_capa" accept="image/*">
            <div id="imagem_atual" class="form-text d-none">
              Imagem atual: <span id="nome_imagem"></span>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" form="obraForm" class="btn btn-primary">Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja excluir esta obra? Esta ação não pode ser desfeita.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="deleteForm" action="excluir_obra.php" method="post">
          <input type="hidden" name="obra_id" id="delete_obra_id" value="">
          <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS e dependências -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
    // Editar obra
    $('.edit-obra').on('click', function() {
      const id = $(this).data('id');
      
      // Limpar formulário
      $('#obraForm')[0].reset();
      
      // Alterar título do modal
      $('#obraModalLabel').text('Editar Obra');
      
      // Carregar dados da obra via AJAX
      $.ajax({
        url: 'obter_obra.php',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
          // Preencher formulário
          $('#obra_id').val(data.id);
          $('#titulo').val(data.titulo);
          $('#autor').val(data.autor);
          $('#ano').val(data.ano);
          $('#categoria').val(data.categoria);
          $('#descricao').val(data.descricao);
          
          // Mostrar arquivos atuais se existirem
          if (data.pdf) {
            $('#arquivo_atual').removeClass('d-none');
            $('#nome_arquivo').text(data.pdf);
          }
          
          if (data.imagem_capa) {
            $('#imagem_atual').removeClass('d-none');
            $('#nome_imagem').text(data.imagem_capa);
          }
          
          // Abrir modal
          $('#obraModal').modal('show');
        },
        error: function() {
          alert('Erro ao carregar dados da obra');
        }
      });
    });
    
    // Nova obra
    $('.btn-primary[data-bs-target="#obraModal"]').on('click', function() {
      // Limpar formulário
      $('#obraForm')[0].reset();
      $('#obra_id').val('');
      $('#obraModalLabel').text('Adicionar Nova Obra');
      $('#arquivo_atual').addClass('d-none');
      $('#imagem_atual').addClass('d-none');
    });
    
    // Visualizar obra
    $('.view-obra').on('click', function() {
      const id = $(this).data('id');
      window.open(`visualizar_obra.php?id=${id}`, '_blank');
    });
    
    // Confirmar exclusão
    $('.delete-obra').on('click', function() {
      const id = $(this).data('id');
      $('#delete_obra_id').val(id);
      $('#deleteModal').modal('show');
    });
  });
</script>
</body>
</html> 