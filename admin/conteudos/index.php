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

// Definir breadcrumbs
$breadcrumbs = [
    ['url' => '../dashboard.php', 'titulo' => 'Dashboard'],
    ['url' => 'index.php', 'titulo' => 'Conteúdos']
];

// Processamento de formulários - Atualização de estatísticas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_estatistica' && isset($_POST['id'], $_POST['valor'])) {
        $id = (int)$_POST['id'];
        $valor = (int)$_POST['valor'];
        
        $conn = require_once '../../ConfigBD.php';
        $sql = "UPDATE estatisticas SET valor = $valor WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Estatística atualizada com sucesso!";
        } else {
            $error_message = "Erro ao atualizar estatística: " . mysqli_error($conn);
        }
    } 
    else if ($action === 'update_texto' && isset($_POST['id'], $_POST['conteudo'])) {
        $id = (int)$_POST['id'];
        $conteudo = mysqli_real_escape_string(require '../../ConfigBD.php', $_POST['conteudo']);
        
        $conn = require_once '../../ConfigBD.php';
        $sql = "UPDATE textos_secoes SET conteudo = '$conteudo' WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Texto atualizado com sucesso!";
        } else {
            $error_message = "Erro ao atualizar texto: " . mysqli_error($conn);
        }
    }
}

// Incluir o cabeçalho
include_once '../templates/header.php';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gestão de Conteúdos</h1>
</div>

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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Estatísticas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="update_estatistica">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="valor" value="<?php echo $row['valor']; ?>" required>
                                                    <button class="btn btn-outline-primary" type="submit">
                                                        <i class="bi bi-check2"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editEstatistica(<?php echo $row['id']; ?>)">
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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
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
                            <div class="card-header">
                                <h6 class="mb-0">Texto #<?php echo $row['id']; ?> - <?php echo htmlspecialchars($row['chave']); ?></h6>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <input type="hidden" name="action" value="update_texto">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    
                                    <div class="form-group mb-3">
                                        <label for="chave_<?php echo $row['id']; ?>" class="form-label">Chave:</label>
                                        <input type="text" class="form-control" id="chave_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($row['chave']); ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="conteudo_<?php echo $row['id']; ?>" class="form-label">Conteúdo:</label>
                                        <textarea class="form-control" id="conteudo_<?php echo $row['id']; ?>" name="conteudo" rows="6"><?php echo htmlspecialchars($row['conteudo']); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Última Atualização:</label>
                                        <p><?php echo date('d/m/Y H:i', strtotime($row['data_atualizacao'])); ?></p>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="alert alert-info">
                        Nenhum texto encontrado.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?> 