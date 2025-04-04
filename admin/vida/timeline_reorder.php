<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Configuração da página
$pageTitle = "Reordenar Blocos da Timeline";
$currentSection = "vida";

// Processar atualização de ordem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ordem'])) {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errorMessage = "Erro de segurança: token inválido.";
    } else {
        $ordem = json_decode($_POST['ordem'], true);
        
        if (is_array($ordem)) {
            $success = true;
            
            // Iniciar transação
            $conn->begin_transaction();
            
            try {
                foreach ($ordem as $position => $id) {
                    $id = (int)$id;
                    $position = (int)$position + 1; // Começar de 1 em vez de 0
                    
                    $stmt = $conn->prepare("UPDATE timeline_blocos SET ordem = ? WHERE id = ?");
                    $stmt->bind_param("ii", $position, $id);
                    $stmt->execute();
                }
                
                // Commit da transação
                $conn->commit();
                
                // Redirecionar com mensagem de sucesso
                header("Location: index.php?success=blocks_reordered");
                exit;
            } catch (Exception $e) {
                // Rollback em caso de erro
                $conn->rollback();
                $errorMessage = "Erro ao atualizar a ordem: " . $e->getMessage();
                $success = false;
            }
        } else {
            $errorMessage = "Formato inválido.";
        }
    }
}

// Gerar token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Buscar blocos da timeline
$query = "SELECT id, titulo, data_periodo, ordem, ativo FROM timeline_blocos ORDER BY ordem ASC";
$result = $conn->query($query);

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<!-- Estilos personalizados -->
<style>
    .sortable-item {
        cursor: grab;
        transition: all 0.2s;
        background-color: #fff;
    }
    .sortable-item:hover {
        background-color: #f8f9fa;
    }
    .sortable-item.dragging {
        opacity: 0.8;
        cursor: grabbing;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.15);
        z-index: 1000;
    }
    .drag-handle {
        cursor: grab;
        color: #6c757d;
    }
    .drag-handle:hover {
        color: #495057;
    }
    .position-indicator {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        background-color: #f0f0f0;
        color: #495057;
        border-radius: 50%;
    }
    .inactive-item {
        opacity: 0.6;
    }
    .save-order-bar {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        box-shadow: 0 -0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .item-details {
        flex: 1;
        overflow: hidden;
    }
    .item-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 80%;
    }
</style>

<div class="container-fluid p-4">
    <!-- Cabeçalho da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;"><?php echo $pageTitle; ?></h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
    
    <!-- Alertas de erro -->
    <?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Instruções -->
    <div class="alert alert-info mb-4">
        <h5 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Como Reordenar</h5>
        <p>Arraste e solte os blocos para definir a ordem em que eles aparecerão na timeline. Os blocos no topo aparecerão primeiro.</p>
        <hr>
        <p class="mb-0">Após organizar os blocos na ordem desejada, clique em <strong>Salvar Nova Ordem</strong> no final da página.</p>
    </div>
    
    <!-- Lista de Blocos -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Arraste para Reordenar</h6>
        </div>
        <div class="card-body p-0">
            <?php if ($result && $result->num_rows > 0): ?>
                <form id="sortableForm" action="timeline_reorder.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="ordem" id="ordem" value="">
                    
                    <ul id="sortableList" class="list-group list-group-flush sortable">
                        <?php 
                        $index = 0;
                        while ($row = $result->fetch_assoc()): 
                            $index++;
                        ?>
                            <li class="list-group-item sortable-item d-flex align-items-center py-3 <?php echo $row['ativo'] ? '' : 'inactive-item'; ?>" data-id="<?php echo $row['id']; ?>">
                                <div class="position-indicator me-3"><?php echo $index; ?></div>
                                <div class="drag-handle me-3">
                                    <i class="bi bi-grip-vertical fs-4"></i>
                                </div>
                                <div class="item-details me-3">
                                    <div class="fw-bold item-title"><?php echo htmlspecialchars($row['titulo']); ?></div>
                                    <div class="small text-muted d-flex align-items-center">
                                        <span class="badge bg-light text-dark border me-2"><?php echo htmlspecialchars($row['data_periodo']); ?></span>
                                        <span class="badge <?php echo $row['ativo'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $row['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ms-auto">
                                    <a href="timeline_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    
                    <!-- Barra de Salvar -->
                    <div class="save-order-bar bg-light p-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="saveOrderBtn">
                            <i class="bi bi-save me-2"></i> Salvar Nova Ordem
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar2-x text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-muted mb-3">Nenhum bloco de timeline encontrado para reordenar.</p>
                    <a href="timeline_add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Adicionar Bloco
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Inclui Sortable.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Sortable
        const list = document.getElementById('sortableList');
        if (list) {
            const sortable = new Sortable(list, {
                animation: 150,
                handle: '.drag-handle',
                draggable: '.sortable-item',
                onStart: function(evt) {
                    evt.item.classList.add('dragging');
                },
                onEnd: function(evt) {
                    evt.item.classList.remove('dragging');
                    
                    // Atualizar os números de posição
                    updatePositionNumbers();
                }
            });
            
            // Formulário submit
            document.getElementById('sortableForm').addEventListener('submit', function(e) {
                // Obter a ordem atual
                const items = Array.from(list.querySelectorAll('.sortable-item'));
                const ordem = items.map(item => item.dataset.id);
                
                // Definir a ordem como valor do input
                document.getElementById('ordem').value = JSON.stringify(ordem);
                
                // Desabilitar botão para evitar cliques duplos
                document.getElementById('saveOrderBtn').disabled = true;
                document.getElementById('saveOrderBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
            });
            
            // Função para atualizar os números de posição
            function updatePositionNumbers() {
                const items = Array.from(list.querySelectorAll('.sortable-item'));
                items.forEach((item, index) => {
                    const indicator = item.querySelector('.position-indicator');
                    if (indicator) {
                        indicator.textContent = index + 1;
                    }
                });
            }
        }
    });
</script>

<?php
include "../../admin/templates/footer.php";
?> 