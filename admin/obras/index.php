<?php
/**
 * Gestão de Obras - Página inicial
 */

// Define o título da página
$page_title = 'Gestão de Obras';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Obras' => '#'
]);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Obras</h6>
        <a href="<?php echo ADMIN_URL; ?>obras/adicionar.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Obra
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Ano</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="py-5">
                                <i class="bi bi-exclamation-circle text-muted fa-3x mb-3"></i>
                                <p class="mb-0 text-muted">Página em construção. Implementação futura.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?>