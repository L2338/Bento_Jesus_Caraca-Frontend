<?php
/**
 * Formulário para adicionar/editar monumentos
 */

// Define o título da página
$page_title = 'Formulário de Monumento';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Diretório para upload das imagens
$upload_dir = '../../assets/img/monumentos/';
// Certificar de que o diretório existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Inicializar variáveis
$id = $nome = $localizacao = $ano = $descricao = $imagem_atual = '';
$errors = [];

// Verificar se é uma edição
$is_edit = isset($_GET['id']) && is_numeric($_GET['id']);

// Se for edição, buscar dados do monumento
if ($is_edit) {
    $id = (int)$_GET['id'];
    $sql = "SELECT id, nome, local as localizacao, YEAR(data_inauguracao) as ano, descricao, imagem FROM monumentos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $nome = $row['nome'];
        $localizacao = $row['localizacao'];
        $ano = $row['ano'];
        $descricao = $row['descricao'];
        $imagem_atual = $row['imagem'] ?? '';
    } else {
        // Monumento não encontrado, redirecionar
        header("Location: index.php?tab=monumentos");
        exit;
    }
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar os dados
    $nome = trim($_POST['nome'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $ano = trim($_POST['ano'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    
    // Validações
    if (empty($nome)) {
        $errors['nome'] = 'O nome do monumento é obrigatório.';
    }
    
    if (empty($localizacao)) {
        $errors['localizacao'] = 'A localização é obrigatória.';
    }
    
    if (empty($ano)) {
        $errors['ano'] = 'O ano é obrigatório.';
    } elseif (!is_numeric($ano) || $ano < 1900 || $ano > date('Y')) {
        $errors['ano'] = 'Por favor, informe um ano válido.';
    }
    
    if (empty($descricao)) {
        $errors['descricao'] = 'A descrição é obrigatória.';
    }
    
    // Processar upload de imagem, se houver
    $imagem_path = $imagem_atual; // Manter a imagem atual por padrão
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_nome = $_FILES['imagem']['name'];
        $imagem_extensao = strtolower(pathinfo($imagem_nome, PATHINFO_EXTENSION));
        
        // Verificar extensão
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imagem_extensao, $extensoes_permitidas)) {
            $errors['imagem'] = 'Formato de imagem não permitido. Use JPG, PNG ou GIF.';
        } else {
            // Gerar nome único para a imagem
            $novo_nome = 'monumento_' . time() . '_' . uniqid() . '.' . $imagem_extensao;
            $novo_caminho = $upload_dir . $novo_nome;
            
            // Tentar mover o arquivo
            if (move_uploaded_file($imagem_tmp, $novo_caminho)) {
                // Atualizar o caminho da imagem para salvar no banco
                $imagem_path = 'assets/img/monumentos/' . $novo_nome;
                
                // Se estiver editando e já existia uma imagem, remover a anterior
                if ($is_edit && !empty($imagem_atual) && $imagem_atual !== $imagem_path) {
                    $caminho_antigo = '../../' . $imagem_atual;
                    if (file_exists($caminho_antigo)) {
                        unlink($caminho_antigo);
                    }
                }
            } else {
                $errors['imagem'] = 'Erro ao fazer upload da imagem. Tente novamente.';
            }
        }
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($errors)) {
        if ($is_edit) {
            // Converter o ano para data completa (primeiro dia do ano)
            $data_inauguracao = $ano . '-01-01';
            
            // Atualizar monumento existente
            $sql = "UPDATE monumentos SET nome = ?, local = ?, data_inauguracao = ?, descricao = ?, imagem = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssi", $nome, $localizacao, $data_inauguracao, $descricao, $imagem_path, $id);
        } else {
            // Converter o ano para data completa (primeiro dia do ano)
            $data_inauguracao = $ano . '-01-01';
            
            // Inserir novo monumento
            $sql = "INSERT INTO monumentos (nome, local, data_inauguracao, descricao, imagem) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $nome, $localizacao, $data_inauguracao, $descricao, $imagem_path);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = $is_edit ? "Monumento atualizado com sucesso!" : "Monumento adicionado com sucesso!";
            // Redirecionar após um breve delay para mostrar a mensagem
            header("Refresh: 1; URL=index.php?tab=monumentos");
        } else {
            $error_message = "Erro ao " . ($is_edit ? "atualizar" : "adicionar") . " monumento: " . mysqli_error($conn);
        }
    }
}

// Incluir o cabeçalho
include_once '../templates/header.php';

// Gerar breadcrumbs
$breadcrumbs = generate_breadcrumbs([
    'Dashboard' => '../dashboard.php',
    'Legado' => 'index.php?tab=monumentos',
    ($is_edit ? 'Editar Monumento' : 'Novo Monumento') => '#'
]);
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="mb-0"><?php echo $is_edit ? 'Editar Monumento' : 'Novo Monumento'; ?></h1>
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
            <i class="bi bi-building-fill me-1"></i>
            Informações do Monumento
        </div>
        <div class="card-body">
            <form method="post" class="needs-validation" enctype="multipart/form-data" novalidate>
                <!-- Campo oculto para ID em caso de edição -->
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <?php endif; ?>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome do Monumento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['nome']) ? 'is-invalid' : ''; ?>" 
                               id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                        <?php if (isset($errors['nome'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['nome']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="localizacao" class="form-label">Localização <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['localizacao']) ? 'is-invalid' : ''; ?>" 
                               id="localizacao" name="localizacao" 
                               value="<?php echo htmlspecialchars($localizacao); ?>" 
                               placeholder="Ex: Lisboa, Portugal" required>
                        <?php if (isset($errors['localizacao'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['localizacao']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="ano" class="form-label">Ano de Inauguração <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?php echo isset($errors['ano']) ? 'is-invalid' : ''; ?>" 
                           id="ano" name="ano" min="1900" max="<?php echo date('Y'); ?>" 
                           value="<?php echo htmlspecialchars($ano); ?>" required>
                    <?php if (isset($errors['ano'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['ano']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                    <textarea class="form-control <?php echo isset($errors['descricao']) ? 'is-invalid' : ''; ?>" 
                              id="descricao" name="descricao" rows="5" required><?php echo htmlspecialchars($descricao); ?></textarea>
                    <?php if (isset($errors['descricao'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['descricao']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">Descreva o monumento, sua importância e relação com Bento de Jesus Caraça.</div>
                </div>
                
                <div class="mb-3">
                    <label for="imagem" class="form-label">Imagem do Monumento</label>
                    <input type="file" class="form-control <?php echo isset($errors['imagem']) ? 'is-invalid' : ''; ?>" 
                           id="imagem" name="imagem" accept=".jpg, .jpeg, .png, .gif">
                    <?php if (isset($errors['imagem'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['imagem']; ?></div>
                    <?php endif; ?>
                    <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamanho máximo: 2MB.</div>
                    
                    <?php if (!empty($imagem_atual)): ?>
                        <div class="mt-3">
                            <p>Imagem atual:</p>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo '../../' . htmlspecialchars($imagem_atual); ?>" 
                                     alt="Imagem atual" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <span class="ms-3 text-muted">
                                    Se você enviar uma nova imagem, a atual será substituída.
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?tab=monumentos" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> <?php echo $is_edit ? 'Atualizar' : 'Salvar'; ?> Monumento
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