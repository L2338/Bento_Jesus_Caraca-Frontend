<?php
/**
 * Upload de imagens para cursos
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Diretório de upload
$upload_dir = '../../assets/img/courses/';
$web_dir = 'assets/img/courses/';

// Verificar se o diretório existe, senão criar
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Processar upload
$success = false;
$error = '';
$image_path = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
    // Verificar tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['course_image']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $error = "Tipo de arquivo não permitido. Apenas imagens JPEG, PNG, GIF e WEBP são aceitas.";
    } else {
        // Obter extensão
        $extension = pathinfo($_FILES['course_image']['name'], PATHINFO_EXTENSION);
        
        // Gerar nome de arquivo único
        $filename = 'course-' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Mover arquivo
        if (move_uploaded_file($_FILES['course_image']['tmp_name'], $filepath)) {
            $success = true;
            $image_path = $web_dir . $filename;
        } else {
            $error = "Falha ao mover o arquivo carregado.";
        }
    }
    
    // Verificar se é uma solicitação AJAX (chamada pelo iframe)
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    if (!$is_ajax && isset($_GET['ajax'])) {
        $is_ajax = true;
    }
    
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'error' => $error,
            'image_path' => $image_path
        ]);
        exit;
    }
}

// Lista de imagens existentes
$images = [];
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $images[] = [
                'path' => $web_dir . $file,
                'name' => $file,
                'size' => filesize($upload_dir . $file)
            ];
        }
    }
}

// Ordenar por data de modificação (mais recente primeiro)
usort($images, function($a, $b) use ($upload_dir) {
    return filemtime($upload_dir . $b['name']) - filemtime($upload_dir . $a['name']);
});

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Escolas e Informações' => ADMIN_URL . 'escolas/index.php?tab=cursos',
    'Upload de Imagens' => '#'
]);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Upload de Nova Imagem</h6>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p>Imagem carregada com sucesso!</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="<?php echo $image_path; ?>" id="image-path" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyPath()">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                        <div class="text-center mb-3">
                            <img src="<?php echo '../../' . $image_path; ?>" class="img-fluid img-thumbnail" alt="Imagem carregada">
                        </div>
                        <a href="<?php echo ADMIN_URL; ?>escolas/index.php?tab=cursos" class="btn btn-primary btn-block">
                            <i class="bi bi-arrow-left me-1"></i> Voltar para Cursos
                        </a>
                    </div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="course_image" class="form-label">Selecione uma imagem:</label>
                        <input type="file" class="form-control" id="course_image" name="course_image" accept="image/*" required>
                        <div class="form-text">Formatos aceitos: JPG, PNG, GIF, WEBP</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Enviar Imagem
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Imagens Disponíveis</h6>
            </div>
            <div class="card-body">
                <?php if (empty($images)): ?>
                    <div class="alert alert-info">
                        Nenhuma imagem disponível. Faça upload de novas imagens.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Visualização</th>
                                    <th>Caminho</th>
                                    <th>Tamanho</th>
                                    <th>Copiar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($images as $image): ?>
                                <tr>
                                    <td style="width: 100px">
                                        <img src="<?php echo '../../' . $image['path']; ?>" class="img-fluid img-thumbnail" alt="<?php echo $image['name']; ?>">
                                    </td>
                                    <td>
                                        <code><?php echo $image['path']; ?></code>
                                    </td>
                                    <td>
                                        <?php echo formatFileSize($image['size']); ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?php echo $image['path']; ?>')">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Caminho copiado para a área de transferência!');
    }, function() {
        alert('Não foi possível copiar o texto');
    });
}

function copyPath() {
    const inputField = document.getElementById('image-path');
    inputField.select();
    document.execCommand('copy');
    alert('Caminho copiado para a área de transferência!');
}
</script>

<?php
// Função para formatar tamanho de arquivo
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?> 