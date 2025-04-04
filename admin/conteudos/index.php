<?php
/**
 * Gestão de Conteúdos - Estatísticas e Textos
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Gestão de Conteúdos';

// Incluir o cabeçalho
include_once '../templates/header.php';

// Definir breadcrumbs - formato correto (associativo)
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Conteúdos' => '#'
]);

// Processamento de formulários - Atualização de estatísticas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_estatistica' && isset($_POST['id'], $_POST['valor'])) {
        $id = (int)$_POST['id'];
        $valor = (int)$_POST['valor'];
        
        $conn = require_once '../../ConfigBD.php';
        $sql = "UPDATE estatisticas SET valor = ?, data_atualizacao = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $valor, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Estatística atualizada com sucesso!";
        } else {
            $error_message = "Erro ao atualizar estatística: " . mysqli_error($conn);
        }
    } 
    else if ($action === 'update_texto' && isset($_POST['id'], $_POST['conteudo'])) {
        $id = (int)$_POST['id'];
        $conn = require_once '../../ConfigBD.php';
        $conteudo = mysqli_real_escape_string($conn, $_POST['conteudo']);
        
        $sql = "UPDATE textos_secoes SET conteudo = ?, data_atualizacao = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $conteudo, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Texto atualizado com sucesso!";
        } else {
            $error_message = "Erro ao atualizar texto: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $page_title; ?></h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabs de Navegação -->
    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="estatisticas-tab" data-bs-toggle="tab" data-bs-target="#estatisticas" type="button" role="tab" aria-controls="estatisticas" aria-selected="true">Estatísticas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="textos-tab" data-bs-toggle="tab" data-bs-target="#textos" type="button" role="tab" aria-controls="textos" aria-selected="false">Textos</button>
        </li>
    </ul>

    <div class="tab-content" id="contentTabsContent">
        <!-- Aba de Estatísticas -->
        <div class="tab-pane fade show active" id="estatisticas" role="tabpanel" aria-labelledby="estatisticas-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estatísticas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Chave</th>
                                    <th>Valor</th>
                                    <th>Descrição</th>
                                    <th>Última Atualização</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = require '../../ConfigBD.php';
                                $query = "SELECT * FROM estatisticas ORDER BY id ASC";
                                $result = mysqli_query($conn, $query);
                                
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['chave']); ?></td>
                                            <td>
                                                <span class="valor-display"><?php echo number_format($row['valor'], 0, ',', '.'); ?></span>
                                                <div class="valor-edit" style="display: none;">
                                                    <form method="post" class="d-inline valor-form">
                                                        <input type="hidden" name="action" value="update_estatistica">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="valor" value="<?php echo $row['valor']; ?>" required>
                                                            <button class="btn btn-primary save-btn" type="submit">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                            <button class="btn btn-secondary cancel-btn" type="button">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-btn" 
                                                        data-id="<?php echo $row['id']; ?>"
                                                        title="Editar estatística">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma estatística encontrada.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Aba de Textos -->
        <div class="tab-pane fade" id="textos" role="tabpanel" aria-labelledby="textos-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Textos de Seções</h6>
                </div>
                <div class="card-body">
                    <?php
                    $conn = require '../../ConfigBD.php';
                    $query = "SELECT * FROM textos_secoes ORDER BY id ASC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Texto: <?php echo htmlspecialchars($row['chave']); ?></h6>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="action" value="update_texto">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label for="chave_<?php echo $row['id']; ?>" class="form-label">Identificador:</label>
                                            <input type="text" class="form-control bg-light" id="chave_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['chave']); ?>" readonly>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="conteudo_<?php echo $row['id']; ?>" class="form-label">Conteúdo:</label>
                                            <textarea class="form-control" id="conteudo_<?php echo $row['id']; ?>" name="conteudo" rows="6"><?php echo htmlspecialchars($row['conteudo']); ?></textarea>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="small text-muted">
                                                Última atualização: <?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-save me-1"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> Nenhum texto encontrado.
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para edição de estatísticas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Botões de edição
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const displayEl = row.querySelector('.valor-display');
            const editEl = row.querySelector('.valor-edit');
            
            // Mostrar formulário de edição
            displayEl.style.display = 'none';
            editEl.style.display = 'block';
            
            // Focar no input
            const input = editEl.querySelector('input[name="valor"]');
            input.focus();
            input.select();
        });
    });
    
    // Botões de cancelar
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const displayEl = row.querySelector('.valor-display');
            const editEl = row.querySelector('.valor-edit');
            
            // Esconder formulário de edição
            displayEl.style.display = 'inline';
            editEl.style.display = 'none';
        });
    });
});
</script>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?> 