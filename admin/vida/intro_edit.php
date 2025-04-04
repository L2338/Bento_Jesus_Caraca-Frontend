<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Configuração da página
$pageTitle = "Editar Texto Introdutório";
$currentSection = "vida";

// Buscar texto introdutório existente
$introQuery = "SELECT id, conteudo FROM textos_secoes WHERE chave = 'vida_introducao' LIMIT 1";
$introResult = $conn->query($introQuery);

$introText = '';
$introId = 0;
$textoExists = false;

if ($introResult && $introResult->num_rows > 0) {
    $introRow = $introResult->fetch_assoc();
    $introText = $introRow['conteudo'];
    $introId = $introRow['id'];
    $textoExists = true;
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_texto'])) {
    $conteudo = $_POST['conteudo'];
    
    // Verificar se já existe um registro para o texto de introdução
    $checkQuery = "SELECT id FROM textos_secoes WHERE chave = 'vida_introducao'";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        // Atualizar o texto existente
        $id = $checkResult->fetch_assoc()['id'];
        $updateQuery = "UPDATE textos_secoes SET conteudo = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $conteudo, $id);
        $stmt->execute();
    } else {
        // Inserir novo texto
        $insertQuery = "INSERT INTO textos_secoes (chave, conteudo) VALUES ('vida_introducao', ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("s", $conteudo);
        $stmt->execute();
    }
    
    // Redirecionar com mensagem de sucesso
    header("Location: index.php?success=intro_updated");
    exit;
}

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;"><?php echo $pageTitle; ?></h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
    
    <!-- Alertas de feedback -->
    <?php if (isset($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
            <i class="bi bi-file-text me-2 text-primary"></i>
            <h6 class="m-0 fw-bold text-primary">Texto Introdutório da Página Vida</h6>
        </div>
        <div class="card-body">
            <p class="mb-4">
                Este texto aparece no início da página "Vida e Obra", apresentando uma introdução sobre Bento de Jesus Caraça. Utilize o editor abaixo para modificar este conteúdo.
            </p>
            
            <form action="" method="POST">
                <!-- Editor de texto simplificado -->
                <div class="mb-4">
                    <label for="editor" class="form-label">Conteúdo</label>
                    <div class="card mb-2">
                        <div class="card-header bg-light py-2">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')"><i class="bi bi-type-underline"></i></button>
                                </div>
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('p')"><i class="bi bi-paragraph"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('h2')">H2</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('h3')">H3</button>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('ul')"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('ol')"><i class="bi bi-list-ol"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <textarea id="editor" name="conteudo" class="form-control" rows="12" style="min-height: 300px;"><?php echo htmlspecialchars($introText); ?></textarea>
                    <small class="form-text text-muted mt-2">Você pode utilizar HTML básico para formatação. Exemplo: &lt;p&gt;Parágrafo&lt;/p&gt;, &lt;b&gt;Negrito&lt;/b&gt;, &lt;i&gt;Itálico&lt;/i&gt;.</small>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="salvar_texto" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Texto
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Prévia do conteúdo -->
    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
            <i class="bi bi-eye me-2 text-primary"></i>
            <h6 class="m-0 fw-bold text-primary">Prévia do Conteúdo</h6>
        </div>
        <div class="card-body">
            <div class="preview-container border p-3 rounded bg-light">
                <div id="texto-preview">
                    <?php if (!empty($introText)): ?>
                        <?php echo $introText; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">A prévia do texto aparecerá aqui.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para o editor simplificado -->
<script>
function formatText(command) {
    var textarea = document.getElementById('editor');
    var selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
    var newText = '';
    
    switch(command) {
        case 'bold':
            newText = '<b>' + selectedText + '</b>';
            break;
        case 'italic':
            newText = '<i>' + selectedText + '</i>';
            break;
        case 'underline':
            newText = '<u>' + selectedText + '</u>';
            break;
        case 'p':
            newText = '<p>' + selectedText + '</p>';
            break;
        case 'h2':
            newText = '<h2>' + selectedText + '</h2>';
            break;
        case 'h3':
            newText = '<h3>' + selectedText + '</h3>';
            break;
        case 'ul':
            newText = '<ul>\n  <li>' + selectedText.split('\n').join('</li>\n  <li>') + '</li>\n</ul>';
            break;
        case 'ol':
            newText = '<ol>\n  <li>' + selectedText.split('\n').join('</li>\n  <li>') + '</li>\n</ol>';
            break;
    }
    
    // Substituir o texto selecionado pelo novo texto formatado
    textarea.value = textarea.value.substring(0, textarea.selectionStart) + 
                    newText + 
                    textarea.value.substring(textarea.selectionEnd);
    
    // Atualizar a prévia
    updatePreview();
}

function updatePreview() {
    var content = document.getElementById('editor').value;
    document.getElementById('texto-preview').innerHTML = content;
}

// Adicionar listener para atualizar a prévia quando o conteúdo mudar
document.getElementById('editor').addEventListener('input', updatePreview);
</script>

<?php
include "../../admin/templates/footer.php";
?> 