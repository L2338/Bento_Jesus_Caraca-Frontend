<?php
// Habilitar exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuração básica
    session_start();
require_once '../config/app-config.php';
require_once '../core/functions.php';
require_login();

// Conectar ao banco de dados com verificação de erro
    $conn = require '../../ConfigBD.php';
    
// Verificar se a conexão foi bem-sucedida
if (mysqli_connect_errno()) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Definir variáveis da página
$page_title = 'Gerenciador Galeria';

// Definir breadcrumbs
$breadcrumbs = [
    'Dashboard' => '../dashboard.php',
    'Galeria' => 'index.php'
];

// Buscar temas (categorias) com tratamento de erro
$sql_temas = "SELECT id_tema_imagem, descricao FROM temas_imagens ORDER BY descricao";
$result_temas = mysqli_query($conn, $sql_temas);

if (!$result_temas) {
    die("Erro na consulta de temas: " . mysqli_error($conn));
}

$temas = [];
while ($row = mysqli_fetch_assoc($result_temas)) {
    $temas[$row['id_tema_imagem']] = $row['descricao'];
}

// Filtrar por tema
$filtro_tema = isset($_GET['tema']) ? (int)$_GET['tema'] : 0;
$where = $filtro_tema > 0 ? "WHERE i.id_tema_imagem = $filtro_tema" : "";

// Buscar imagens com tratamento de erro
$sql = "SELECT i.id_imagem, i.imagem, i.descricao, t.descricao AS tema, i.id_tema_imagem 
        FROM imagens i 
        LEFT JOIN temas_imagens t ON i.id_tema_imagem = t.id_tema_imagem 
        $where 
        ORDER BY i.id_imagem DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erro na consulta de imagens: " . mysqli_error($conn));
}

// Contar o número de resultados para depuração
$num_rows = mysqli_num_rows($result);

// Processar exclusão
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $delete_result = mysqli_query($conn, "DELETE FROM imagens WHERE id_imagem = $id");
    
    if (!$delete_result) {
        $_SESSION['mensagem'] = 'Erro ao excluir imagem: ' . mysqli_error($conn);
        $_SESSION['tipo_mensagem'] = 'danger';
    } else {
        $_SESSION['mensagem'] = 'Imagem excluída com sucesso!';
        $_SESSION['tipo_mensagem'] = 'success';
    }
    
    header("Location: index.php" . ($filtro_tema > 0 ? "?tema=$filtro_tema" : ""));
    exit;
}

// Incluir cabeçalho do template
include_once '../templates/header.php';

// Exibir mensagens de feedback se existirem
if (isset($_SESSION['mensagem']) && isset($_SESSION['tipo_mensagem'])) {
    echo '<div class="alert alert-' . $_SESSION['tipo_mensagem'] . ' alert-dismissible fade show" role="alert">
            ' . $_SESSION['mensagem'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
          </div>';
    
    // Limpar mensagens para não exibir novamente
    unset($_SESSION['mensagem']);
    unset($_SESSION['tipo_mensagem']);
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestão de Galeria</h1>
    
    <!-- Dashboard com 5 blocos de atividades -->
<div class="row mb-4">
        <!-- Bloco 1: Total de Imagens -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Total de Imagens</h6>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="col-auto me-3">
                        <i class="bi bi-images fa-3x text-gray-300"></i>
                    </div>
                    <div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800"><?php echo mysqli_num_rows($result); ?></div>
                        <div class="text-muted">imagens registradas</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bloco 2: Categorias -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Categorias</h6>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="col-auto me-3">
                        <i class="bi bi-tags fa-3x text-gray-300"></i>
                    </div>
                    <div>
                        <div class="h1 mb-0 font-weight-bold text-gray-800"><?php echo count($temas); ?></div>
                        <div class="text-muted">categorias disponíveis</div>
                    </div>
                </div>
                <div class="card-footer p-2 text-center">
                    <a href="categorias.php" class="btn btn-sm btn-primary"><i class="bi bi-gear"></i> Gerenciar</a>
                </div>
            </div>
        </div>
        
        <!-- Bloco 3: Upload Rápido -->
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upload Rápido</h6>
                </div>
                <div class="card-body text-center">
                    <i class="bi bi-cloud-upload fa-3x mb-3 text-gray-300"></i>
                    <p>Adicione uma nova imagem à galeria</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddImagem">
                        <i class="bi bi-plus-circle"></i> Nova Imagem
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Bloco 4: Filtros -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filtrar por Categoria</h6>
            </div>
            <div class="card-body">
                    <div class="btn-group mb-2 w-100">
                        <a href="index.php" class="btn <?php echo $filtro_tema === 0 ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        Todas
                    </a>
                    <?php foreach ($temas as $id => $nome): ?>
                        <a href="index.php?tema=<?php echo $id; ?>" 
                           class="btn <?php echo $filtro_tema === $id ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <?php echo htmlspecialchars($nome); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-secondary"><?php echo mysqli_num_rows($result); ?> imagens encontradas</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bloco 5: Estatísticas -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribuição por Categoria</h6>
                </div>
                <div class="card-body">
                    <?php 
                    // Contar imagens por categoria
                    $temas_count = [];
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $tema_id = $row['id_tema_imagem'];
                        $tema_nome = $row['tema'] ?? 'Sem categoria';
                        
                        if (!isset($temas_count[$tema_id])) {
                            $temas_count[$tema_id] = [
                                'nome' => $tema_nome,
                                'count' => 0
                            ];
                        }
                        $temas_count[$tema_id]['count']++;
                    }
                    
                    // Resetar o ponteiro do resultado para uso posterior
                    mysqli_data_seek($result, 0);
                    ?>
                    
                    <?php foreach ($temas_count as $tema_id => $data): ?>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?php echo htmlspecialchars($data['nome']); ?></span>
                                <span><?php echo $data['count']; ?> imagens</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <?php 
                                    $percentage = $num_rows > 0 ? ($data['count'] / $num_rows) * 100 : 0;
                                    $color = 'bg-info';
                                    if ($tema_id == 1) $color = 'bg-primary';
                                    if ($tema_id == 2) $color = 'bg-success';
                                    if ($tema_id == 3) $color = 'bg-warning';
                                ?>
                                <div class="progress-bar <?php echo $color; ?>" role="progressbar" 
                                     style="width: <?php echo $percentage; ?>%" 
                                     aria-valuenow="<?php echo $percentage; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grid de Imagens -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Galeria</h6>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                        // Usar um cache de sessão para verificação de arquivos, evitando múltiplas chamadas a file_exists()
                        // que pode causar problemas de desempenho se o diretório for em rede ou inacessível
                        
                        // IMPORTANTE: Verificar o formato do caminho no banco de dados para evitar duplicação
                        $image_path = $row['imagem'];
                        
                        // Se o caminho já começar com 'assets/', então ele é relativo à raiz do projeto
                        if (strpos($image_path, 'assets/') === 0) {
                            $img_path = '../../' . $image_path;
                        } else {
                            // Verificar em qual diretório a imagem pode estar (retratos, viagens ou amigos)
                            $filename = basename($image_path);
                            $possible_dirs = [
                                '../../assets/img/galeria/' . $filename,
                                '../../assets/img/retratos/' . $filename,
                                '../../assets/img/viagens/' . $filename,
                                '../../assets/img/amigos/' . $filename
                            ];
                            
                            $img_path = '../../assets/img/galeria/' . $filename; // Caminho padrão
                            
                            // Verificar em qual diretório a imagem existe
                            foreach ($possible_dirs as $dir_path) {
                                if (file_exists($dir_path)) {
                                    $img_path = $dir_path;
                                    break;
                                }
                            }
                        }
                        
                        $cache_key = 'img_exists_' . md5($img_path);
                        
                        if (!isset($_SESSION[$cache_key])) {
                            $_SESSION[$cache_key] = file_exists($img_path);
                        }
                        
                        $img_exists = $_SESSION[$cache_key];
                        ?>
                        <div class="col">
                            <div class="card h-100 gallery-item">
                                <!-- Imagem -->
                                <div class="gallery-img-container" style="height: 160px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                                    <img src="<?php echo $img_path; ?>" 
                                         alt="<?php echo htmlspecialchars($row['descricao']); ?>" 
                                         class="card-img-top gallery-img"
                                         style="object-fit: cover; max-height: 100%; cursor: pointer;"
                                         onerror="this.src='../../assets/img/no-image.jpg'; this.classList.add('img-error');"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#imageModal"
                                         data-id="<?php echo $row['id_imagem']; ?>"
                                         data-image="<?php echo $img_path; ?>"
                                         data-desc="<?php echo htmlspecialchars($row['descricao']); ?>"
                                         data-tema="<?php echo htmlspecialchars($row['tema']); ?>"
                                         data-tema-id="<?php echo $row['id_tema_imagem']; ?>">
                                    
                                    <?php if (!$img_exists): ?>
                                    <div class="position-absolute bottom-0 end-0 p-1 bg-danger text-white" style="font-size: 10px;">
                                        Arquivo não encontrado
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Informações -->
                                <div class="card-body">
                                    <p class="card-text text-truncate" title="<?php echo htmlspecialchars($row['descricao']); ?>">
                                        <?php echo htmlspecialchars($row['descricao']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">#<?php echo $row['id_imagem']; ?></small>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($row['tema']); ?></span>
    </div>
</div>

                                <!-- Botões de ação -->
                                <div class="card-footer bg-transparent d-flex justify-content-between">
                                    <button type="button" class="btn btn-sm btn-outline-primary view-image-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#imageModal"
                                            data-id="<?php echo $row['id_imagem']; ?>"
                                            data-image="<?php echo $img_path; ?>"
                                            data-desc="<?php echo htmlspecialchars($row['descricao']); ?>"
                                            data-tema="<?php echo htmlspecialchars($row['tema']); ?>"
                                            data-tema-id="<?php echo $row['id_tema_imagem']; ?>">
                                        <i class="bi bi-eye"></i> Ver
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-image-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditImagem"
                                            data-id="<?php echo $row['id_imagem']; ?>"
                                            data-desc="<?php echo htmlspecialchars($row['descricao']); ?>"
                                            data-tema-id="<?php echo $row['id_tema_imagem']; ?>">
                                        <i class="bi bi-pencil"></i>
    </button>
</div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                        <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> Nenhuma imagem encontrada. Adicione sua primeira imagem clicando no botão "Nova Imagem".
                            </div>
                        <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para visualização da imagem -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Visualizar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 text-center">
                        <img id="modalImage" src="" alt="Imagem" class="img-fluid mb-3" style="max-height: 400px;">
                    </div>
                    <div class="col-md-5">
                        <h6 class="border-bottom pb-2 mb-3">Informações da Imagem</h6>
                        <div id="modalImageInfo">
                            <!-- Informações da imagem serão inseridas aqui via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnEditarNoModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <a id="deleteLink" href="#" class="btn btn-danger" 
                   onclick="return confirm('Tem certeza que deseja excluir esta imagem?');">
                    <i class="bi bi-trash"></i> Excluir
                </a>
                <a id="fullImageLink" href="#" class="btn btn-info" target="_blank">
                    <i class="bi bi-arrows-fullscreen"></i> Tamanho Real
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
</div>
</div>

<!-- Modal para adicionar imagem -->
<div class="modal fade" id="modalAddImagem" tabindex="-1" aria-labelledby="modalAddImagemLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddImagemLabel">Adicionar Nova Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formAddImagem" action="processar_imagem.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tema" class="form-label">Categoria:</label>
                        <select class="form-select" id="tema" name="tema" required>
                            <option value="">Selecione uma categoria</option>
                            <?php mysqli_data_seek($result_temas, 0); ?>
                            <?php while ($row = mysqli_fetch_assoc($result_temas)): ?>
                                <option value="<?php echo $row['id_tema_imagem']; ?>">
                                    <?php echo htmlspecialchars($row['descricao']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Arquivo de Imagem:</label>
                        <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg,image/png" required>
                        <div class="form-text">Formatos permitidos: JPG e PNG. Tamanho máximo: 5MB.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarNovaImagem">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar imagem -->
<div class="modal fade" id="modalEditImagem" tabindex="-1" aria-labelledby="modalEditImagemLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditImagemLabel">Editar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditImagem" action="processar_edicao.php" method="post">
                    <input type="hidden" id="edit_id" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="edit_descricao" class="form-label">Descrição:</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tema" class="form-label">Categoria:</label>
                        <select class="form-select" id="edit_tema" name="tema" required>
                            <option value="">Selecione uma categoria</option>
                            <?php mysqli_data_seek($result_temas, 0); ?>
                            <?php while ($row = mysqli_fetch_assoc($result_temas)): ?>
                                <option value="<?php echo $row['id_tema_imagem']; ?>">
                                    <?php echo htmlspecialchars($row['descricao']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarEdicao">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para os modais -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de visualização
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            const id = trigger.getAttribute('data-id');
            const imagePath = trigger.getAttribute('data-image');
            const description = trigger.getAttribute('data-desc');
            const tema = trigger.getAttribute('data-tema');
            const temaId = trigger.getAttribute('data-tema-id');
            
            // Atualiza os elementos do modal
            document.getElementById('modalImage').src = imagePath;
            document.getElementById('modalImage').alt = description;
            
            let infoHtml = `
                <div class="mb-3">
                    <p><strong>ID:</strong> ${id}</p>
                    <p><strong>Descrição:</strong> ${description}</p>
                    <p><strong>Categoria:</strong> ${tema}</p>
                </div>
            `;
            
            document.getElementById('modalImageInfo').innerHTML = infoHtml;
            
            // Armazenar ID para usar no botão de edição
            document.getElementById('btnEditarNoModal').setAttribute('data-id', id);
            document.getElementById('btnEditarNoModal').setAttribute('data-desc', description);
            document.getElementById('btnEditarNoModal').setAttribute('data-tema-id', temaId);
            
            // Atualiza os links
            document.getElementById('deleteLink').href = `index.php?excluir=${id}${temaId > 0 ? '&tema=' + temaId : ''}`;
            document.getElementById('fullImageLink').href = imagePath;
            
            // Adiciona tratamento para erro de carga da imagem
            document.getElementById('modalImage').onerror = function() {
                this.src = '../../assets/img/no-image.jpg';
                document.getElementById('modalImageInfo').innerHTML += '<div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle"></i> Imagem não encontrada no caminho especificado.</div>';
            };
        });
    }
    
    // Botão para abrir o modal de edição a partir do modal de visualização
    const btnEditarNoModal = document.getElementById('btnEditarNoModal');
    if (btnEditarNoModal) {
        btnEditarNoModal.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const desc = this.getAttribute('data-desc');
            const temaId = this.getAttribute('data-tema-id');
            
            // Fechar o modal de visualização
            const modalVisualizacao = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
            modalVisualizacao.hide();
            
            // Preencher o formulário de edição
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_descricao').value = desc;
            document.getElementById('edit_tema').value = temaId;
            
            // Abrir o modal de edição
            const modalEdicao = new bootstrap.Modal(document.getElementById('modalEditImagem'));
            modalEdicao.show();
        });
    }
    
    // Configurar botões de edição nas miniaturas
    document.querySelectorAll('.edit-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const desc = this.getAttribute('data-desc');
            const temaId = this.getAttribute('data-tema-id');
            
            // Preencher o formulário de edição
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_descricao').value = desc;
            document.getElementById('edit_tema').value = temaId;
        });
    });
    
    // Botão para salvar nova imagem
    const btnSalvarNovaImagem = document.getElementById('btnSalvarNovaImagem');
    if (btnSalvarNovaImagem) {
        btnSalvarNovaImagem.addEventListener('click', function() {
            const form = document.getElementById('formAddImagem');
            if (form.checkValidity()) {
                form.submit();
            } else {
                // Trigger form validation
                form.reportValidity();
            }
        });
    }
    
    // Botão para salvar edição
    const btnSalvarEdicao = document.getElementById('btnSalvarEdicao');
    if (btnSalvarEdicao) {
        btnSalvarEdicao.addEventListener('click', function() {
            const form = document.getElementById('formEditImagem');
            if (form.checkValidity()) {
                form.submit();
            } else {
                // Trigger form validation
                form.reportValidity();
            }
        });
    }
    
    // Tornar toda a área da imagem clicável para abrir o modal
    document.querySelectorAll('.gallery-img').forEach(img => {
        img.addEventListener('click', function() {
            // Os dados já estão nos atributos data-* da imagem
            // O evento show.bs.modal será acionado automaticamente
        });
    });
});
</script>

<style>
/* Estilo geral da página */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

/* Cards e elementos da galeria */
.gallery-item {
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.gallery-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: none;
}

.card {
    border-radius: 12px;
    overflow: hidden;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

/* Estilizar imagens */
.gallery-img-container {
    background-color: #f5f5f5;
    transition: all 0.3s ease;
}

.gallery-img {
    transition: all 0.3s ease;
}

.gallery-item:hover .gallery-img {
    transform: scale(1.05);
}

.img-error {
    opacity: 0.6;
    filter: grayscale(1);
}

/* Botões e elementos de interação */
.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-outline-secondary:hover {
    background-color: #5a5c69;
    border-color: #5a5c69;
}

/* Filtros e navegação */
.btn-group {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.btn-group .btn {
    border-radius: 0;
    border-right: 1px solid rgba(0,0,0,0.1);
}

.btn-group .btn:first-child {
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.btn-group .btn:last-child {
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
    border-right: none;
}

/* Modais */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-bottom: none;
}

.modal-header .modal-title {
    color: white;
}

.modal-footer {
    border-top: none;
}

/* Badge e rótulos */
.badge {
    font-weight: 500;
    padding: 0.4em 0.7em;
    border-radius: 6px;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.row-cols-1 .col {
    animation: fadeIn 0.5s ease forwards;
}

/* Personalizar cores */
.text-primary {
    color: #4e73df !important;
}

.text-secondary {
    color: #5a5c69 !important;
}

.text-info {
    color: #36b9cc !important;
}

.text-success {
    color: #1cc88a !important;
}
</style>

<?php 
// Incluir o rodapé do template
include_once '../templates/footer.php';
?>