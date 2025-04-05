<?php
/**
 * Formulário para adicionar/editar toponímia
 */

// Define o título da página
$page_title = 'Formulário de Toponímia';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Diretório para upload das imagens
$upload_dir = '../../assets/img/toponimia/';
// Certificar de que o diretório existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Inicializar variáveis
$id = $nome = $tipo = $cidade = $descricao = '';
$errors = [];

// Verificar se é uma edição
$is_edit = isset($_GET['id']) && is_numeric($_GET['id']);

// Se for edição, buscar dados da toponímia
if ($is_edit) {
    $id = (int)$_GET['id'];
    $sql = "SELECT id, nome, categoria as tipo, cidade, info_adicional as descricao FROM toponimia WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $nome = $row['nome'];
        $tipo = $row['tipo'];
        $cidade = $row['cidade'];
        $descricao = $row['descricao'];
    } else {
        // Toponímia não encontrada, redirecionar
        header("Location: index.php?tab=toponimia");
        exit;
    }
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar os dados
    $nome = trim($_POST['nome'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    
    // Validações
    if (empty($nome)) {
        $errors['nome'] = 'O nome da toponímia é obrigatório.';
    }
    
    if (empty($tipo)) {
        $errors['tipo'] = 'O tipo de toponímia é obrigatório.';
    }
    
    if (empty($cidade)) {
        $errors['cidade'] = 'A cidade é obrigatória.';
    }
    
    if (empty($descricao)) {
        $errors['descricao'] = 'A descrição é obrigatória.';
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($errors)) {
        if ($is_edit) {
            // Atualizar toponímia existente
            $sql = "UPDATE toponimia SET nome = ?, categoria = ?, cidade = ?, info_adicional = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi", $nome, $tipo, $cidade, $descricao, $id);
        } else {
            // Inserir nova toponímia
            $sql = "INSERT INTO toponimia (nome, categoria, cidade, info_adicional) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $nome, $tipo, $cidade, $descricao);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = $is_edit ? "Toponímia atualizada com sucesso!" : "Toponímia adicionada com sucesso!";
            // Redirecionar após um breve delay para mostrar a mensagem
            header("Refresh: 1; URL=index.php?tab=toponimia");
        } else {
            $error_message = "Erro ao " . ($is_edit ? "atualizar" : "adicionar") . " toponímia: " . mysqli_error($conn);
        }
    }
}

// Buscar tipos de toponímia para o dropdown
$tipos_toponimia = [
    ['valor' => 'rua', 'nome' => 'Rua'],
    ['valor' => 'avenida', 'nome' => 'Avenida'],
    ['valor' => 'praca', 'nome' => 'Praça'],
    ['valor' => 'escola', 'nome' => 'Escola'],
    ['valor' => 'instituicao', 'nome' => 'Instituição']
];

// Incluir o cabeçalho
include_once '../templates/header.php';

// Gerar breadcrumbs
$breadcrumbs = generate_breadcrumbs([
    'Dashboard' => '../dashboard.php',
    'Legado' => 'index.php?tab=toponimia',
    ($is_edit ? 'Editar Toponímia' : 'Nova Toponímia') => '#'
]);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="mb-0"><?php echo $is_edit ? 'Editar Toponímia' : 'Nova Toponímia'; ?></h1>
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
        <div class="card-header">
            <i class="bi bi-geo-alt-fill me-1"></i>
            Informações da Toponímia
        </div>
        <div class="card-body">
            <form method="post" class="needs-validation" novalidate>
                <!-- Campo oculto para ID em caso de edição -->
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['nome']) ? 'is-invalid' : ''; ?>" 
                               id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                        <?php if (isset($errors['nome'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['nome']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tipo" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select class="form-select <?php echo isset($errors['tipo']) ? 'is-invalid' : ''; ?>" 
                                id="tipo" name="tipo" required>
                            <option value="" disabled <?php echo empty($tipo) ? 'selected' : ''; ?>>Selecione a categoria...</option>
                            <?php foreach ($tipos_toponimia as $tipo_item): ?>
                                <option value="<?php echo htmlspecialchars($tipo_item['valor']); ?>" 
                                        <?php echo $tipo === $tipo_item['valor'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo_item['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['tipo'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['tipo']; ?></div>
                        <?php endif; ?>
                        <div class="form-text">Selecione a categoria da toponímia.</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="cidade" class="form-label">Cidade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['cidade']) ? 'is-invalid' : ''; ?>" 
                               id="cidade" name="cidade" value="<?php echo htmlspecialchars($cidade); ?>" required>
                        <?php if (isset($errors['cidade'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['cidade']; ?></div>
                        <?php endif; ?>
                        <div class="form-text">Indique a cidade onde está localizada a toponímia.</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                    <textarea class="form-control <?php echo isset($errors['descricao']) ? 'is-invalid' : ''; ?>" 
                              id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($descricao); ?></textarea>
                    <?php if (isset($errors['descricao'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['descricao']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">Descreva a toponímia, sua localização e importância na homenagem a Bento de Jesus Caraça.</div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?tab=toponimia" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> <?php echo $is_edit ? 'Atualizar' : 'Salvar'; ?> Toponímia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?> 