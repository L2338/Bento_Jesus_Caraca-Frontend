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

// Incluir o cabeçalho
include_once 'templates/header.php';
?>

<!-- Cards de estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card stats-card-primary h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Obras</div>
                        <div class="stats-card-number">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book stats-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card stats-card-success h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Vida</div>
                        <div class="stats-card-number">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-lines-fill stats-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card stats-card-info h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Legado</div>
                        <div class="stats-card-number">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-award stats-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card stats-card-warning h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stats-card-text mb-1">Galeria</div>
                        <div class="stats-card-number">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-images stats-card-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo principal -->
<div class="row">
    <!-- Coluna principal -->
    <div class="col-lg-8 mb-4">
        <!-- Card de boas-vindas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
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
                <p class="lead">Olá, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrador'; ?></strong>!</p>
                <p>Bem-vindo ao painel administrativo do site Bento de Jesus Caraça. Este painel permite gerenciar todo o conteúdo do site, incluindo:</p>
                
                <div class="row mt-4 mb-2">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon bg-primary bg-gradient text-white rounded-3 me-3">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Obras Literárias</h5>
                                <p class="mb-0 text-muted">Cadastre e edite obras literárias</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon bg-success bg-gradient text-white rounded-3 me-3">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Vida</h5>
                                <p class="mb-0 text-muted">Gerencie a história de vida</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon bg-info bg-gradient text-white rounded-3 me-3">
                                <i class="bi bi-award"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Legado</h5>
                                <p class="mb-0 text-muted">Gerencie o legado histórico</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon bg-warning bg-gradient text-white rounded-3 me-3">
                                <i class="bi bi-images"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Galeria</h5>
                                <p class="mb-0 text-muted">Organize a galeria de imagens</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="mt-3">Use o menu lateral para navegar entre as diferentes seções do painel.</p>
                
                <div class="alert alert-info d-flex align-items-center mt-4 mb-0" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Dica:</strong> Você pode personalizar as cores e aparência deste painel na página de <a href="settings.php" class="alert-link">Configurações</a>.
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividades Recentes -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Atividades Recentes</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#">Ver Todas</a></li>
                        <li><a class="dropdown-item" href="#">Limpar Notificações</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-4 py-3 list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Sistema Inicializado</h6>
                                <p class="mb-0 text-muted small">O painel administrativo foi inicializado com sucesso.</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Agora</span>
                        </div>
                    </div>
                    <div class="list-group-item px-4 py-3 list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Login Realizado</h6>
                                <p class="mb-0 text-muted small">Login bem-sucedido no sistema administrativo.</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Hoje</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna lateral -->
    <div class="col-lg-4 mb-4">
        <!-- Links Rápidos -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Links Rápidos</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="obras/" class="btn btn-primary btn-block w-100">
                            <i class="bi bi-book me-2"></i> Obras
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="vida/" class="btn btn-success btn-block w-100">
                            <i class="bi bi-person-lines-fill me-2"></i> Vida
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="legado/" class="btn btn-info btn-block w-100 text-white">
                            <i class="bi bi-award me-2"></i> Legado
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="../index.php" target="_blank" class="btn btn-secondary btn-block w-100">
                            <i class="bi bi-eye me-2"></i> Ver Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status do Sistema -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status do Sistema</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="small font-weight-bold">Uso de Armazenamento <span class="float-end">20%</span></h5>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h5 class="small font-weight-bold">Cache do Site <span class="float-end">40%</span></h5>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h5 class="small font-weight-bold">Banco de Dados <span class="float-end">60%</span></h5>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <a href="#" class="btn btn-light btn-sm w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .feature-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>

<?php
// Incluir o rodapé
include_once 'templates/footer.php';
?> 