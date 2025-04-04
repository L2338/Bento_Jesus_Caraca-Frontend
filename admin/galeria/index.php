<?php
/**
 * Admin - Gestão de Galeria
 * Gerencia as imagens do site organizadas por categorias
 */

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir configurações e funções
require_once '../config/app-config.php';
require_once '../core/functions.php';

// Verificação de login
require_login();

// Conexão com o banco de dados
try {
    $conn = require '../../ConfigBD.php';
    
    if (!$conn) {
        throw new Exception("Falha na conexão com o banco de dados: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    set_flash_message('error', $e->getMessage());
    $error_message = $e->getMessage();
}

// Processar exclusão de imagem
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Primeiro, buscar a imagem para obter o caminho do arquivo
    $sql_select = "SELECT imagem FROM imagens WHERE id_imagem = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    
    if ($row = mysqli_fetch_assoc($result_select)) {
        $imagem_path = $row['imagem'];
        
        // Excluir o arquivo físico se ele existir
        $file_path = "../../" . $imagem_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Agora excluir o registro do banco de dados
        $sql_delete = "DELETE FROM imagens WHERE id_imagem = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            set_flash_message('success', "Imagem excluída com sucesso!");
        } else {
            set_flash_message('error', "Erro ao excluir imagem: " . mysqli_error($conn));
        }
    }
    
    // Redirecionar para evitar reenvio do formulário
    header("Location: index.php?deleted=true");
    exit;
}

// Processar upload de nova imagem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'upload') {
        // Diretório para upload
        $upload_dir = '../../assets/img/galeria/';
        
        // Verificar se o diretório existe, se não, criar
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Receber dados do formulário
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $id_tema = (int)($_POST['tema'] ?? 0);
        
        // Validar dados
        $errors = [];
        if (empty($titulo)) {
            $errors[] = "O título da imagem é obrigatório.";
        }
        
        if ($id_tema <= 0) {
            $errors[] = "Por favor, selecione uma categoria válida.";
        }
        
        // Validar e processar o upload do arquivo
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $file_tmp = $_FILES['imagem']['tmp_name'];
            $file_name = $_FILES['imagem']['name'];
            $file_size = $_FILES['imagem']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Verificar extensão
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_ext, $allowed_exts)) {
                $errors[] = "Formato de arquivo não permitido. Use JPG, PNG ou GIF.";
            }
            
            // Verificar tamanho (5MB máximo)
            if ($file_size > 5 * 1024 * 1024) {
                $errors[] = "O arquivo é muito grande. O tamanho máximo é 5MB.";
            }
            
            // Se não houver erros, processar o upload
            if (empty($errors)) {
                // Gerar nome único para o arquivo
                $new_file_name = 'galeria_' . time() . '_' . uniqid() . '.' . $file_ext;
                $file_destination = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    // Arquivo enviado com sucesso, inserir no banco de dados
                    $imagem_path = 'assets/img/galeria/' . $new_file_name;
                    
                    $sql = "INSERT INTO imagens (imagem, descricao, id_tema_imagem) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ssi", $imagem_path, $descricao, $id_tema);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        set_flash_message('success', "Imagem enviada com sucesso!");
                    } else {
                        set_flash_message('error', "Erro ao salvar imagem no banco de dados: " . mysqli_error($conn));
                        // Se falhou no banco, remover o arquivo
                        if (file_exists($file_destination)) {
                            unlink($file_destination);
                        }
                    }
                } else {
                    set_flash_message('error', "Erro ao enviar o arquivo. Tente novamente.");
                }
            }
        } else {
            $errors[] = "Por favor, selecione uma imagem para enviar.";
        }
    } elseif ($_POST['action'] == 'edit') {
        // Processar edição de imagem
        $id_imagem = (int)($_POST['id_imagem'] ?? 0);
        $descricao = $_POST['descricao'] ?? '';
        $id_tema = (int)($_POST['tema'] ?? 0);
        
        // Validar dados
        $errors = [];
        if ($id_imagem <= 0) {
            $errors[] = "ID de imagem inválido.";
        }
        
        if ($id_tema <= 0) {
            $errors[] = "Por favor, selecione uma categoria válida.";
        }
        
        // Se não houver erros, atualizar no banco de dados
        if (empty($errors)) {
            $sql = "UPDATE imagens SET descricao = ?, id_tema_imagem = ? WHERE id_imagem = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $descricao, $id_tema, $id_imagem);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', "Imagem atualizada com sucesso!");
            } else {
                set_flash_message('error', "Erro ao atualizar imagem: " . mysqli_error($conn));
            }
        }
    }
}

// Buscar temas de imagens
$sql_temas = "SELECT id_tema_imagem, descricao FROM temas_imagens ORDER BY id_tema_imagem";
$result_temas = mysqli_query($conn, $sql_temas);
$temas = [];
if ($result_temas) {
    while ($row = mysqli_fetch_assoc($result_temas)) {
        $temas[$row['id_tema_imagem']] = $row['descricao'];
    }
}

// Filtrar por tema (categoria)
$filtro_tema = isset($_GET['tema']) ? (int)$_GET['tema'] : 0;
$where_clause = $filtro_tema > 0 ? "WHERE id_tema_imagem = $filtro_tema" : "";

// Buscar imagens da galeria
$sql_imagens = "SELECT i.id_imagem, i.imagem, i.descricao, i.id_tema_imagem, t.descricao AS tema_nome 
                FROM imagens i 
                LEFT JOIN temas_imagens t ON i.id_tema_imagem = t.id_tema_imagem 
                $where_clause 
                ORDER BY i.id_imagem DESC";
$result_imagens = mysqli_query($conn, $sql_imagens);

// Verificar se houve erro na consulta
if ($result_imagens === false) {
    $error_message = "Erro na consulta de imagens: " . mysqli_error($conn);
}

// Definir título da página e breadcrumbs para os templates
$page_title = "Gestão de Galeria";
$breadcrumbs = [
    'Dashboard' => '../dashboard.php',
    'Galeria' => ''
];

// Funções auxiliares para manipulação de temas/categorias
function get_tema_badge_color($tema_id) {
    switch ($tema_id) {
        case 1: // Retratos
            return 'primary';
        case 2: // Amigos
            return 'success';
        case 3: // Viagens
            return 'info';
        default:
            return 'secondary';
    }
}

// Incluir o cabeçalho do template
include_once '../templates/header.php';
?>

<!-- Conteúdo específico da página -->
<!-- Filtros de Categorias -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-1"></i> Filtrar por Categoria
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <a href="index.php" class="btn btn-outline-primary <?php echo $filtro_tema === 0 ? 'active' : ''; ?>">
                        Todas
                    </a>
                    <?php foreach ($temas as $id => $nome): ?>
                        <a href="index.php?tema=<?php echo $id; ?>" 
                           class="btn btn-outline-<?php echo get_tema_badge_color($id); ?> <?php echo $filtro_tema === $id ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($nome); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botão para adicionar nova imagem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Imagens na Galeria</h5>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
        <i class="bi bi-cloud-arrow-up"></i> Carregar Nova Imagem
    </button>
</div>

<!-- Grid de Imagens -->
<div class="row">
    <?php if ($result_imagens && mysqli_num_rows($result_imagens) > 0): ?>
        <?php while ($imagem = mysqli_fetch_assoc($result_imagens)): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <?php if (!empty($imagem['imagem'])): ?>
                            <img src="<?php echo SITE_URL . htmlspecialchars($imagem['imagem']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($imagem['descricao']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span class="text-muted">Sem imagem</span>
                            </div>
                        <?php endif; ?>
                        <span class="position-absolute top-0 end-0 badge bg-<?php echo get_tema_badge_color($imagem['id_tema_imagem']); ?> m-2">
                            <?php echo htmlspecialchars($imagem['tema_nome']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($imagem['descricao']); ?></p>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary edit-image" 
                                    data-id="<?php echo $imagem['id_imagem']; ?>"
                                    data-descricao="<?php echo htmlspecialchars($imagem['descricao']); ?>"
                                    data-tema="<?php echo $imagem['id_tema_imagem']; ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editImageModal"
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="#" class="btn btn-sm btn-outline-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal"
                                data-id="<?php echo $imagem['id_imagem']; ?>"
                                data-descricao="<?php echo htmlspecialchars($imagem['descricao']); ?>"
                                title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Não foram encontradas imagens. Adicione novas imagens à galeria.
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Links de diagnóstico -->
<div class="mt-4 text-muted small">
    <p>
        <a href="diagnostico.php" class="text-muted">Executar diagnóstico</a> | 
        <a href="galeria_simplificada.php" class="text-muted">Ver versão simplificada</a>
    </p>
</div>

<!-- Modal de Upload de Imagem -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Carregar Nova Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título da Imagem</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tema" class="form-label">Categoria</label>
                        <select class="form-select" id="tema" name="tema" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($temas as $id => $nome): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Selecionar Imagem</label>
                        <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*" required>
                        <div class="form-text">Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 5MB.</div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Carregar Imagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Imagem -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageModalLabel">Editar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php" id="formEditImage">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id_imagem" id="edit_id_imagem">
                    
                    <div class="mb-3">
                        <label for="edit_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tema" class="form-label">Categoria</label>
                        <select class="form-select" id="edit_tema" name="tema" required>
                            <?php foreach ($temas as $id => $nome): ?>
                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nome); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Imagem</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta imagem?</p>
                <p id="deleteImageDescription" class="fw-bold"></p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Excluir Imagem</a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript específico da página -->
<script>
// Script para manipulação dos modais
document.addEventListener('DOMContentLoaded', function() {
    // Modal de exclusão
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const descricao = button.getAttribute('data-descricao');
            
            document.getElementById('deleteImageDescription').textContent = descricao;
            document.getElementById('confirmDelete').href = 'index.php?delete=' + id;
        });
    }
    
    // Modal de edição
    const editButtons = document.querySelectorAll('.edit-image');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const descricao = this.getAttribute('data-descricao');
            const tema = this.getAttribute('data-tema');
            
            document.getElementById('edit_id_imagem').value = id;
            document.getElementById('edit_descricao').value = descricao;
            document.getElementById('edit_tema').value = tema;
        });
    });
});
</script>

<?php 
// Incluir o rodapé do template
include_once '../templates/footer.php';
?>