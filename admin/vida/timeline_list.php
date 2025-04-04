<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Configuração da página
$pageTitle = "Gerenciar Blocos da Timeline";
$currentSection = "vida";

// Paginação
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Busca e filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Condições para busca e filtros
$whereConditions = [];
if (!empty($search)) {
    $searchTerm = $conn->real_escape_string($search);
    $whereConditions[] = "(titulo LIKE '%$searchTerm%' OR data_periodo LIKE '%$searchTerm%' OR conteudo LIKE '%$searchTerm%')";
}

if ($filter === 'active') {
    $whereConditions[] = "ativo = 1";
} elseif ($filter === 'inactive') {
    $whereConditions[] = "ativo = 0";
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Obter contagem total para paginação
$countQuery = "SELECT COUNT(*) as total FROM timeline_blocos $whereClause";
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Obter blocos para esta página
$query = "SELECT id, titulo, data_periodo, ordem, ativo, data_atualizacao
          FROM timeline_blocos
          $whereClause
          ORDER BY ordem ASC
          LIMIT $offset, $limit";
$result = $conn->query($query);

// Processar exclusão de bloco
if (isset($_POST['delete_block']) && isset($_POST['block_id'])) {
    $blockId = intval($_POST['block_id']);
    
    // Verificar se o bloco existe
    $checkQuery = "SELECT id FROM timeline_blocos WHERE id = $blockId";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult->num_rows > 0) {
        // Excluir o bloco
        $deleteQuery = "DELETE FROM timeline_blocos WHERE id = $blockId";
        if ($conn->query($deleteQuery)) {
            // Reordenar os blocos restantes
            $conn->query("SET @count = 0");
            $conn->query("UPDATE timeline_blocos SET ordem = (@count:=@count+1) ORDER BY ordem ASC");
            
            // Redirecionar para atualizar a página e evitar reenvio do form
            header("Location: timeline_list.php?success=delete" . (!empty($search) ? "&search=$search" : "") . "&filter=$filter&page=$page");
            exit;
        } else {
            $errorMessage = "Erro ao excluir bloco: " . $conn->error;
        }
    } else {
        $errorMessage = "Bloco não encontrado!";
    }
}

// Processar ativação/desativação
if (isset($_POST['toggle_status']) && isset($_POST['block_id'])) {
    $blockId = intval($_POST['block_id']);
    $currentStatus = intval($_POST['current_status']);
    $newStatus = $currentStatus ? 0 : 1;
    
    $updateQuery = "UPDATE timeline_blocos SET ativo = $newStatus WHERE id = $blockId";
    if ($conn->query($updateQuery)) {
        header("Location: timeline_list.php?success=status" . (!empty($search) ? "&search=$search" : "") . "&filter=$filter&page=$page");
        exit;
    } else {
        $errorMessage = "Erro ao atualizar status: " . $conn->error;
    }
}

// Verificar mensagens de sucesso
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'add':
            $successMessage = "Novo bloco adicionado com sucesso!";
            break;
        case 'edit':
            $successMessage = "Bloco atualizado com sucesso!";
            break;
        case 'delete':
            $successMessage = "Bloco excluído com sucesso!";
            break;
        case 'status':
            $successMessage = "Status do bloco atualizado com sucesso!";
            break;
        case 'order':
            $successMessage = "Ordem dos blocos atualizada com sucesso!";
            break;
    }
}

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<div class="container-fluid p-4">
    <!-- Cabeçalho da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;"><?php echo $pageTitle; ?></h1>
        <div>
            <a href="timeline_add.php" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-plus-lg"></i> Novo Bloco
            </a>
            <a href="timeline_reorder.php" class="btn btn-info btn-sm me-2">
                <i class="bi bi-sort-down"></i> Reordenar
            </a>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Alertas de sucesso/erro -->
    <?php if (isset($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Filtros e pesquisa -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
            <i class="bi bi-funnel me-2 text-primary"></i>
            <h6 class="m-0 fw-bold text-primary">Filtrar e Pesquisar Blocos</h6>
        </div>
        <div class="card-body bg-light p-4">
            <form action="timeline_list.php" method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Pesquisar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Buscar por título, data ou conteúdo..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filter" class="form-label">Filtrar por status</label>
                    <select class="form-select" id="filter" name="filter">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Todos os blocos</option>
                        <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Apenas ativos</option>
                        <option value="inactive" <?php echo $filter === 'inactive' ? 'selected' : ''; ?>>Apenas inativos</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>
            </form>
            
            <?php if (!empty($search) || $filter !== 'all'): ?>
            <div class="mt-3 p-2 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        <span class="text-muted small">
                            <?php 
                                $filterTexts = [];
                                if (!empty($search)) $filterTexts[] = "Pesquisa: \"" . htmlspecialchars($search) . "\"";
                                if ($filter === 'active') $filterTexts[] = "Status: Ativos";
                                if ($filter === 'inactive') $filterTexts[] = "Status: Inativos";
                                
                                echo "Filtros ativos: " . implode(", ", $filterTexts);
                            ?>
                        </span>
                    </div>
                    <a href="timeline_list.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar Filtros
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Lista de blocos -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                <i class="bi bi-list-ul me-2"></i> Blocos da Timeline
            </h6>
            <span class="badge bg-primary rounded-pill"><?php echo $totalRows; ?> bloco(s)</span>
        </div>
        <div class="card-body">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="35%">Título</th>
                                <th width="15%">Data/Período</th>
                                <th width="10%">Status</th>
                                <th width="15%">Atualizado em</th>
                                <th width="20%" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill"><?php echo $row['ordem']; ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($row['titulo']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['data_periodo']); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $row['ativo'] ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                                        <?php echo $row['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="timeline_view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-info btn-sm" title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="timeline_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-<?php echo $row['ativo'] ? 'success' : 'secondary'; ?> btn-sm toggle-status-btn" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-status="<?php echo $row['ativo']; ?>"
                                                title="<?php echo $row['ativo'] ? 'Desativar' : 'Ativar'; ?>">
                                            <i class="bi bi-toggle-<?php echo $row['ativo'] ? 'on' : 'off'; ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-block-btn" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-title="<?php echo htmlspecialchars($row['titulo']); ?>"
                                                title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Formulários para ações (ocultos) -->
                                    <form id="toggle-form-<?php echo $row['id']; ?>" action="timeline_list.php" method="POST" class="d-none">
                                        <input type="hidden" name="block_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="current_status" value="<?php echo $row['ativo']; ?>">
                                        <input type="hidden" name="toggle_status" value="1">
                                    </form>
                                    
                                    <form id="delete-form-<?php echo $row['id']; ?>" action="timeline_list.php" method="POST" class="d-none">
                                        <input type="hidden" name="block_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="delete_block" value="1">
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Navegação da timeline" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>&filter=<?php echo $filter; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>&filter=<?php echo $filter; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>&filter=<?php echo $filter; ?>" aria-label="Próximo">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="empty-state mb-3">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-muted mb-3">
                        <?php if (!empty($search) || $filter !== 'all'): ?>
                            Nenhum bloco encontrado com os filtros aplicados.
                        <?php else: ?>
                            Nenhum bloco de timeline cadastrado.
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!empty($search) || $filter !== 'all'): ?>
                        <a href="timeline_list.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpar Filtros
                        </a>
                    <?php else: ?>
                        <a href="timeline_add.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Adicionar Primeiro Bloco
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o bloco "<span id="block-title" class="fw-bold"></span>"?</p>
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>Esta ação não pode ser desfeita.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirm-delete">
                    <i class="bi bi-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Adicionar overlay de carregamento para blocos da timeline -->
<div id="timelineLoadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background-color: rgba(0, 0, 0, 0.7); z-index: 9999;">
    <div class="position-absolute top-50 start-50 translate-middle text-center">
        <div class="mb-3">
            <i class="bi bi-hourglass-split text-warning" style="font-size: 4rem; animation: flip 1.5s infinite linear;"></i>
        </div>
        <h4 class="text-white mb-2">Viajando no tempo...</h4>
        <p class="text-light">Um momento enquanto exploramos a história!</p>
    </div>
</div>

<style>
@keyframes flip {
    0% { transform: rotateX(0deg); }
    50% { transform: rotateX(180deg); }
    100% { transform: rotateX(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manipulador para botões de ativar/desativar
    document.querySelectorAll('.toggle-status-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');
            const statusText = currentStatus === '1' ? 'desativar' : 'ativar';
            
            if (confirm(`Tem certeza que deseja ${statusText} este bloco?`)) {
                document.getElementById(`toggle-form-${id}`).submit();
            }
        });
    });
    
    // Manipulador para botões de exclusão (usando modal)
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let blockIdToDelete = null;
    
    document.querySelectorAll('.delete-block-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            blockIdToDelete = this.getAttribute('data-id');
            const blockTitle = this.getAttribute('data-title');
            
            document.getElementById('block-title').textContent = blockTitle;
            deleteModal.show();
        });
    });
    
    document.getElementById('confirm-delete').addEventListener('click', function() {
        if (blockIdToDelete) {
            document.getElementById(`delete-form-${blockIdToDelete}`).submit();
        }
        deleteModal.hide();
    });
    
    // Adicionar loading overlay para visualização de blocos
    const viewButtons = document.querySelectorAll('a[href^="timeline_view.php"]');
    const loadingOverlay = document.getElementById('timelineLoadingOverlay');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Mostrar overlay de carregamento
            if (loadingOverlay) {
                loadingOverlay.classList.remove('d-none');
                
                // Esconder automaticamente após 1.5 segundos para garantir
                // que não fique preso se ocorrer algum problema no carregamento
                setTimeout(() => {
                    loadingOverlay.classList.add('d-none');
                }, 2000);
            }
        });
    });
});
</script>

<?php
include "../../admin/templates/footer.php";
?> 