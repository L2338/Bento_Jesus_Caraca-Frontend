<?php
/**
 * Vida - Gerenciamento da história de vida de Bento de Jesus Caraça
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Vida';

// Definir breadcrumbs
$breadcrumbs = [
    ['url' => '../dashboard.php', 'titulo' => 'Dashboard'],
    ['url' => 'index.php', 'titulo' => 'Vida']
];

// Incluir o cabeçalho
include_once '../templates/header.php';
?>

<div class="container-fluid">
    <!-- Cabeçalho da página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vida de Bento de Jesus Caraça</h1>
        <a href="adicionar.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Adicionar Evento
        </a>
    </div>

    <!-- Conteúdo principal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Linha do Tempo</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-filter me-2"></i> Filtrar</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-sort-down me-2"></i> Ordenar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i> Exportar</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Timeline vertical -->
                    <div class="timeline-vertical">
                        <!-- Exemplo de eventos -->
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">1901</div>
                                <div class="timeline-item-marker-indicator bg-primary"></div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="timeline-item-content-header">
                                    <h5 class="mb-0">Nascimento em Baldio</h5>
                                    <small class="text-muted">18 de Abril de 1901</small>
                                </div>
                                <p>Bento de Jesus Caraça nasceu em Baldio, Vila Viçosa, filho de João António Caraça e Maria do Rosário.</p>
                                <div class="btn-group" role="group" aria-label="Ações">
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i> Editar</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Excluir</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">1919</div>
                                <div class="timeline-item-marker-indicator bg-info"></div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="timeline-item-content-header">
                                    <h5 class="mb-0">Ingresso no Instituto Superior de Comércio</h5>
                                    <small class="text-muted">Outubro de 1919</small>
                                </div>
                                <p>Ingressou no Instituto Superior de Comércio, atual ISEG - Instituto Superior de Economia e Gestão, em Lisboa.</p>
                                <div class="btn-group" role="group" aria-label="Ações">
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i> Editar</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Excluir</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-item-marker">
                                <div class="timeline-item-marker-text">1948</div>
                                <div class="timeline-item-marker-indicator bg-warning"></div>
                            </div>
                            <div class="timeline-item-content">
                                <div class="timeline-item-content-header">
                                    <h5 class="mb-0">Falecimento em Lisboa</h5>
                                    <small class="text-muted">25 de Junho de 1948</small>
                                </div>
                                <p>Faleceu em Lisboa, aos 47 anos, vítima de doença cardíaca.</p>
                                <div class="btn-group" role="group" aria-label="Ações">
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i> Editar</a>
                                    <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Excluir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mensagem quando não há eventos cadastrados -->
                    <div class="text-center py-5 d-none">
                        <i class="bi bi-calendar3 display-4 text-gray-300 mb-3"></i>
                        <p class="text-gray-500 mb-0">Não há eventos cadastrados.</p>
                        <a href="adicionar.php" class="btn btn-sm btn-primary mt-3">
                            <i class="bi bi-plus-circle me-1"></i> Adicionar Primeiro Evento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para a timeline vertical */
.timeline-vertical {
    position: relative;
    padding-left: 3rem;
    margin-bottom: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item-marker {
    position: absolute;
    left: -3rem;
    width: 3rem;
    text-align: center;
}

.timeline-item-marker-text {
    font-weight: bold;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.timeline-item-marker-indicator {
    height: 2rem;
    width: 2rem;
    border-radius: 100%;
    margin-left: auto;
    margin-right: auto;
    position: relative;
}

.timeline-item-marker-indicator::before {
    content: '';
    display: block;
    position: absolute;
    top: 2rem;
    left: 0.9rem;
    height: calc(100% + 2rem);
    width: 0.2rem;
    background-color: #e3e6f0;
}

.timeline-item:last-child .timeline-item-marker-indicator::before {
    display: none;
}

.timeline-item-content {
    padding: 1.5rem;
    border-radius: 0.35rem;
    border: 1px solid #e3e6f0;
    background-color: #fff;
}

.timeline-item-content-header {
    margin-bottom: 0.75rem;
}
</style>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?> 