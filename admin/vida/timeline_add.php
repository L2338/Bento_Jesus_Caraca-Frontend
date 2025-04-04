<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Configuração da página
$pageTitle = "Adicionar Bloco na Timeline";
$currentSection = "vida";

// Definir o diretório de upload
$uploadDir = "../../assets/images/timeline/";

// Verificar se o diretório existe, se não, criar
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Inicializar variáveis
$imagem_nome = '';
$upload_message = '';
$upload_status = '';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['imagem']['tmp_name'];
        $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        
        // Validações em sequência lógica
        $allowedTypes = ["jpg", "jpeg", "png"];
        
        if (!getimagesize($tempFile)) {
            $errors[] = "O arquivo enviado não é uma imagem válida.";
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $errors[] = "Apenas arquivos JPG, JPEG e PNG são permitidos.";
        } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
            $errors[] = "O tamanho da imagem não pode exceder 3MB.";
        } else {
            // Verificar dimensões
            list($width, $height) = getimagesize($tempFile);
            if ($width < 200 || $height < 200) {
                $errors[] = "A imagem deve ter no mínimo 200x200 pixels.";
            } elseif ($width > 800 || $height > 800) {
                $errors[] = "A imagem não pode exceder 800x800 pixels.";
            }
            // O upload real é feito após inserir o registro para ter o ID
        }
    }
    
    // Obter dados do formulário
    $titulo = trim($_POST['titulo'] ?? '');
    $data_periodo = trim($_POST['data_periodo'] ?? '');
    $conteudo = trim($_POST['conteudo'] ?? '');
    $conteudo_expandido = trim($_POST['conteudo_expandido'] ?? '');
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Validação básica
    $errors = [];
    if (empty($titulo)) $errors[] = "O título é obrigatório.";
    if (empty($data_periodo)) {
        $errors[] = "A data/período é obrigatória.";
    } elseif (!preg_match('/^[0-9\-]+$/', $data_periodo)) {
        $errors[] = "A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.";
    }
    if (empty($conteudo)) $errors[] = "O conteúdo resumido é obrigatório.";
    if (empty($conteudo_expandido)) $errors[] = "O conteúdo expandido é obrigatório.";
    
    // Se não houver erros, inserir no banco
    if (empty($errors)) {
        // Determinar a próxima ordem
        $orderQuery = "SELECT MAX(ordem) as max_ordem FROM timeline_blocos";
        $orderResult = $conn->query($orderQuery);
        $maxOrdem = $orderResult->fetch_assoc()['max_ordem'];
        $ordem = $maxOrdem ? $maxOrdem + 1 : 1;
        
        // Escapar dados para evitar SQL injection
        $titulo = $conn->real_escape_string($titulo);
        $data_periodo = $conn->real_escape_string($data_periodo);
        $conteudo = $conn->real_escape_string($conteudo);
        $conteudo_expandido = $conn->real_escape_string($conteudo_expandido);
        
        // Inserir o bloco primeiro para obter o ID
        $query = "INSERT INTO timeline_blocos (titulo, data_periodo, conteudo, conteudo_expandido, ordem, ativo, data_criacao, data_atualizacao) 
                  VALUES ('$titulo', '$data_periodo', '$conteudo', '$conteudo_expandido', $ordem, $ativo, NOW(), NOW())";
        
        if ($conn->query($query)) {
            $blockId = $conn->insert_id;
            
            // Upload simplificado após ter o ID
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imageFileType = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                $imagem_nome = 'timeline_' . $blockId . '_' . time() . '.' . $imageFileType;
                $targetFile = $uploadDir . $imagem_nome;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetFile)) {
                    // Atualizar o registro com o nome da imagem
                    $imagem_nome = $conn->real_escape_string($imagem_nome);
                    $updateQuery = "UPDATE timeline_blocos SET imagem = '$imagem_nome' WHERE id = $blockId";
                    $conn->query($updateQuery);
                }
            }
            
            // Redirecionar para a lista com mensagem de sucesso
            header("Location: timeline_list.php?success=add");
            exit;
        } else {
            $errorMessage = "Erro ao adicionar bloco: " . $conn->error;
        }
    } else {
        $errorMessage = "Por favor, corrija os seguintes erros:<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
    }
}

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<!-- Estilos personalizados para melhorar a interface -->
<style>
    /* Fontes e tipografia mais coerentes */
    body {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    /* Formulário mais limpo */
    .form-control, .form-select {
        border-radius: 0.375rem;
        border-color: #ced4da;
        padding: 0.5rem 0.75rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Estilos para validação */
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    .was-validated .form-control:invalid ~ .invalid-feedback,
    .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }
    
    /* Melhorias no editor */
    .editor-container {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.5rem;
        margin-bottom: 1rem;
    }
    .editor-toolbar {
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ced4da;
        margin-bottom: 0.5rem;
    }
    .editor-toolbar button {
        background: none;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-right: 0.25rem;
        padding: 0.25rem 0.5rem;
        cursor: pointer;
    }
    .editor-toolbar button:hover {
        background-color: #e9ecef;
    }
    
    /* Área de upload de imagem */
    .image-upload-container {
        border: 2px dashed #ced4da;
        border-radius: 0.375rem;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .image-upload-container:hover {
        border-color: #86b7fe;
        background-color: #f8f9fc;
    }
    .image-preview {
        max-width: 100%;
        height: auto;
        max-height: 200px;
        margin-top: 1rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
    .image-preview-container {
        text-align: center;
        margin-top: 1rem;
    }
    
    /* Botões mais consistentes */
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
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
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;"><?php echo $pageTitle; ?></h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Lista
        </a>
    </div>
    
    <!-- Alertas de erro -->
    <?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Alerta de upload -->
    <?php if (!empty($upload_message)): ?>
    <div class="alert alert-<?php echo $upload_status; ?> alert-dismissible fade show" role="alert">
        <?php echo $upload_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <!-- Formulário -->
    <form action="timeline_add.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Coluna da Esquerda - Dados Principais -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
                        <h6 class="m-0 fw-bold text-primary">Informações do Bloco</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Título -->
                        <div class="mb-4">
                            <label for="titulo" class="form-label fw-bold">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors) && in_array('O título é obrigatório.', $errors) ? 'is-invalid' : ''; ?>" 
                                   id="titulo" name="titulo" required
                                   value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>">
                            <div class="invalid-feedback">O título é obrigatório.</div>
                        </div>
                        
                        <!-- Data/Período -->
                        <div class="mb-4">
                            <label for="data_periodo" class="form-label fw-bold">Data/Período <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors) && (in_array('A data/período é obrigatória.', $errors) || in_array('A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.', $errors)) ? 'is-invalid' : ''; ?>" 
                                   id="data_periodo" name="data_periodo" required
                                   value="<?php echo isset($_POST['data_periodo']) ? htmlspecialchars($_POST['data_periodo']) : ''; ?>"
                                   placeholder="Ex: 1948 ou 1901-1948">
                            <div class="invalid-feedback">
                                <?php 
                                if (isset($errors) && in_array('A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.', $errors)) {
                                    echo 'A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.';
                                } else {
                                    echo 'A data/período é obrigatória.';
                                }
                                ?>
                            </div>
                            <small class="text-muted">Use apenas números e hífens (ex: 1948 ou 1901-1948)</small>
                        </div>
                        
                        <!-- Upload de Imagem -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Imagem do Evento</label>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="timeline-circle mb-3">
                                        <div class="timeline-circle-placeholder" id="placeholderIcon">
                                            <i class="bi bi-image"></i>
                                        </div>
                                        <img src="" id="imagePreview" style="display: none;" alt="Prévia da imagem">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <label for="imagem" class="form-label">Selecione uma imagem</label>
                                        <input type="file" class="form-control" id="imagem" name="imagem" accept="image/jpeg, image/jpg, image/png">
                                        <div class="form-text">
                                            Imagem para círculo (180x180). Tamanho: 200-800px (máx 3MB).
                                        </div>
                                        <?php if (isset($errors) && (in_array("O arquivo enviado não é uma imagem válida.", $errors) || 
                                                in_array("Apenas arquivos JPG, JPEG e PNG são permitidos.", $errors) ||
                                                in_array("O tamanho da imagem não pode exceder 3MB.", $errors) ||
                                                in_array("A imagem deve ter no mínimo 200x200 pixels.", $errors) ||
                                                in_array("A imagem não pode exceder 800x800 pixels.", $errors))): ?>
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
                        
                        <!-- Conteúdo Resumido -->
                        <div class="mb-4">
                            <label for="conteudo" class="form-label fw-bold">Conteúdo Resumido <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php echo isset($errors) && in_array('O conteúdo resumido é obrigatório.', $errors) ? 'is-invalid' : ''; ?>" 
                                      id="conteudo" name="conteudo" rows="4" required><?php echo isset($_POST['conteudo']) ? htmlspecialchars($_POST['conteudo']) : ''; ?></textarea>
                            <div class="invalid-feedback">O conteúdo resumido é obrigatório.</div>
                            <div class="form-text">Versão curta do conteúdo (exibida inicialmente na timeline).</div>
                        </div>
                        
                        <!-- Conteúdo Expandido -->
                        <div class="mb-4">
                            <label for="conteudo_expandido" class="form-label fw-bold">Conteúdo Expandido <span class="text-danger">*</span></label>
                            <div class="editor-container <?php echo isset($errors) && in_array('O conteúdo expandido é obrigatório.', $errors) ? 'border-danger' : ''; ?>">
                                <div class="editor-toolbar">
                                    <button type="button" onclick="formatText('bold')"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" onclick="formatText('italic')"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" onclick="formatText('underline')"><i class="bi bi-type-underline"></i></button>
                                    <button type="button" onclick="addHeading()"><i class="bi bi-type-h1"></i></button>
                                    <button type="button" onclick="addLink()"><i class="bi bi-link"></i></button>
                                    <button type="button" onclick="addList()"><i class="bi bi-list-ul"></i></button>
                                </div>
                                <textarea class="form-control <?php echo isset($errors) && in_array('O conteúdo expandido é obrigatório.', $errors) ? 'is-invalid' : ''; ?>" 
                                          id="conteudo_expandido" name="conteudo_expandido" rows="8" required><?php echo isset($_POST['conteudo_expandido']) ? htmlspecialchars($_POST['conteudo_expandido']) : ''; ?></textarea>
                            </div>
                            <div class="invalid-feedback">O conteúdo expandido é obrigatório.</div>
                            <div class="form-text">Versão detalhada do conteúdo (exibida ao expandir o bloco).</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coluna da Direita - Configurações e Preview -->
            <div class="col-lg-4">
                <!-- Card de Configurações -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
                        <h6 class="m-0 fw-bold text-primary">Configurações</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Upload de Imagem (versão compacta) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Imagem do Bloco</label>
                            <div class="d-flex">
                                <div class="image-upload-container flex-grow-1" id="dropZone" onclick="document.getElementById('imagem').click()" style="height: 80px; padding: 0.75rem;">
                                    <i class="bi bi-cloud-arrow-up text-primary"></i>
                                    <span class="small d-block">Clique para upload</span>
                                    <input type="file" class="d-none" id="imagem" name="imagem" accept="image/jpeg, image/jpg, image/png">
                                </div>
                                <button type="button" class="btn btn-outline-primary ms-2" id="preview-btn" onclick="openPreviewModal()">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="image-preview-container" class="image-preview-container d-none mt-2">
                                <div class="d-flex align-items-center">
                                    <img id="image-preview" class="image-preview" src="#" alt="Preview" style="max-height: 60px; max-width: 80px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeImage()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-text">Formatos permitidos: JPG, JPEG, PNG.</div>
                        </div>
                        
                        <!-- Status -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="ativo" name="ativo"
                                   <?php echo !isset($_POST['ativo']) || $_POST['ativo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="ativo">Bloco ativo</label>
                            <div class="form-text">Desmarque para manter o bloco inativo (não será exibido no site).</div>
                        </div>
                        
                        <!-- Ordem -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ordem na Timeline</label>
                            <div class="alert alert-info py-2 px-3 mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <span class="small">O novo bloco será adicionado ao final da timeline. Você poderá reorganizar a ordem posteriormente.</span>
                            </div>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2" id="btn-salvar">
                                <i class="bi bi-save me-2"></i> Salvar Bloco
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary py-2">
                                <i class="bi bi-x-circle me-2"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Visualização -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Prévia do Bloco</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="timeline-preview border p-3 rounded bg-light">
                    <div class="timeline-date mb-3 p-2 bg-primary text-white text-center rounded">
                        <span id="modal-preview-data">Data/Período</span>
                    </div>
                    <h4 id="modal-preview-titulo" class="mb-3">Título do Bloco</h4>
                    <div id="modal-preview-image-container" class="mb-3 text-center d-none">
                        <img id="modal-preview-image" class="img-fluid rounded" style="max-height: 300px;" src="#" alt="Imagem do evento">
                    </div>
                    <div class="mb-3">
                        <h6 class="border-bottom pb-2 mb-2">Conteúdo Resumido:</h6>
                        <p id="modal-preview-conteudo">O conteúdo resumido será exibido aqui.</p>
                    </div>
                    <div>
                        <h6 class="border-bottom pb-2 mb-2">Conteúdo Expandido:</h6>
                        <div id="modal-preview-expandido">O conteúdo expandido será exibido aqui.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário antes de enviar
    const form = document.querySelector('form');
    const submitButton = document.getElementById('btn-salvar');
    
    // Desabilitar duplo clique no botão de salvar
    submitButton.addEventListener('click', function() {
        setTimeout(() => {
            if (form.checkValidity()) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
            }
        }, 0);
    });
    
    form.addEventListener('submit', validateForm);
    
    // Função para validar o formulário
    function validateForm(e) {
        let isValid = true;
        const fields = [
            { id: 'titulo', message: 'O título é obrigatório.' },
            { id: 'conteudo', message: 'O conteúdo resumido é obrigatório.' },
            { id: 'conteudo_expandido', message: 'O conteúdo expandido é obrigatório.', container: '.editor-container' }
        ];
        
        // Validar campos obrigatórios
        fields.forEach(field => {
            const element = document.getElementById(field.id);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                if (field.container) {
                    document.querySelector(field.container).classList.add('border-danger');
                }
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
                if (field.container) {
                    document.querySelector(field.container).classList.remove('border-danger');
                }
            }
        });
        
        // Validar data/período especificamente
        const dataPeriodo = document.getElementById('data_periodo');
        if (!dataPeriodo.value.trim()) {
            dataPeriodo.classList.add('is-invalid');
            isValid = false;
        } else if (!/^[0-9\-]+$/.test(dataPeriodo.value.trim())) {
            // Verificar se contém apenas números e hífen
            dataPeriodo.classList.add('is-invalid');
            dataPeriodo.nextElementSibling.textContent = "A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.";
            isValid = false;
        } else {
            dataPeriodo.classList.remove('is-invalid');
        }
        
        // Impedir envio se houver erros
        if (!isValid) {
            e.preventDefault();
            
            // Scroll até o primeiro erro
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            
            // Mostrar alerta
            showAlert('Por favor, corrija os erros no formulário antes de continuar.', 'danger');
        }
    }
    
    // Função para mostrar alertas
    function showAlert(message, type = 'danger') {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        `;
        const formContainer = document.querySelector('.container-fluid');
        const existingAlert = formContainer.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        formContainer.insertAdjacentHTML('afterbegin', alertHTML);
    }
    
    // Validação em tempo real
    ['titulo', 'data_periodo', 'conteudo', 'conteudo_expandido'].forEach(id => {
        const element = document.getElementById(id);
        element.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                
                if (id === 'data_periodo' && !/^[0-9\-]+$/.test(this.value.trim())) {
                    this.classList.add('is-invalid');
                    this.nextElementSibling.textContent = "A data deve conter apenas números e hífen. Ex: 1970 ou 1901-1910.";
                } else if (id === 'conteudo_expandido') {
                    document.querySelector('.editor-container').classList.remove('border-danger');
                }
            }
        });
    });
    
    // Drag and drop para upload de imagem
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('imagem');
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-primary');
    });
    
    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('border-primary');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-primary');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            previewImage(e.dataTransfer.files[0]);
        }
    });
    
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length) {
            previewImage(fileInput.files[0]);
        }
    });
    
    // Função para prévia da imagem
    function previewImage(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-container').classList.remove('d-none');
                document.getElementById('modal-preview-image').src = e.target.result;
                document.getElementById('modal-preview-image-container').classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }
    
    // Inicializar o preview com uma imagem existente, se houver
    const imagemNome = document.getElementById('imagem').value;
    if (imagemNome) {
        document.getElementById('image-preview').src = imagemNome;
        document.getElementById('image-preview-container').classList.remove('d-none');
        document.getElementById('modal-preview-image').src = imagemNome;
        document.getElementById('modal-preview-image-container').classList.remove('d-none');
    }
    
    // Eventos para atualizar os dados no modal
    document.getElementById('titulo').addEventListener('input', updateModalPreview);
    document.getElementById('data_periodo').addEventListener('input', updateModalPreview);
    document.getElementById('conteudo').addEventListener('input', updateModalPreview);
    document.getElementById('conteudo_expandido').addEventListener('input', updateModalPreview);
    
    // Inicializar a prévia
    updateModalPreview();
    
    // Simplificar o código de preview de imagem
    const imageInput = document.getElementById('imagem');
    const imagePreview = document.getElementById('imagePreview');
    const placeholderIcon = document.getElementById('placeholderIcon');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Atualizar todas as prévias com uma única leitura
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        if (placeholderIcon) placeholderIcon.style.display = 'none';
                    }
                    
                    // Atualizar prévia no modal também
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-preview-container').classList.remove('d-none');
                    document.getElementById('modal-preview-image').src = e.target.result;
                    document.getElementById('modal-preview-image-container').classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});

// Função simplificada para remover imagem
function removeImage() {
    document.getElementById('imagem').value = '';
    document.getElementById('image-preview-container').classList.add('d-none');
    document.getElementById('modal-preview-image-container').classList.add('d-none');
    
    // Limpar também a prévia circular
    if (document.getElementById('imagePreview')) {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('placeholderIcon').style.display = 'block';
    }
}

// Atualizar os dados no modal de prévia
function updateModalPreview() {
    const titulo = document.getElementById('titulo').value || 'Título do Bloco';
    const data = document.getElementById('data_periodo').value || 'Data/Período';
    const conteudo = document.getElementById('conteudo').value || 'O conteúdo resumido será exibido aqui.';
    const expandido = document.getElementById('conteudo_expandido').value || 'O conteúdo expandido será exibido aqui.';
    
    document.getElementById('modal-preview-titulo').textContent = titulo;
    document.getElementById('modal-preview-data').textContent = data;
    document.getElementById('modal-preview-conteudo').textContent = conteudo;
    document.getElementById('modal-preview-expandido').innerHTML = expandido;
}

// Abrir o modal de prévia
function openPreviewModal() {
    updateModalPreview();
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();
}

// Funções consolidadas para formatação de texto
function formatText(command) {
    const textarea = document.getElementById('conteudo_expandido');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    let replacement = '';
    
    switch(command) {
        case 'bold':
            replacement = '<strong>' + selectedText + '</strong>';
            break;
        case 'italic':
            replacement = '<em>' + selectedText + '</em>';
            break;
        case 'underline':
            replacement = '<u>' + selectedText + '</u>';
            break;
        case 'heading':
            replacement = '<h3>' + selectedText + '</h3>';
            break;
        case 'list':
            replacement = '\n<ul>\n  <li>Item 1</li>\n  <li>Item 2</li>\n  <li>Item 3</li>\n</ul>\n';
            break;
        case 'link':
            const url = prompt('Digite a URL do link:', 'https://');
            if (url) {
                replacement = '<a href="' + url + '">' + (selectedText || url) + '</a>';
            } else {
                return; // Cancelado pelo usuário
            }
            break;
    }
    
    insertFormatting(textarea, start, end, replacement);
    updateModalPreview();
}

function insertFormatting(textarea, start, end, text) {
    textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(end);
    textarea.focus();
    
    // Se selecionou texto, posiciona cursor após o texto formatado
    if (start !== end) {
        textarea.selectionStart = start + text.length;
        textarea.selectionEnd = start + text.length;
    }
}

// Simplificar as funções de formatação para usar a função consolidada
function addHeading() {
    formatText('heading');
}

function addLink() {
    formatText('link');
}

function addList() {
    formatText('list');
}
</script>

<?php
include "../../admin/templates/footer.php";
?> 