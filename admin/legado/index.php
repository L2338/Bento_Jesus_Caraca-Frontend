<?php
/**
 * Admin - Seção de Legado
 * Gerencia elementos do legado de Bento de Jesus Caraça: condecorações, monumentos, toponímia, instituições
 */

// Define o título da página
$page_title = 'Legado';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Processamento de exclusão de condecoração
if (isset($_POST['delete_condecoracao']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM condecoracoes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Condecoração excluída com sucesso!";
    } else {
        $error_message = "Erro ao excluir condecoração: " . mysqli_error($conn);
    }
}

// Processamento de exclusão de monumento
if (isset($_POST['delete_monumento']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM monumentos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Monumento excluído com sucesso!";
    } else {
        $error_message = "Erro ao excluir monumento: " . mysqli_error($conn);
    }
}

// Processamento de exclusão de toponímia
if (isset($_POST['delete_toponimia']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM toponimia WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Toponímia excluída com sucesso!";
    } else {
        $error_message = "Erro ao excluir toponímia: " . mysqli_error($conn);
    }
}

// Definir a aba ativa (padrão: 'condecoracoes')
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'condecoracoes';

// Incluir o cabeçalho
include_once '../templates/header.php';

// Gerar breadcrumbs
$breadcrumbs = generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Legado' => '#'
]);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="mb-0"><?php echo $page_title; ?></h1>
        <nav class="small" aria-label="breadcrumb">
    <?php echo $breadcrumbs; ?>
        </nav>
    </div>
    
    <!-- Mensagens de sucesso ou erro -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

            <div class="card mb-4">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab == 'condecoracoes' ? 'active' : ''; ?>" 
                        href="?tab=condecoracoes" 
                        role="tab">
                        <i class="bi bi-award me-1"></i> Condecorações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab == 'monumentos' ? 'active' : ''; ?>" 
                        href="?tab=monumentos" 
                        role="tab">
                        <i class="bi bi-building-fill me-1"></i> Monumentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab == 'toponimia' ? 'active' : ''; ?>" 
                        href="?tab=toponimia" 
                        role="tab">
                        <i class="bi bi-geo-alt-fill me-1"></i> Toponímia
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active_tab == 'instituicoes' ? 'active' : ''; ?>" 
                        href="?tab=instituicoes" 
                        role="tab">
                        <i class="bi bi-building me-1"></i> Instituições
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <?php if ($active_tab == 'condecoracoes'): ?>
                <!-- Conteúdo da aba Condecorações -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-award me-1"></i> Condecorações
                    </h5>
                    <a href="condecoracoes_form.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Nova Condecoração
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Título</th>
                                <th scope="col">Data</th>
                                <th scope="col">Descrição</th>
                                <th scope="col" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Buscar condecorações do banco de dados
                            $sql = "SELECT id, titulo, DATE_FORMAT(data, '%d/%m/%Y') as data_formatada, descricao FROM condecoracoes ORDER BY data DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['data_formatada']); ?></td>
                                        <td><?php echo mb_strimwidth(htmlspecialchars($row['descricao']), 0, 100, '...'); ?></td>
                                        <td class="text-end">
                                            <a href="condecoracoes_form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-titulo="<?php echo htmlspecialchars($row['titulo']); ?>"
                                                    data-tipo="condecoracao"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="mb-3 text-muted">Nenhuma condecoração encontrada.</p>
                                        <a href="condecoracoes_form.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Adicionar primeira condecoração
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($active_tab == 'monumentos'): ?>
                <!-- Conteúdo da aba Monumentos -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building-fill me-1"></i> Monumentos
                    </h5>
                    <a href="monumentos_form.php" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Novo Monumento
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%">ID</th>
                                <th scope="col" style="width: 15%">Imagem</th>
                                <th scope="col" style="width: 20%">Nome</th>
                                <th scope="col" style="width: 15%">Localização</th>
                                <th scope="col" style="width: 10%">Ano</th>
                                <th scope="col" style="width: 25%">Descrição</th>
                                <th scope="col" style="width: 10%" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Buscar monumentos do banco de dados
                            $sql = "SELECT id, nome, local as localizacao, YEAR(data_inauguracao) as ano, descricao, imagem FROM monumentos ORDER BY data_inauguracao DESC";
                            $result = mysqli_query($conn, $sql);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <?php if (!empty($row['imagem'])): ?>
                                                <img src="<?php echo '../../' . htmlspecialchars($row['imagem']); ?>" 
                                                    alt="<?php echo htmlspecialchars($row['nome']); ?>" 
                                                    class="img-thumbnail" 
                                                    style="max-width: 100px; max-height: 70px;">
                                            <?php else: ?>
                                                <div class="text-muted">Sem imagem</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($row['localizacao']); ?></td>
                                        <td><?php echo htmlspecialchars($row['ano']); ?></td>
                                        <td><?php echo mb_strimwidth(htmlspecialchars($row['descricao']), 0, 100, '...'); ?></td>
                                        <td class="text-end">
                                            <a href="monumentos_form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-titulo="<?php echo htmlspecialchars($row['nome']); ?>"
                                                    data-tipo="monumento"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="mb-3 text-muted">Nenhum monumento encontrado.</p>
                                        <a href="monumentos_form.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Adicionar primeiro monumento
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($active_tab == 'toponimia'): ?>
                <!-- Conteúdo da aba Toponímia -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt-fill me-1"></i> Toponímia
                    </h5>
                    <div>
                        <a href="toponimia_form.php" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Nova Toponímia
                        </a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%">ID</th>
                                <th scope="col" style="width: 30%">Nome</th>
                                <th scope="col" style="width: 15%">Categoria</th>
                                <th scope="col" style="width: 20%">Cidade</th>
                                <th scope="col" style="width: 20%">Descrição</th>
                                <th scope="col" style="width: 10%" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Simplificar a consulta e verificar conexão
                        if (!$conn) {
                            echo '<tr><td colspan="6" class="text-center text-danger">Erro de conexão com o banco de dados.</td></tr>';
                        } else {
                            $sql = "SELECT * FROM toponimia ORDER BY nome";
                            $result = mysqli_query($conn, $sql);
                            
                            if (!$result) {
                                echo '<tr><td colspan="6" class="text-center text-danger">Erro na consulta: ' . mysqli_error($conn) . '</td></tr>';
                            } else if (mysqli_num_rows($result) > 0) {
                                // Temos resultados para mostrar
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Definir valores padrão para campos que podem estar vazios
                                    $id = $row['id'];
                                    $nome = $row['nome'];
                                    $cidade = $row['cidade'];
                                    $categoria = $row['categoria'];
                                    $descricao = $row['info_adicional'] ?? '';
                                    
                                    // Traduzir categoria para exibição
                                    $categoria_traduzida = '';
                                    switch(strtolower($categoria)) {
                                        case 'rua': $categoria_traduzida = 'Rua'; break;
                                        case 'avenida': $categoria_traduzida = 'Avenida'; break;
                                        case 'praca': $categoria_traduzida = 'Praça'; break;
                                        case 'escola': $categoria_traduzida = 'Escola'; break;
                                        case 'instituicao': $categoria_traduzida = 'Instituição'; break;
                                        default: $categoria_traduzida = ucfirst($categoria);
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td><?php echo htmlspecialchars($nome); ?></td>
                                        <td><?php echo htmlspecialchars($categoria_traduzida); ?></td>
                                        <td><?php echo htmlspecialchars($cidade); ?></td>
                                        <td><?php echo mb_strimwidth(htmlspecialchars($descricao), 0, 100, '...'); ?></td>
                                        <td class="text-end">
                                            <a href="toponimia_form.php?id=<?php echo $id; ?>" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal" 
                                                    data-id="<?php echo $id; ?>"
                                                    data-titulo="<?php echo htmlspecialchars($nome); ?>"
                                                    data-tipo="toponimia"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                // Nenhum resultado encontrado
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="mb-3 text-muted">Nenhuma toponímia encontrada.</p>
                                        <a href="toponimia_form.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Adicionar primeira toponímia
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($active_tab == 'instituicoes'): ?>
                <!-- Conteúdo da aba Instituições -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building me-1"></i> Instituições no Legado
                    </h5>
                    <button type="button" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Nova Instituição
                    </button>
                </div>
                
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Localização</th>
                                    <th scope="col">Fundação</th>
                                <th scope="col" class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemplo de entrada -->
                                <tr>
                                    <td>Escola Profissional Bento de Jesus Caraça</td>
                                    <td>Escola Profissional</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1989</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sociedade Portuguesa de Matemática</td>
                                    <td>Associação</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1940</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Universidade Aberta</td>
                                    <td>Universidade</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1988</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <?php endif; ?>
        </div>
    </div>
                </div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <span id="item-tipo">a condecoração</span> <strong id="item-titulo"></strong>?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita!</small></p>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" id="delete-form">
                    <input type="hidden" name="id" id="item-id">
                    <input type="hidden" name="delete_item" id="delete-item-type" value="">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para o modal de exclusão -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do modal de exclusão
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const titulo = button.getAttribute('data-titulo');
            const tipo = button.getAttribute('data-tipo');
            
            document.getElementById('item-id').value = id;
            document.getElementById('item-titulo').textContent = titulo;
            
            const deleteForm = document.getElementById('delete-form');
            const deleteItemType = document.getElementById('delete-item-type');
            const itemTipoSpan = document.getElementById('item-tipo');
            
            // Configurar o tipo de item a ser excluído
            if (tipo === 'condecoracao') {
                deleteItemType.name = 'delete_condecoracao';
                itemTipoSpan.textContent = 'a condecoração';
            } else if (tipo === 'monumento') {
                deleteItemType.name = 'delete_monumento';
                itemTipoSpan.textContent = 'o monumento';
            } else if (tipo === 'toponimia') {
                deleteItemType.name = 'delete_toponimia';
                itemTipoSpan.textContent = 'a toponímia';
            }
        });
    }
});
</script>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?>