<?php
/**
 * Dashboard do painel administrativo
 */

// Incluir configurações e funções
require_once __DIR__ . '/config/app-config.php';
require_once __DIR__ . '/core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Dashboard';

// Definir breadcrumbs
$breadcrumbs = [
    ['url' => 'dashboard.php', 'titulo' => 'Dashboard']
];

// Obter totais para os cards de estatísticas
$conn = require_once __DIR__ . '/../ConfigBD.php';

// Contador de obras
$obrasQuery = "SELECT COUNT(*) as total FROM obras";
$obrasResult = $conn->query($obrasQuery);
$obrasTotal = ($obrasResult && $obrasResult->num_rows > 0) ? $obrasResult->fetch_assoc()['total'] : 0;

// Contador da timeline (vida)
$timelineQuery = "SELECT COUNT(*) as total FROM timeline_blocos";
$timelineResult = $conn->query($timelineQuery);
$timelineTotal = ($timelineResult && $timelineResult->num_rows > 0) ? $timelineResult->fetch_assoc()['total'] : 0;

// Contador de escolas (legado)
$escolasQuery = "SELECT COUNT(*) as total FROM escolas_profissionais";
$escolasResult = $conn->query($escolasQuery);
$escolasTotal = ($escolasResult && $escolasResult->num_rows > 0) ? $escolasResult->fetch_assoc()['total'] : 0;

// Contador de imagens (galeria)
$imagensQuery = "SELECT COUNT(*) as total FROM imagens";
$imagensResult = $conn->query($imagensQuery);
$imagensTotal = ($imagensResult && $imagensResult->num_rows > 0) ? $imagensResult->fetch_assoc()['total'] : 0;

// Obter data do último login
$ultimoLogin = isset($_SESSION['last_login']) ? date('d/m/Y H:i', $_SESSION['last_login']) : 'Primeira vez';

// Obter atividades recentes
$atividadesQuery = "SELECT 'obra' as tipo, titulo as nome, data_atualizacao, id as id_item 
                    FROM obras 
                    UNION 
                    SELECT 'timeline' as tipo, titulo as nome, data_atualizacao, id as id_item 
                    FROM timeline_blocos 
                    UNION 
                    SELECT 'escola' as tipo, nome, data_cadastro as data_atualizacao, id as id_item 
                    FROM escolas_profissionais 
                    ORDER BY data_atualizacao DESC 
                    LIMIT 8";
$atividadesResult = $conn->query($atividadesQuery);
$atividades = [];
if ($atividadesResult && $atividadesResult->num_rows > 0) {
    while ($row = $atividadesResult->fetch_assoc()) {
        $atividades[] = $row;
    }
}

// Extrair notificações não lidas (simulação - em produção seria uma tabela real)
// As 4 mais recentes atividades são consideradas não lidas para demonstração
$notificacoesNaoLidas = array_slice($atividades, 0, 3);
$totalNotificacoes = count($notificacoesNaoLidas);

// Incluir o cabeçalho
include_once 'templates/header.php';
?>

<style>
    /* Estilos personalizados para o dashboard */
    .stats-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .stats-card-icon {
        font-size: 2rem;
        opacity: 0.8;
    }
    
    .stats-card-text {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stats-card-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .feature-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }
    
    .welcome-badge {
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .quick-link-btn {
        border-radius: 10px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .quick-link-btn:hover {
        transform: translateY(-3px);
    }
    
    .quick-link-btn::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(255,255,255,0.1), rgba(255,255,255,0));
        z-index: -1;
    }
    
    .activity-item {
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .activity-item.obra {
        border-left-color: var(--bs-primary);
    }
    
    .activity-item.timeline {
        border-left-color: var(--bs-success);
    }
    
    .activity-item.escola {
        border-left-color: var(--bs-info);
    }
    
    .notification-dot {
        position: absolute;
        top: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background-color: var(--bs-danger);
        border-radius: 50%;
        border: 2px solid #fff;
        transform: translate(25%, -25%);
    }
    
    .content-summary-value {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
        background: linear-gradient(45deg, var(--bs-primary), var(--bs-primary-active, var(--bs-primary)));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .user-menu-dropdown {
        min-width: 280px;
    }
    
    .expand-guide-btn:focus {
        box-shadow: none;
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0.5);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(var(--bs-primary-rgb), 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0);
        }
    }
    
    /* Atualização para ícones nas cartas */
    .card-icon-wrapper {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 60px;
        height: 60px;
        border-radius: 15px;
    }
</style>

<!-- Injetar script para modificar o dropdown de notificações -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar contador de notificações no ícone
    const notificationCounter = document.getElementById('notification-counter');
    if (notificationCounter) {
        notificationCounter.textContent = '<?php echo $totalNotificacoes; ?>';
    }
    
    // Animar elementos
    const animateItems = document.querySelectorAll('.animate-on-load');
    animateItems.forEach((item, index) => {
        setTimeout(() => {
            item.classList.add('show');
        }, 100 * index);
    });
});
</script>

<!-- Cards de estatísticas -->
<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card stats-card-primary h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Obras</div>
                        <div class="stats-card-number"><?php echo $obrasTotal; ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="card-icon-wrapper bg-primary bg-opacity-10">
                            <i class="bi bi-book stats-card-icon text-primary"></i>
                        </div>
                    </div>
                </div>
                <a href="obras/" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card stats-card-success h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Vida</div>
                        <div class="stats-card-number"><?php echo $timelineTotal; ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="card-icon-wrapper bg-success bg-opacity-10">
                            <i class="bi bi-person-lines-fill stats-card-icon text-success"></i>
                        </div>
                    </div>
                </div>
                <a href="vida/" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card stats-card-info h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Escolas</div>
                        <div class="stats-card-number"><?php echo $escolasTotal; ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="card-icon-wrapper bg-info bg-opacity-10">
                            <i class="bi bi-award stats-card-icon text-info"></i>
                        </div>
                    </div>
                </div>
                <a href="escolas/" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card stats-card-warning h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Galeria</div>
                        <div class="stats-card-number"><?php echo $imagensTotal; ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="card-icon-wrapper bg-warning bg-opacity-10">
                            <i class="bi bi-images stats-card-icon text-warning"></i>
                        </div>
                    </div>
                </div>
                <a href="galeria/" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo principal -->
<div class="row g-4">
    <!-- Coluna principal -->
    <div class="col-lg-8">
        <!-- Card de boas-vindas -->
        <div class="card shadow mb-4 border-0 rounded-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Bem-vindo ao Painel Administrativo</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="welcomeMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="welcomeMenuLink">
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i> Ajuda</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded-circle p-3 me-3 pulse-animation">
                        <i class="bi bi-person-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h4 class="mb-0 me-2">Olá, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrador'; ?></strong>!</h4>
                            <span class="badge bg-primary bg-opacity-10 text-primary welcome-badge">
                                Administrador
                            </span>
                        </div>
                        <p class="text-muted mb-0">Último acesso: <?php echo $ultimoLogin; ?></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="border-start border-primary ps-2 mb-3" style="border-left-width: 3px!important;">Resumo do Sistema</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Conteúdo Completo</h6>
                                    <p class="mb-0 small text-muted">
                                        Obras: <?php echo $obrasTotal; ?> | 
                                        Timeline: <?php echo $timelineTotal; ?> | 
                                        Escolas: <?php echo $escolasTotal; ?> | 
                                        Imagens: <?php echo $imagensTotal; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="mb-2"><i class="bi bi-clock-fill text-info me-2"></i> Status Atual</h6>
                                    <p class="mb-0 small text-muted">
                                        Sistema funcionando normalmente<br>
                                        Versão: 1.0.0
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <!-- Removido o botão "Visualizar o site publicado" -->
                </div>
            </div>
        </div>

        <!-- Guia de Uso -->
        <div class="card shadow mb-4 border-0 rounded-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Guia Rápido de Uso</h6>
                <button class="btn btn-sm btn-outline-primary expand-guide-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuide" aria-expanded="false" aria-controls="collapseGuide">
                    <i class="bi bi-chevron-down"></i> Expandir
                </button>
            </div>
            <div class="collapse" id="collapseGuide">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-1-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Navegação Básica</h6>
                                    <p class="small text-muted">Use o menu lateral à esquerda para navegar entre as diferentes seções do painel. Cada seção possui funcionalidades específicas para gerenciar os conteúdos do site.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-2-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Gerenciando Obras</h6>
                                    <p class="small text-muted">Acesse "Obras" para adicionar, editar ou remover obras literárias. Você pode fazer upload de PDFs, adicionar imagens de capa e categorizar por temas.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-3-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Timeline e Biografia</h6>
                                    <p class="small text-muted">Na seção "Vida", você pode gerenciar a biografia e a timeline. Adicione eventos cronológicos, edite textos e organize a ordem dos blocos.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-4-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Escolas e Instituições</h6>
                                    <p class="small text-muted">Gerencie as informações das escolas profissionais na seção "Escolas", incluindo endereços, contatos e cursos oferecidos.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-5-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Galeria de Imagens</h6>
                                    <p class="small text-muted">Use a seção "Galeria" para fazer upload e organizar imagens, que podem ser utilizadas em várias seções do site.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary rounded-circle text-white p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-6-circle-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold">Personalizações</h6>
                                    <p class="small text-muted">Acesse "Configurações" para personalizar a aparência do painel administrativo, incluindo cores e tema.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna lateral -->
    <div class="col-lg-4">
        <!-- Links Rápidos -->
        <div class="card shadow mb-4 border-0 rounded-3">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Links Rápidos</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="obras/" class="btn btn-primary btn-block w-100 quick-link-btn py-3">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-book fs-3 mb-2"></i>
                                <span>Obras</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="vida/" class="btn btn-success btn-block w-100 quick-link-btn py-3">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-person-lines-fill fs-3 mb-2"></i>
                                <span>Vida</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="escolas/" class="btn btn-info btn-block w-100 quick-link-btn py-3 text-white">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-buildings fs-3 mb-2"></i>
                                <span>Escolas</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="galeria/" class="btn btn-warning btn-block w-100 quick-link-btn py-3 text-white">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-images fs-3 mb-2"></i>
                                <span>Galeria</span>
                            </div>
                        </a>
                    </div>
                </div>
                
                <hr>
                
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <a href="settings.php" class="btn btn-light btn-block w-100 py-2 border">
                            <i class="bi bi-gear me-2"></i> Configurações
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Atividades Recentes e Notificações -->
        <div class="card shadow mb-4 border-0 rounded-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Atividades Recentes</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i> Ver Todas</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-bell-slash me-2"></i> Marcar Todas como Lidas</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($atividades)): ?>
                    <div class="list-group-item px-4 py-3 list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Sistema Inicializado</h6>
                                <p class="mb-0 text-muted small">O painel administrativo foi inicializado com sucesso.</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Agora</span>
                        </div>
                    </div>
                    <?php else: ?>
                        <?php foreach ($atividades as $index => $atividade): ?>
                            <?php
                            // Definir links de destino para cada tipo
                            $link = '#';
                            switch ($atividade['tipo']) {
                                case 'obra':
                                    $link = 'obras/index.php?id=' . $atividade['id_item'];
                                    break;
                                case 'timeline':
                                    $link = 'vida/timeline_edit.php?id=' . $atividade['id_item'];
                                    break;
                                case 'escola':
                                    $link = 'escolas/index.php?id=' . $atividade['id_item'];
                                    break;
                            }
                            ?>
                            <a href="<?php echo $link; ?>" class="list-group-item px-4 py-3 list-group-item-action activity-item <?php echo $atividade['tipo']; ?> <?php echo $index < 3 ? 'fw-bold' : ''; ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php 
                                            if ($atividade['tipo'] === 'obra'):
                                                echo '<i class="bi bi-book me-2 text-primary"></i>';
                                            elseif ($atividade['tipo'] === 'timeline'):
                                                echo '<i class="bi bi-calendar-event me-2 text-success"></i>';
                                            elseif ($atividade['tipo'] === 'escola'):
                                                echo '<i class="bi bi-building me-2 text-info"></i>';
                                            endif;
                                            
                                            echo htmlspecialchars($atividade['nome']);
                                            
                                            if ($index < 3) {
                                                echo ' <span class="badge bg-danger bg-opacity-10 text-danger small">Novo</span>';
                                            }
                                            ?>
                                        </h6>
                                        <p class="mb-0 text-muted small">
                                            <?php 
                                            echo 'Atualizado em: ' . date('d/m/Y H:i', strtotime($atividade['data_atualizacao']));
                                            ?>
                                        </p>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Adicionar código para modificar o dropdown de notificações no header.php
// Isso será adicionado pelo JavaScript para implementar corretamente

// Incluir o script para sobrescrever o dropdown de notificações no header
?>

<!-- Script para implementar modificações dinâmicas no dropdown de notificações -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Localizar o menu de notificações atual
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    if (notificationsDropdown) {
        // Adicionar indicador de notificações não lidas
        if (<?php echo $totalNotificacoes; ?> > 0) {
            const notificationDot = document.createElement('span');
            notificationDot.className = 'notification-dot';
            notificationDot.id = 'notification-counter';
            notificationsDropdown.appendChild(notificationDot);
        }
        
        // Personalizar o conteúdo do dropdown
        const dropdownMenu = notificationsDropdown.nextElementSibling;
        if (dropdownMenu) {
            dropdownMenu.classList.add('user-menu-dropdown');
            
            // Limpar e adicionar novo conteúdo ao dropdown
            dropdownMenu.innerHTML = `
                <li><h6 class="dropdown-header">Notificações Recentes</h6></li>
                <li><hr class="dropdown-divider"></li>
                <?php foreach ($notificacoesNaoLidas as $index => $notificacao): ?>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <?php if ($notificacao['tipo'] === 'obra'): ?>
                            <div class="me-3">
                                <div class="bg-primary text-white rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_atualizacao'])); ?></div>
                                <span>Nova obra: <?php echo htmlspecialchars($notificacao['nome']); ?></span>
                            </div>
                        <?php elseif ($notificacao['tipo'] === 'timeline'): ?>
                            <div class="me-3">
                                <div class="bg-success text-white rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_atualizacao'])); ?></div>
                                <span>Novo evento: <?php echo htmlspecialchars($notificacao['nome']); ?></span>
                            </div>
                        <?php elseif ($notificacao['tipo'] === 'escola'): ?>
                            <div class="me-3">
                                <div class="bg-info text-white rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-building"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_atualizacao'])); ?></div>
                                <span>Escola atualizada: <?php echo htmlspecialchars($notificacao['nome']); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small" href="#">Ver todas notificações</a></li>
            `;
        }
    }
});
</script>

<?php
include_once 'templates/footer.php';
?> 