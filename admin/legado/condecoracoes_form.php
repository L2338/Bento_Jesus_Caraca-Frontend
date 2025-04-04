<?php
/**
 * Admin - Formulário de Condecorações
 * Permite adicionar e editar condecorações recebidas por Bento de Jesus Caraça
 */

// Define o título da página
$page_title = 'Formulário de Condecoração';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Inicializar variáveis
$id = null;
$titulo = '';
$data = '';
$descricao = '';
$is_update = false;
$errors = [];

// Verificar se é uma atualização (edição)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $is_update = true;
    
    // Buscar dados da condecoração
    $sql = "SELECT id, titulo, DATE_FORMAT(data, '%Y-%m-%d') as data_formatada, descricao FROM condecoracoes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $titulo = $row['titulo'];
        $data = $row['data_formatada'];
        $descricao = $row['descricao'];
    } else {
        // Condecoração não encontrada
        $error_message = "Condecoração não encontrada!";
        header('Location: index.php?tab=condecoracoes');
        exit;
    }
}

// Processar formulário submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar e sanitizar dados
    $titulo = trim($_POST['titulo'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    
    // Validações
    if (empty($titulo)) {
        $errors['titulo'] = 'O título é obrigatório';
    } elseif (strlen($titulo) > 255) {
        $errors['titulo'] = 'O título não pode ter mais de 255 caracteres';
    }
    
    if (empty($data)) {
        $errors['data'] = 'A data é obrigatória';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $errors['data'] = 'A data deve estar no formato YYYY-MM-DD';
    }
    
    if (empty($descricao)) {
        $errors['descricao'] = 'A descrição é obrigatória';
    }
    
    // Se não houver erros, inserir/atualizar no banco de dados
    if (empty($errors)) {
        if ($is_update) {
            // Atualizar condecoração existente
            $sql = "UPDATE condecoracoes SET titulo = ?, data = ?, descricao = ?, data_atualizacao = NOW() WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $titulo, $data, $descricao, $id);
        } else {
            // Inserir nova condecoração
            $sql = "INSERT INTO condecoracoes (titulo, data, descricao, data_criacao, data_atualizacao) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $titulo, $data, $descricao);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirecionar para a lista com mensagem de sucesso
            $action = $is_update ? 'atualizada' : 'adicionada';
            header("Location: index.php?tab=condecoracoes&success=Condecoração $action com sucesso!");
            exit;
        } else {
            $error_message = "Erro ao salvar condecoração: " . mysqli_error($conn);
        }
    }
}

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Legado' => ADMIN_URL . 'legado/index.php?tab=condecoracoes',
    ($is_update ? 'Editar Condecoração' : 'Nova Condecoração') => '#'
]);

// Incluir o cabeçalho
include_once '../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $is_update ? 'Editar Condecoração' : 'Nova Condecoração'; ?></h1>
    <?php echo $breadcrumbs; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mt-4 mb-4">
        <div class="card-header">
            <i class="bi bi-award me-1"></i>
            <?php echo $is_update ? 'Editar Condecoração' : 'Adicionar Nova Condecoração'; ?>
        </div>
        <div class="card-body">
            <form method="post" id="condecoracaoForm" class="needs-validation" novalidate>
                <?php if ($is_update): ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['titulo']) ? 'is-invalid' : ''; ?>" 
                           id="titulo" name="titulo" value="<?php echo htmlspecialchars($titulo); ?>" required>
                    <?php if (isset($errors['titulo'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['titulo']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="data" class="form-label">Data <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?php echo isset($errors['data']) ? 'is-invalid' : ''; ?>" 
                           id="data" name="data" value="<?php echo htmlspecialchars($data); ?>" required>
                    <?php if (isset($errors['data'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['data']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">Data em que a condecoração foi concedida</div>
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                    <textarea class="form-control <?php echo isset($errors['descricao']) ? 'is-invalid' : ''; ?>" 
                              id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($descricao); ?></textarea>
                    <?php if (isset($errors['descricao'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['descricao']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">Descreva detalhes sobre a condecoração, quem concedeu, motivo, etc.</div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?tab=condecoracoes" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> <?php echo $is_update ? 'Salvar Alterações' : 'Adicionar Condecoração'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validação do formulário no lado do cliente
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('condecoracaoForm');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    }, false);
});
</script>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?> 