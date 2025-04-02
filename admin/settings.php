<?php
/**
 * Configurações do painel administrativo
 */

// Incluir configurações e funções
require_once __DIR__ . '/config/app-config.php';
require_once __DIR__ . '/core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Configurações';

// Definir breadcrumbs
$breadcrumbs = [
    ['url' => 'dashboard.php', 'titulo' => 'Dashboard'],
    ['url' => 'settings.php', 'titulo' => 'Configurações']
];

// Cores disponíveis
$theme_colors = [
    // Cores da EPBJC
    'epbjc_red' => ['name' => 'Vermelho EPBJC', 'primary' => '#ac062a', 'gradient_start' => '#ac062a', 'gradient_end' => '#750418'],
    
    // Cores das escolas
    'barreiro' => ['name' => 'Barreiro', 'primary' => '#00bcd4', 'gradient_start' => '#00bcd4', 'gradient_end' => '#0097a7'],
    'porto' => ['name' => 'Porto', 'primary' => '#0072c6', 'gradient_start' => '#0072c6', 'gradient_end' => '#005299'],
    'beja' => ['name' => 'Beja', 'primary' => '#ff9800', 'gradient_start' => '#ff9800', 'gradient_end' => '#f57c00'],
    'lisboa' => ['name' => 'Lisboa', 'primary' => '#4caf50', 'gradient_start' => '#4caf50', 'gradient_end' => '#388e3c'],
    'seixal' => ['name' => 'Seixal', 'primary' => '#009688', 'gradient_start' => '#009688', 'gradient_end' => '#00796b'],
    
    // Cores padrão do sistema
    'blue' => ['name' => 'Azul', 'primary' => '#4e73df', 'gradient_start' => '#4e73df', 'gradient_end' => '#224abe'],
    'green' => ['name' => 'Verde', 'primary' => '#1cc88a', 'gradient_start' => '#1cc88a', 'gradient_end' => '#13855c'],
    'red' => ['name' => 'Vermelho', 'primary' => '#e74a3b', 'gradient_start' => '#e74a3b', 'gradient_end' => '#be2617'],
    'purple' => ['name' => 'Roxo', 'primary' => '#7952b3', 'gradient_start' => '#7952b3', 'gradient_end' => '#543b7e'],
    'orange' => ['name' => 'Laranja', 'primary' => '#fd7e14', 'gradient_start' => '#fd7e14', 'gradient_end' => '#c46a0f'],
    'teal' => ['name' => 'Turquesa', 'primary' => '#20c9a6', 'gradient_start' => '#20c9a6', 'gradient_end' => '#178066'],
    'dark' => ['name' => 'Escuro', 'primary' => '#5a5c69', 'gradient_start' => '#5a5c69', 'gradient_end' => '#373840'],
];

// Backgrounds disponíveis
$backgrounds = [
    'light' => 'Claro (Padrão)',
    'dark' => 'Escuro',
    'gray' => 'Cinza Suave',
];

// Processar formulário
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['theme_action']) && $_POST['theme_action'] === 'save_theme') {
        
        // Salvar as preferências no cookie (em um sistema real seria no banco de dados)
        $selected_theme = isset($_POST['theme_color']) && array_key_exists($_POST['theme_color'], $theme_colors) ? $_POST['theme_color'] : 'blue';
        $selected_background = isset($_POST['theme_background']) && array_key_exists($_POST['theme_background'], $backgrounds) ? $_POST['theme_background'] : 'light';
        
        // Definir cookies (30 dias)
        setcookie('admin_theme_color', $selected_theme, time() + (86400 * 30), '/');
        setcookie('admin_theme_background', $selected_background, time() + (86400 * 30), '/');
        
        $success_message = 'Configurações de tema salvas com sucesso! As mudanças serão aplicadas na próxima atualização da página.';
    }
}

// Obter temas atuais
$current_theme = isset($_COOKIE['admin_theme_color']) ? $_COOKIE['admin_theme_color'] : 'blue';
$current_background = isset($_COOKIE['admin_theme_background']) ? $_COOKIE['admin_theme_background'] : 'light';

// Incluir o cabeçalho
include_once 'templates/header.php';
?>

<!-- Conteúdo da página -->
<div class="row">
    <div class="col-lg-8">
        <!-- Card de Personalização do Tema -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Personalização do Tema</h6>
            </div>
            <div class="card-body">
                <?php if ($success_message): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <form method="post" action="">
                    <input type="hidden" name="theme_action" value="save_theme">
                    
                    <div class="mb-4">
                        <h5 class="mb-3">Cor do Tema</h5>
                        
                        <!-- Cor institucional principal -->
                        <div class="mb-3">
                            <p class="fw-bold mb-2"><i class="bi bi-star-fill me-1"></i> Cor Institucional EPBJC</p>
                            <div class="row">
                                <?php 
                                $main_color = array_slice($theme_colors, 0, 1);
                                foreach ($main_color as $key => $color): 
                                ?>
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme_color" 
                                            id="theme_color_<?php echo $key; ?>" value="<?php echo $key; ?>"
                                            <?php echo ($current_theme === $key) ? 'checked' : ''; ?>>
                                        <label class="form-check-label d-flex align-items-center" for="theme_color_<?php echo $key; ?>">
                                            <span class="color-preview me-2" style="background: linear-gradient(180deg, <?php echo $color['gradient_start']; ?> 10%, <?php echo $color['gradient_end']; ?> 100%);"></span>
                                            <?php echo $color['name']; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Cores das escolas -->
                        <div class="mb-3">
                            <p class="fw-bold mb-2"><i class="bi bi-buildings me-1"></i> Cores das Escolas</p>
                            <div class="row">
                                <?php 
                                $school_colors = array_slice($theme_colors, 1, 5);
                                foreach ($school_colors as $key => $color): 
                                ?>
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme_color" 
                                            id="theme_color_<?php echo $key; ?>" value="<?php echo $key; ?>"
                                            <?php echo ($current_theme === $key) ? 'checked' : ''; ?>>
                                        <label class="form-check-label d-flex align-items-center" for="theme_color_<?php echo $key; ?>">
                                            <span class="color-preview me-2" style="background: linear-gradient(180deg, <?php echo $color['gradient_start']; ?> 10%, <?php echo $color['gradient_end']; ?> 100%);"></span>
                                            <?php echo $color['name']; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Outras cores -->
                        <div>
                            <p class="fw-bold mb-2"><i class="bi bi-palette me-1"></i> Outras Cores</p>
                            <div class="row">
                                <?php 
                                $other_colors = array_slice($theme_colors, 6);
                                foreach ($other_colors as $key => $color): 
                                ?>
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme_color" 
                                            id="theme_color_<?php echo $key; ?>" value="<?php echo $key; ?>"
                                            <?php echo ($current_theme === $key) ? 'checked' : ''; ?>>
                                        <label class="form-check-label d-flex align-items-center" for="theme_color_<?php echo $key; ?>">
                                            <span class="color-preview me-2" style="background: linear-gradient(180deg, <?php echo $color['gradient_start']; ?> 10%, <?php echo $color['gradient_end']; ?> 100%);"></span>
                                            <?php echo $color['name']; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3">Fundo do Painel</h5>
                        <div class="row">
                            <?php foreach ($backgrounds as $key => $name): ?>
                            <div class="col-6 col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme_background" 
                                        id="theme_background_<?php echo $key; ?>" value="<?php echo $key; ?>"
                                        <?php echo ($current_background === $key) ? 'checked' : ''; ?>>
                                    <label class="form-check-label d-flex align-items-center" for="theme_background_<?php echo $key; ?>">
                                        <span class="bg-preview me-2 bg-<?php echo $key; ?>"></span>
                                        <?php echo $name; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Visualização de temas -->
                    <div class="mb-4">
                        <h5 class="mb-3">Visualização</h5>
                        <div class="theme-preview p-3 border rounded">
                            <div class="d-flex theme-preview-header align-items-center mb-3">
                                <div class="theme-preview-sidebar me-3"></div>
                                <div class="theme-preview-content flex-grow-1">
                                    <div class="theme-preview-navbar mb-2"></div>
                                    <div class="d-flex gap-2 mb-2">
                                        <div class="theme-preview-card flex-grow-1"></div>
                                        <div class="theme-preview-card flex-grow-1"></div>
                                        <div class="theme-preview-card flex-grow-1"></div>
                                        <div class="theme-preview-card flex-grow-1"></div>
                                    </div>
                                    <div class="theme-preview-card p-2 mb-2">
                                        <div class="theme-preview-text"></div>
                                        <div class="theme-preview-text w-75"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="small text-muted text-center">Esta é uma visualização simplificada do tema selecionado</div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Card de Outras Configurações -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Outras Configurações</h6>
            </div>
            <div class="card-body">
                <p>Configure outras opções do sistema.</p>
                
                <form method="post" action="">
                    <!-- Configurações futuras serão adicionadas aqui -->
                    <div class="mb-3">
                        <label for="site_title" class="form-label">Título do Site</label>
                        <input type="text" class="form-control" id="site_title" name="site_title" value="Bento de Jesus Caraça" disabled>
                        <small class="text-muted">Esta função será implementada em breve.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="items_per_page" class="form-label">Itens por Página</label>
                        <select class="form-select" id="items_per_page" name="items_per_page" disabled>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <small class="text-muted">Esta função será implementada em breve.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Coluna Lateral -->
    <div class="col-lg-4">
        <!-- Card de Informações -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informações do Sistema</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold">Versão do Sistema</h6>
                    <p>1.0.0 Beta</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold">PHP Version</h6>
                    <p><?php echo phpversion(); ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold">Último Login</h6>
                    <p><?php echo isset($_SESSION['last_login']) ? date('d/m/Y H:i:s', $_SESSION['last_login']) : 'Não disponível'; ?></p>
                </div>
                
                <a href="#" class="btn btn-info w-100">
                    <i class="bi bi-arrow-repeat me-2"></i> Verificar Atualizações
                </a>
            </div>
        </div>
        
        <!-- Card de Ajuda -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ajuda & Suporte</h6>
            </div>
            <div class="card-body">
                <p>Precisa de ajuda com o painel administrativo?</p>
                <a href="#" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-question-circle me-2"></i> Documentação
                </a>
                <a href="#" class="btn btn-outline-success w-100">
                    <i class="bi bi-envelope me-2"></i> Contatar Suporte
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .color-preview {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ddd;
    }
    
    .bg-preview {
        width: 25px;
        height: 25px;
        border-radius: 4px;
        display: inline-block;
        border: 1px solid #ddd;
    }
    
    .bg-light {
        background-color: #f8f9fc;
    }
    
    .bg-dark {
        background-color: #2d3748;
    }
    
    .bg-gray {
        background-color: #eaecf4;
    }
    
    /* Preview de tema */
    .theme-preview {
        background-color: var(--background-color, #f8f9fc);
        transition: all 0.3s ease;
    }
    
    .theme-preview-sidebar {
        width: 40px;
        height: 150px;
        background: linear-gradient(180deg, var(--primary-color, #4e73df) 10%, var(--sidebar-gradient-end, #224abe) 100%);
        border-radius: 4px;
    }
    
    .theme-preview-navbar {
        height: 20px;
        background-color: var(--card-bg, #fff);
        border-radius: 4px;
        border: 1px solid var(--border-color, #e3e6f0);
    }
    
    .theme-preview-card {
        height: 40px;
        background-color: var(--card-bg, #fff);
        border-radius: 4px;
        border: 1px solid var(--border-color, #e3e6f0);
    }
    
    .theme-preview-text {
        height: 10px;
        background-color: var(--card-text, #333);
        opacity: 0.7;
        border-radius: 3px;
        margin-bottom: 5px;
    }
    
    /* Script para atualizar a visualização enquanto o usuário seleciona cores */
    input[name="theme_color"], input[name="theme_background"] {
        cursor: pointer;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[name="theme_color"]');
    const bgInputs = document.querySelectorAll('input[name="theme_background"]');
    const preview = document.querySelector('.theme-preview');
    
    // Cores dos temas
    const themeColors = <?php echo json_encode($theme_colors); ?>;
    const backgrounds = {
        'light': '#f8f9fc',
        'dark': '#2d3748',
        'gray': '#eaecf4'
    };
    
    // Definir cores iniciais
    updatePreview();
    
    // Atualizar quando a cor muda
    colorInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
    });
    
    // Atualizar quando o fundo muda
    bgInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
    });
    
    function updatePreview() {
        const selectedColor = document.querySelector('input[name="theme_color"]:checked').value;
        const selectedBg = document.querySelector('input[name="theme_background"]:checked').value;
        
        // Definir cores do tema
        const primary = themeColors[selectedColor].primary;
        const gradientEnd = themeColors[selectedColor].gradient_end;
        const bg = backgrounds[selectedBg];
        
        // Definir cores do preview
        preview.style.setProperty('--primary-color', primary);
        preview.style.setProperty('--sidebar-gradient-end', gradientEnd);
        preview.style.setProperty('--background-color', bg);
        
        // Definir cores para os cards e textos
        if (selectedBg === 'dark') {
            preview.style.setProperty('--card-bg', '#2d3748');
            preview.style.setProperty('--card-text', '#e2e8f0');
            preview.style.setProperty('--border-color', '#4a5568');
        } else {
            preview.style.setProperty('--card-bg', '#ffffff');
            preview.style.setProperty('--card-text', '#333333');
            preview.style.setProperty('--border-color', '#e3e6f0');
        }
    }
});
</script>

<?php
// Incluir o rodapé
include_once 'templates/footer.php';
?> 