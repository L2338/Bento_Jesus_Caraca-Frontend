<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: timeline_list.php?error=id_missing");
    exit;
}

$blockId = intval($_GET['id']);

// Buscar dados do bloco
$query = "SELECT * FROM timeline_blocos WHERE id = $blockId";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    header("Location: timeline_list.php?error=block_not_found");
    exit;
}

$block = $result->fetch_assoc();

// Configuração da página
$pageTitle = "Visualizar Bloco: " . htmlspecialchars($block['titulo']);
$currentSection = "vida";

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<div class="container-fluid p-4">
    <!-- Cabeçalho da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;">
            <?php echo $pageTitle; ?>
        </h1>
        <div>
            <a href="timeline_edit.php?id=<?php echo $blockId; ?>" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="timeline_list.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Conteúdo da página -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Detalhes do Bloco da Timeline</h6>
                    <span class="badge <?php echo $block['ativo'] ? 'bg-success' : 'bg-secondary'; ?>">
                        <?php echo $block['ativo'] ? 'Ativo' : 'Inativo'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-secondary small">TÍTULO</label>
                                <p class="fw-bold fs-5"><?php echo htmlspecialchars($block['titulo']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label text-secondary small">DATA/PERÍODO</label>
                                <p class="fs-6">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <?php echo htmlspecialchars($block['data_periodo']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label text-secondary small">ORDEM NA TIMELINE</label>
                                <p class="fs-6">
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <?php echo $block['ordem']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mb-4">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 text-primary fw-bold">Conteúdo Resumido</h6>
                                    <div class="ms-2 badge bg-light text-dark border">Exibido inicialmente</div>
                                </div>
                                <div class="p-3 bg-light rounded border">
                                    <?php echo $block['conteudo']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 text-primary fw-bold">Conteúdo Expandido</h6>
                                    <div class="ms-2 badge bg-light text-dark border">Exibido após clique</div>
                                </div>
                                <div class="p-3 bg-light rounded border">
                                    <?php echo $block['conteudo_expandido']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="mb-4">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label text-secondary small">INFORMAÇÕES ADICIONAIS</label>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th width="15%" class="table-light">Data de Criação</th>
                                                <td><?php echo date('d/m/Y H:i', strtotime($block['data_criacao'])); ?></td>
                                            </tr>
                                            <tr>
                                                <th width="15%" class="table-light">Última Atualização</th>
                                                <td><?php echo date('d/m/Y H:i', strtotime($block['data_atualizacao'])); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="timeline_list.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
                <div>
                    <a href="timeline_edit.php?id=<?php echo $blockId; ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Bloco
                    </a>
                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i> Excluir Bloco
                    </button>
                </div>
            </div>
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
                <p>Tem certeza que deseja excluir o bloco "<strong><?php echo htmlspecialchars($block['titulo']); ?></strong>"?</p>
                <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>Esta ação não pode ser desfeita.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i> Cancelar
                </button>
                <form action="timeline_list.php" method="POST">
                    <input type="hidden" name="block_id" value="<?php echo $blockId; ?>">
                    <input type="hidden" name="delete_block" value="1">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "../../admin/templates/footer.php";
?> 