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

// Processar o formulário de edição se enviado
$errors = [];
$success = false;
$imagemPreview = '';
$debug_image = []; // Adicionar variável para debug

// Definir o diretório de upload de imagens
$uploadDir = "../../assets/images/timeline/";

// Verificar se o diretório existe, se não, criar
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adicionar log de debug para verificar os dados recebidos
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Validar campos obrigatórios
    $titulo = trim($_POST['titulo'] ?? '');
    $data_periodo = trim($_POST['data_periodo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $conteudo_expandido = trim($_POST['conteudo_expandido'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Verificar campos obrigatórios
    if (empty($titulo)) {
        $errors[] = "O título é obrigatório.";
    }
    
    if (empty($data_periodo)) {
        $errors[] = "A data/período é obrigatória.";
    } elseif (!preg_match('/^[0-9\-]+$/', $data_periodo)) {
        $errors[] = "A data/período deve conter apenas números e hífens.";
    }
    
    if (empty($conteudo)) {
        $errors[] = "O conteúdo resumido é obrigatório.";
    }
    
    if (empty($conteudo_expandido)) {
        $errors[] = "O conteúdo expandido é obrigatório.";
    }
    
    // Processar upload de imagem se houver
    $imagem_nome = $block['imagem'] ?? ''; // Manter a imagem atual se não for atualizada
    $debug_image['original'] = $imagem_nome; // Debug - imagem original
    
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK && !empty($_FILES['imagem']['name'])) {
        $tempFile = $_FILES['imagem']['tmp_name'];
        $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        
        $debug_image['uploaded'] = true;
        $debug_image['type'] = $imageFileType;
        $debug_image['temp_file'] = $tempFile;
        
        // Verificações simplificadas
        $allowedTypes = ["jpg", "jpeg", "png"];
        
        // Validar imagem em uma única etapa
        if (!getimagesize($tempFile)) {
            $errors[] = "O arquivo enviado não é uma imagem válida.";
            $debug_image['error'] = "Não é uma imagem válida";
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $errors[] = "Apenas arquivos JPG, JPEG e PNG são permitidos.";
            $debug_image['error'] = "Formato não permitido";
        } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
            $errors[] = "O tamanho da imagem não pode exceder 3MB.";
            $debug_image['error'] = "Tamanho excede 3MB";
        } else {
            // Verificar dimensões apenas se as validações anteriores passarem
            list($width, $height) = getimagesize($tempFile);
            $debug_image['dimensions'] = "$width x $height";
            
            if ($width < 200 || $height < 200) {
                $errors[] = "A imagem deve ter no mínimo 200x200 pixels.";
                $debug_image['error'] = "Dimensões muito pequenas";
            } elseif ($width > 800 || $height > 800) {
                $errors[] = "A imagem não pode exceder 800x800 pixels.";
                $debug_image['error'] = "Dimensões muito grandes";
            } else {
                // Processar o upload apenas se não houver erros
                $imagem_nome = 'timeline_' . $blockId . '_' . time() . '.' . $imageFileType;
                $targetFile = $uploadDir . $imagem_nome;
                $debug_image['new_name'] = $imagem_nome;
                $debug_image['target_path'] = $targetFile;
                
                if (move_uploaded_file($tempFile, $targetFile)) {
                    $debug_image['upload_success'] = true;
                    error_log("Imagem salva com sucesso: " . $targetFile);
                    
                    // Remover imagem antiga se existir e for diferente
                    if (!empty($block['imagem']) && $block['imagem'] != $imagem_nome && file_exists($uploadDir . $block['imagem'])) {
                        @unlink($uploadDir . $block['imagem']);
                        $debug_image['old_image_deleted'] = true;
                    }
                } else {
                    $errors[] = "Erro ao fazer upload da imagem. Verifique as permissões do diretório.";
                    $debug_image['upload_success'] = false;
                    $debug_image['php_error'] = error_get_last();
                    error_log("Erro ao salvar imagem: " . error_get_last()['message'] ?? 'Erro desconhecido');
                }
            }
        }
    } elseif (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Registrar erros de upload diferentes de "nenhum arquivo enviado"
        $uploadErrors = [
            1 => "O tamanho do arquivo excede o limite permitido pelo servidor.",
            2 => "O tamanho do arquivo excede o limite especificado no formulário HTML.",
            3 => "O arquivo foi apenas parcialmente carregado.",
            4 => "Nenhum arquivo foi carregado.",
            6 => "Pasta temporária ausente.",
            7 => "Falha ao escrever o arquivo no disco.",
            8 => "Uma extensão PHP interrompeu o upload do arquivo."
        ];
        $errorCode = $_FILES['imagem']['error'];
        $errors[] = "Erro no upload da imagem: " . ($uploadErrors[$errorCode] ?? "Erro desconhecido (código $errorCode)");
        $debug_image['upload_error_code'] = $errorCode;
    } elseif (isset($_POST['remover_imagem']) && $_POST['remover_imagem'] == '1') {
        // Remover imagem existente
        if (!empty($block['imagem']) && file_exists($uploadDir . $block['imagem'])) {
            @unlink($uploadDir . $block['imagem']);
            $debug_image['removed'] = true;
        }
        $imagem_nome = '';
        $debug_image['cleared'] = true;
    } else {
        $debug_image['no_change'] = true;
    }
    
    // Se não houver erros, atualizar o bloco
    if (empty($errors)) {
        // Escapar dados para evitar SQL injection
        $titulo = $conn->real_escape_string($titulo);
        $data_periodo = $conn->real_escape_string($data_periodo);
        $conteudo = $conn->real_escape_string($conteudo);
        $conteudo_expandido = $conn->real_escape_string($conteudo_expandido);
        $imagem_nome = $conn->real_escape_string($imagem_nome);
        
        // Atualizar o bloco
        $query = "UPDATE timeline_blocos SET 
                  titulo = '$titulo', 
                  data_periodo = '$data_periodo', 
                  conteudo = '$conteudo', 
                  conteudo_expandido = '$conteudo_expandido',
                  imagem = '$imagem_nome',
                  ativo = $ativo,
                  data_atualizacao = NOW()
                  WHERE id = $blockId";
        
        error_log("SQL Query: " . $query);
        
        if ($conn->query($query)) {
            $success = true;
            error_log("Bloco atualizado com sucesso. ID: $blockId, Imagem: $imagem_nome");
            
            // Força a limpeza do cache
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            // Redirecionar após atualização bem-sucedida
            header("Location: timeline_list.php?success=edit&nocache=" . time());
            exit;
        } else {
            $errors[] = "Erro ao atualizar bloco: " . $conn->error;
            error_log("Erro SQL: " . $conn->error);
        }
    } else {
        error_log("Erros encontrados: " . print_r($errors, true));
    }
    
    // Salvar debug em arquivo para análise posterior
    error_log("Debug de imagem: " . print_r($debug_image, true));
}

// Buscar dados atuais do bloco
$query = "SELECT * FROM timeline_blocos WHERE id = $blockId";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    header("Location: timeline_list.php?error=block_not_found");
    exit;
}

$block = $result->fetch_assoc();

// Verificar se existe uma imagem para exibir prévia
if (!empty($block['imagem']) && file_exists($uploadDir . $block['imagem'])) {
    $imagemPreview = '../../../assets/images/timeline/' . $block['imagem'];
}

// Configuração da página
$pageTitle = "Editar Bloco: " . htmlspecialchars($block['titulo']);
$currentSection = "vida";

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<!-- Estilos adicionais para a página -->
<style>
    .ck-editor__editable {
        min-height: 200px;
    }
    
    /* Estilos para validação de formulário */
    .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-check-input.is-invalid {
        border-color: #dc3545;
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    .was-validated .form-control:invalid ~ .invalid-feedback,
    .was-validated .form-check-input:invalid ~ .invalid-feedback,
    .form-control.is-invalid ~ .invalid-feedback,
    .form-check-input.is-invalid ~ .invalid-feedback {
        display: block;
    }
    
    /* Estilos para prévia de imagem */
    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #eee;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .image-preview-container {
        position: relative;
        display: inline-block;
    }
    
    .remove-image-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        padding: 0;
        line-height: 24px;
        text-align: center;
        font-size: 12px;
    }
    
    .timeline-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background-color: #f8f9fa;
        border: 2px dashed #ccc;
        overflow: hidden;
        position: relative;
    }
    
    .timeline-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .timeline-circle-placeholder {
        color: #aaa;
        font-size: 40px;
    }
</style>

<div class="container-fluid p-4">
    <!-- Cabeçalho da página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;">
            <?php echo $pageTitle; ?>
        </h1>
        <div>
            <a href="timeline_view.php?id=<?php echo $blockId; ?>" class="btn btn-info btn-sm me-2">
                <i class="bi bi-eye"></i> Visualizar
            </a>
            <a href="timeline_list.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <!-- Alertas de erro -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Ocorreram erros:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Debug info - mostrar apenas durante o desenvolvimento -->
    <?php if (isset($debug_image) && !empty($debug_image)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size: 0.9rem">
        <strong>Informações de Debug:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($debug_image as $key => $value): ?>
                <li><strong><?php echo htmlspecialchars($key); ?>:</strong> 
                    <?php 
                        if (is_array($value)) {
                            echo '<pre>' . htmlspecialchars(print_r($value, true)) . '</pre>';
                        } else {
                            echo htmlspecialchars($value); 
                        }
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Alerta de sucesso -->
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Bloco atualizado com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Formulário de edição -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <i class="bi bi-pencil-square me-2 text-primary"></i>
            <h6 class="m-0 fw-bold text-primary">Formulário de Edição</h6>
        </div>
        <div class="card-body">
            <form id="blockForm" method="POST" action="timeline_edit.php?id=<?php echo $blockId; ?>" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo in_array("O título é obrigatório.", $errors) ? 'is-invalid' : ''; ?>" 
                                   id="titulo" name="titulo" value="<?php echo htmlspecialchars($block['titulo']); ?>" required>
                            <div class="invalid-feedback">O título é obrigatório.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="data_periodo" class="form-label">Data/Período <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo (in_array("A data/período é obrigatória.", $errors) || in_array("A data/período deve conter apenas números e hífens.", $errors)) ? 'is-invalid' : ''; ?>" 
                                   id="data_periodo" name="data_periodo" 
                                   value="<?php echo htmlspecialchars($block['data_periodo']); ?>" 
                                   placeholder="Ex: 1948 ou 1901-1948" required>
                            <div class="invalid-feedback">
                                <?php 
                                if (in_array("A data/período deve conter apenas números e hífens.", $errors)) {
                                    echo "A data/período deve conter apenas números e hífens.";
                                } else {
                                    echo "A data/período é obrigatória.";
                                }
                                ?>
                            </div>
                            <small class="text-muted">Use apenas números e hífens (ex: 1948 ou 1901-1948)</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                       <?php echo $block['ativo'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ativo">
                                    <span class="text-success">Ativo</span> / <span class="text-secondary">Inativo</span>
                                </label>
                            </div>
                            <small class="text-muted">Blocos inativos não aparecem na timeline</small>
                        </div>
                    </div>
                </div>
                
                <!-- Adicionar campo para upload de imagem -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold">Imagem do Evento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="timeline-circle mb-3">
                                            <?php if (!empty($imagemPreview)): ?>
                                                <img src="<?php echo $imagemPreview; ?>?v=<?php echo time(); ?>" id="imagePreview" alt="Prévia da imagem">
                                            <?php else: ?>
                                                <div class="timeline-circle-placeholder" id="placeholderIcon">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                                <img src="" id="imagePreview" style="display: none;" alt="Prévia da imagem">
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($imagemPreview)): ?>
                                            <div class="form-check ms-4 mb-3">
                                                <input class="form-check-input" type="checkbox" id="remover_imagem" name="remover_imagem" value="1">
                                                <label class="form-check-label" for="remover_imagem">
                                                    Remover imagem
                                                </label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="mb-3">
                                            <label for="imagem" class="form-label">Selecione uma imagem</label>
                                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/jpg, image/png">
                                            <div class="form-text">
                                                Imagem para círculo (180x180). Tamanho: 200-800px (máx 3MB).
                                            </div>
                                            <?php if (in_array("O arquivo enviado não é uma imagem válida.", $errors) || 
                                                    in_array("Apenas arquivos JPG, JPEG e PNG são permitidos.", $errors) ||
                                                    in_array("O tamanho da imagem não pode exceder 3MB.", $errors) ||
                                                    in_array("A imagem deve ter no mínimo 200x200 pixels.", $errors) ||
                                                    in_array("A imagem não pode exceder 800x800 pixels.", $errors)): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?php 
                                                    if (in_array("O arquivo enviado não é uma imagem válida.", $errors)) {
                                                        echo "O arquivo enviado não é uma imagem válida.";
                                                    } elseif (in_array("Apenas arquivos JPG, JPEG e PNG são permitidos.", $errors)) {
                                                        echo "Apenas arquivos JPG, JPEG e PNG são permitidos.";
                                                    } elseif (in_array("O tamanho da imagem não pode exceder 3MB.", $errors)) {
                                                        echo "O tamanho da imagem não pode exceder 3MB.";
                                                    } elseif (in_array("A imagem deve ter no mínimo 200x200 pixels.", $errors)) {
                                                        echo "A imagem deve ter no mínimo 200x200 pixels.";
                                                    } elseif (in_array("A imagem não pode exceder 800x800 pixels.", $errors)) {
                                                        echo "A imagem não pode exceder 800x800 pixels.";
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="conteudo" class="form-label">Conteúdo Resumido <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo in_array("O conteúdo resumido é obrigatório.", $errors) ? 'is-invalid' : ''; ?>" 
                                      id="conteudo" name="conteudo" rows="5" required><?php echo htmlspecialchars($block['conteudo']); ?></textarea>
                            <div class="invalid-feedback">O conteúdo resumido é obrigatório.</div>
                            <small class="text-muted">Este conteúdo é exibido inicialmente na timeline</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="conteudo_expandido" class="form-label">Conteúdo Expandido <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo in_array("O conteúdo expandido é obrigatório.", $errors) ? 'is-invalid' : ''; ?>" 
                                      id="conteudo_expandido" name="conteudo_expandido" rows="5" required><?php echo htmlspecialchars($block['conteudo_expandido']); ?></textarea>
                            <div class="invalid-feedback">O conteúdo expandido é obrigatório.</div>
                            <small class="text-muted">Este conteúdo é exibido quando o usuário clica em "Saiba mais"</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-light border py-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                                <div>
                                    <strong>Informações adicionais:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Data de criação: <?php echo date('d/m/Y H:i', strtotime($block['data_criacao'])); ?></li>
                                        <li>Última atualização: <?php echo date('d/m/Y H:i', strtotime($block['data_atualizacao'])); ?></li>
                                        <li>Ordem na timeline: <?php echo $block['ordem']; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="timeline_list.php" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="bi bi-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inclusão do CKEditor para melhorar a edição de conteúdo -->
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar editores de texto rico
    ClassicEditor
        .create(document.querySelector('#conteudo'))
        .catch(error => {
            console.error('Erro ao inicializar editor de conteúdo:', error);
        });
    
    ClassicEditor
        .create(document.querySelector('#conteudo_expandido'))
        .catch(error => {
            console.error('Erro ao inicializar editor de conteúdo expandido:', error);
        });
    
    // Prévia da imagem simplificada
    const imageInput = document.getElementById('imagem');
    const imagePreview = document.getElementById('imagePreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    const removeImageCheckbox = document.getElementById('remover_imagem');
    
    // Garantir que o formulário tenha o atributo correto para upload de arquivos
    const form = document.getElementById('blockForm');
    if (form && form.getAttribute('enctype') !== 'multipart/form-data') {
        console.error('Formulário sem atributo enctype correto!');
        form.setAttribute('enctype', 'multipart/form-data');
    }
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            console.log('Imagem selecionada:', this.files);
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('Imagem carregada na prévia');
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        if (placeholderIcon) placeholderIcon.style.display = 'none';
                    }
                    if (removeImageCheckbox) removeImageCheckbox.checked = false;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Simplificar a gestão de remoção de imagem
    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                if (imagePreview) imagePreview.style.display = 'none';
                if (placeholderIcon) placeholderIcon.style.display = 'block';
                if (imageInput) imageInput.value = '';
            } else if (imagePreview && imagePreview.src) {
                imagePreview.style.display = 'block';
                if (placeholderIcon) placeholderIcon.style.display = 'none';
            }
        });
    }
    
    // Validação do formulário do lado do cliente
    const submitButton = document.getElementById('submitButton');
    const statusSwitch = document.getElementById('ativo');
    const originalStatus = <?php echo $block['ativo'] ? 'true' : 'false'; ?>;
    let statusChanged = false;
    
    // Verificar se o status foi alterado sem mostrar mensagem imediata
    statusSwitch.addEventListener('change', function() {
        statusChanged = (this.checked !== originalStatus);
    });
    
    form.addEventListener('submit', function(event) {
        if (imageInput && imageInput.files.length > 0) {
            console.log('Imagem será enviada:', imageInput.files[0].name);
            const fileSize = imageInput.files[0].size;
            const fileName = imageInput.files[0].name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            console.log(`Validando imagem: ${fileName} (${fileSize} bytes, extensão: ${fileExt})`);
            
            if (!['jpg', 'jpeg', 'png'].includes(fileExt)) {
                alert('Apenas arquivos JPG, JPEG e PNG são permitidos.');
                event.preventDefault();
                return false;
            }
            
            if (fileSize > 3 * 1024 * 1024) {
                alert('O arquivo é muito grande. O tamanho máximo permitido é 3MB.');
                event.preventDefault();
                return false;
            }
        }
        
        if (!validateForm()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            // Verificar se o status foi alterado e mostrar mensagem personalizada
            if (statusChanged) {
                event.preventDefault();
                
                // Criar e mostrar modal Bootstrap
                const statusText = statusSwitch.checked ? 'ativar' : 'desativar';
                const modalHtml = `
                    <div class="modal fade" id="statusChangeModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Confirmar Alteração de Status
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Você está prestes a <strong>${statusText}</strong> este bloco da timeline.</p>
                                    <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <div>
                                            ${statusSwitch.checked 
                                                ? 'Blocos ativos são exibidos na timeline pública.'
                                                : 'Blocos inativos não aparecem na timeline pública.'}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x"></i> Cancelar
                                    </button>
                                    <button type="button" id="confirmStatusChange" class="btn btn-primary">
                                        <i class="bi bi-check2"></i> Confirmar e Salvar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Adicionar modal ao DOM
                const modalContainer = document.createElement('div');
                modalContainer.innerHTML = modalHtml;
                document.body.appendChild(modalContainer);
                
                // Mostrar modal
                const statusModal = new bootstrap.Modal(document.getElementById('statusChangeModal'));
                statusModal.show();
                
                // Configurar ação de confirmação
                document.getElementById('confirmStatusChange').addEventListener('click', function() {
                    statusModal.hide();
                    
                    // Continuar com o envio do formulário após confirmação
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
                    form.submit();
                });
                
                // Limpar modal ao fechar
                document.getElementById('statusChangeModal').addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modalContainer);
                });
            } else {
                // Se não houve mudança de status, apenas enviar o formulário
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
            }
        }
        
        form.classList.add('was-validated');
    });
    
    // Validar campos do formulário
    function validateForm() {
        let isValid = true;
        
        // Validar título
        const titulo = document.getElementById('titulo');
        if (!titulo.value.trim()) {
            titulo.classList.add('is-invalid');
            isValid = false;
        } else {
            titulo.classList.remove('is-invalid');
        }
        
        // Validar data/período
        const dataPeriodo = document.getElementById('data_periodo');
        const dataPeriodoValue = dataPeriodo.value.trim();
        const dataPeriodoRegex = /^[0-9\-]+$/;
        
        if (!dataPeriodoValue) {
            dataPeriodo.classList.add('is-invalid');
            dataPeriodo.nextElementSibling.textContent = 'A data/período é obrigatória.';
            isValid = false;
        } else if (!dataPeriodoRegex.test(dataPeriodoValue)) {
            dataPeriodo.classList.add('is-invalid');
            dataPeriodo.nextElementSibling.textContent = 'A data/período deve conter apenas números e hífens.';
            isValid = false;
        } else {
            dataPeriodo.classList.remove('is-invalid');
        }
        
        // Validação adicional para conteúdos (CKEditor já fará isso)
        
        return isValid;
    }
});
</script>

<?php
include "../../admin/templates/footer.php";
?> 