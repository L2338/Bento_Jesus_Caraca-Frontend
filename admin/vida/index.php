<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Configuração da página
$pageTitle = "Gerenciar Vida & Timeline";
$currentSection = "vida";

// Ativar a exibição de erros para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug - verificar estrutura da tabela (remover em produção)
echo "<!-- DEBUG: Verificando tabela textos_secoes -->";
$table_check = $conn->query("SHOW TABLES LIKE 'textos_secoes'");
echo "<!-- DEBUG: Tabela existe? " . ($table_check->num_rows > 0 ? "Sim" : "Não") . " -->";

if ($table_check->num_rows > 0) {
    $desc_query = $conn->query("DESCRIBE textos_secoes");
    echo "<!-- DEBUG: Estrutura da tabela: ";
    if ($desc_query) {
        $columns = [];
        while ($col = $desc_query->fetch_assoc()) {
            $columns[] = $col['Field'] . " (" . $col['Type'] . ")";
        }
        echo implode(", ", $columns);
    } else {
        echo "Erro ao obter estrutura: " . $conn->error;
    }
    echo " -->";
    
    // Verificar todos os registros da tabela
    $all_records = $conn->query("SELECT id, chave, SUBSTRING(conteudo, 1, 50) AS preview FROM textos_secoes");
    echo "<!-- DEBUG: Registros encontrados: " . ($all_records ? $all_records->num_rows : "erro") . " -->";
    if ($all_records && $all_records->num_rows > 0) {
        echo "<!-- DEBUG: Todos os registros: ";
        while ($rec = $all_records->fetch_assoc()) {
            echo "[{$rec['id']}] {$rec['chave']}: {$rec['preview']}... | ";
        }
        echo " -->";
    }
}

// Buscar contagens para exibição
$timelineQuery = "SELECT COUNT(*) as total FROM timeline_blocos";
$timelineResult = $conn->query($timelineQuery);
$timelineCount = $timelineResult->fetch_assoc()['total'];

$activeTimelineQuery = "SELECT COUNT(*) as total FROM timeline_blocos WHERE ativo = 1";
$activeTimelineResult = $conn->query($activeTimelineQuery);
$activeTimelineCount = $activeTimelineResult->fetch_assoc()['total'];

// Buscar texto introdutório
$introQuery = "SELECT conteudo FROM textos_secoes WHERE chave = 'vida_introducao' LIMIT 1";
$introResult = $conn->query($introQuery);

// Debug - exibir apenas durante desenvolvimento
echo "<!-- DEBUG: Query: $introQuery -->";
echo "<!-- DEBUG: Result: " . ($introResult ? "Success" : "Failed: " . $conn->error) . " -->";
echo "<!-- DEBUG: Rows: " . ($introResult ? $introResult->num_rows : "N/A") . " -->";

$introText = ($introResult && $introResult->num_rows > 0) ? $introResult->fetch_assoc()['conteudo'] : '';
$hasIntroText = !empty($introText);

echo "<!-- DEBUG: Text Empty? " . (empty($introText) ? "Yes" : "No") . " -->";
echo "<!-- DEBUG: hasIntroText: " . ($hasIntroText ? "True" : "False") . " -->";

// Buscar blocos recentes
$recentBlocksQuery = "SELECT id, titulo, data_periodo, ativo, data_atualizacao 
                     FROM timeline_blocos 
                     ORDER BY data_atualizacao DESC 
                     LIMIT 5";
$recentBlocksResult = $conn->query($recentBlocksQuery);

// Incluir o cabeçalho
include "../../admin/templates/header.php";
?>

<!-- Estilos personalizados para melhorar a tipografia -->
<style>
    /* Fontes e tipografia mais coerentes com o restante do painel administrativo */
    .container-fluid {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    /* Títulos principais */
    h1.h3, h2.h4, h6.m-0.fw-bold {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        font-weight: 600 !important;
        letter-spacing: -0.02em;
    }
    
    /* Título da página */
    h1.h3.mb-0.text-gray-800.fw-bold {
        font-size: 1.6rem;
        color: #2c3e50 !important;
    }
    
    /* Estatísticas e números grandes */
    .card h2.h1 {
        font-weight: 700 !important;
        font-size: 2.6rem !important;
        letter-spacing: -0.03em;
    }
    
    /* Títulos de cards */
    .card-header h6 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Melhorar textos informativos */
    p, .text-muted {
        font-size: 0.95rem;
        line-height: 1.5;
        color: #4a5568 !important;
    }
    
    /* Botões com texto mais nítido */
    .btn {
        font-weight: 500;
        letter-spacing: 0.01em;
    }
    
    /* Badges com texto mais legível */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    /* Tabelas com texto mais consistente */
    .table {
        font-size: 0.9rem;
    }
    
    .table th {
        font-weight: 600;
        color: #2d3748;
    }
    
    /* Ajustes específicos para os cards de estatísticas */
    .card-header.bg-white.py-3 {
        border-bottom-width: 2px !important;
    }
    
    /* Ajustar visualização de texto introdutório */
    .text-preview {
        font-size: 0.95rem;
        line-height: 1.6;
    }
</style>

<div class="container-fluid p-4">
    <!-- Cabeçalho da página com estilo melhorado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold border-start border-primary ps-3" style="border-left-width: 4px!important;"><?php echo $pageTitle; ?></h1>
    </div>
    
    <!-- Alertas de feedback com estilo melhorado -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <?php 
            $successMsg = '';
            switch ($_GET['success']) {
                case 'intro_updated':
                    $successMsg = 'Texto introdutório atualizado com sucesso!';
                    break;
                case 'block_added':
                    $successMsg = 'Novo bloco adicionado com sucesso!';
                    break;
                case 'block_updated':
                    $successMsg = 'Bloco atualizado com sucesso!';
                    break;
                case 'block_deleted':
                    $successMsg = 'Bloco excluído com sucesso!';
                    break;
                case 'blocks_reordered':
                    $successMsg = 'Blocos reordenados com sucesso!';
                    break;
                default:
                    $successMsg = 'Operação realizada com sucesso!';
            }
            echo $successMsg;
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    
    <!-- Cards de estatísticas com design melhorado -->
    <div class="row mb-4 g-3">
        <!-- Card do Texto Introdutório -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom border-primary" style="border-bottom-width: 3px!important;">
                    <i class="bi bi-file-text me-2 text-primary"></i>
                    <h6 class="m-0 fw-bold text-primary text-uppercase">Texto Introdutório</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-center mb-auto py-3">
                        <div class="text-center">
                            <h2 class="h1 mb-0 fw-bold <?php echo $hasIntroText ? 'text-primary' : 'text-danger'; ?>">
                                <?php echo $hasIntroText ? 'Configurado' : 'Não Configurado'; ?>
                            </h2>
                        </div>
                    </div>
                    <a href="intro_edit.php" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-pencil"></i> Editar Texto
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Card de Blocos da Timeline -->
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom border-success" style="border-bottom-width: 3px!important;">
                    <i class="bi bi-calendar-event me-2 text-success"></i>
                    <h6 class="m-0 fw-bold text-success text-uppercase">Total de Blocos da Timeline</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-center mb-auto py-3">
                        <div class="text-center">
                            <h2 class="h1 mb-0 fw-bold text-success"><?php echo $timelineCount; ?></h2>
                            <p class="text-muted mt-2 mb-0">
                                <span class="badge bg-success"><?php echo $activeTimelineCount; ?> ativos</span>
                            </p>
                        </div>
                    </div>
                    <a href="timeline_list.php" class="btn btn-success w-100 mt-3">
                        <i class="bi bi-list-check"></i> Gerenciar Blocos
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Card de Ações Rápidas -->
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 rounded-3 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom border-info" style="border-bottom-width: 3px!important;">
                    <i class="bi bi-lightning me-2 text-info"></i>
                    <h6 class="m-0 fw-bold text-info text-uppercase">Ações Rápidas</h6>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-center mb-auto py-3">
                        <div class="text-center">
                            <h2 class="h1 mb-0 fw-bold text-info">Gerenciamento</h2>
                            <p class="text-muted mt-2 mb-0">Acesso rápido às principais funções</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a href="timeline_add.php" class="btn btn-info flex-grow-1">
                            <i class="bi bi-plus-lg"></i> Novo Bloco
                        </a>
                        <a href="timeline_reorder.php" class="btn btn-secondary flex-grow-1">
                            <i class="bi bi-sort-down"></i> Reordenar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Conteúdo Principal com design melhorado -->
    <div class="row g-3">
        <!-- Coluna da Esquerda - Gerenciamento do Texto -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2"></i> Texto Introdutório
                    </h6>
                    <a href="intro_edit.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($hasIntroText): ?>
                        <div class="text-preview bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            <?php echo $introText; ?>
                            <?php if (strlen($introText) > 500): ?>
                                <div class="text-center mt-3">
                                    <a href="intro_edit.php" class="btn btn-outline-primary btn-sm">Editar Texto Completo</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="empty-state mb-3">
                                <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <p class="text-muted mb-3">Nenhum texto introdutório configurado. Clique no botão abaixo para configurar o texto que aparecerá na introdução da página "Vida".</p>
                            <a href="intro_edit.php" class="btn btn-primary">
                                <i class="bi bi-pencil"></i> Configurar Texto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Coluna da Direita - Blocos Recentes -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-3 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i> Blocos Recentes da Timeline
                    </h6>
                    <a href="timeline_list.php" class="btn btn-success btn-sm">
                        <i class="bi bi-list"></i> Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($recentBlocksResult && $recentBlocksResult->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Data/Período</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($block = $recentBlocksResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($block['titulo']); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($block['data_periodo']); ?></span></td>
                                            <td>
                                                <span class="badge <?php echo $block['ativo'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $block['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="timeline_view.php?id=<?php echo $block['id']; ?>" class="btn btn-outline-info btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="timeline_edit.php?id=<?php echo $block['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="empty-state mb-3">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <p class="text-muted mb-3">Nenhum bloco de timeline encontrado.</p>
                            <a href="timeline_add.php" class="btn btn-success">
                                <i class="bi bi-plus-lg"></i> Adicionar Bloco
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Visão Geral do Módulo com design melhorado -->
    <div class="card border-0 rounded-3 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex align-items-center border-bottom">
            <i class="bi bi-info-circle me-2 text-primary"></i>
            <h6 class="m-0 fw-bold text-primary">Visão Geral do Módulo Vida & Timeline</h6>
        </div>
        <div class="card-body bg-light rounded p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-white p-4 rounded shadow-sm h-100">
                        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-file-text text-primary me-2"></i> Gerenciamento do Texto Introdutório</h5>
                        <p>Configure o texto que aparece na parte superior da página "Vida e Obra", 
                           que apresenta uma introdução sobre Bento de Jesus Caraça.</p>
                        <a href="intro_edit.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Editar Texto Introdutório
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white p-4 rounded shadow-sm h-100">
                        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-calendar-event text-success me-2"></i> Gerenciamento da Timeline</h5>
                        <p>Gerencie os blocos cronológicos que aparecem na timeline da vida de Bento de Jesus Caraça, 
                           incluindo eventos importantes, conquistas e marcos históricos.</p>
                        <div class="btn-group">
                            <a href="timeline_list.php" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-list-check"></i> Listar Blocos
                            </a>
                            <a href="timeline_add.php" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-plus-lg"></i> Adicionar Bloco
                            </a>
                            <a href="timeline_reorder.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-sort-down"></i> Reordenar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../../admin/templates/footer.php";
?> 