<?php
/**
 * Gestão de Escolas - Página inicial
 */

// Definir variáveis da página
$page_title = 'Gerenciador Escolas';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Definir a seção ativa (escolas, cursos, redes_sociais, contatos)
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'escolas';

// Processar formulários
$success_message = '';
$error_message = '';

// Buscar escolas
$query_escolas = "SELECT id, nome, cidade, ordem, ativa, publicada_site, endereco, telefone, email, website, coordenador FROM escolas_profissionais ORDER BY ordem ASC";
$result_escolas = mysqli_query($conn, $query_escolas);

// Verificar se houve resultados
if (!$result_escolas) {
    $error_message = "Erro ao buscar escolas: " . mysqli_error($conn);
}

// Processar ações nas escolas
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'update_escola') {
        // Processar atualização da escola do modal de edição
        $escola_id = $_POST['escola_id'];
        $nome = $_POST['escola_nome'];
        $cidade = $_POST['escola_cidade'];
        $ordem = $_POST['escola_ordem'];
        $publicada = isset($_POST['escola_publicada']) ? 1 : 0;
        $endereco = $_POST['escola_endereco'];
        $telefone = $_POST['escola_telefone'];
        $email = $_POST['escola_email'];
        $website = $_POST['escola_website'];
        $coordenador = $_POST['escola_coordenador'];
        
        // Atualizar escola
        $sql = "UPDATE escolas_profissionais SET 
                nome = ?, 
                cidade = ?, 
                ordem = ?, 
                publicada_site = ?, 
                endereco = ?, 
                telefone = ?, 
                email = ?, 
                website = ?, 
                coordenador = ? 
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssisssssi", $nome, $cidade, $ordem, $publicada, $endereco, $telefone, $email, $website, $coordenador, $escola_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Escola atualizada com sucesso!";
            // Recarregar os dados após atualização
            $result_escolas = mysqli_query($conn, $query_escolas);
        } else {
            $error_message = "Erro ao atualizar a escola: " . mysqli_error($conn);
        }
    } else if ($_POST['action'] == 'toggle_status') {
        // Processar alteração de status (publicado/não publicado)
        $escola_id = $_POST['escola_id'];
        $publicada = $_POST['publicada'] ? 1 : 0;
        
        $sql = "UPDATE escolas_profissionais SET publicada_site = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $publicada, $escola_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Status da escola atualizado com sucesso!";
            // Recarregar os dados após atualização
            $result_escolas = mysqli_query($conn, $query_escolas);
        } else {
            $error_message = "Erro ao atualizar status da escola: " . mysqli_error($conn);
        }
    }
}

// Processar formulário de informações de contato
if (isset($_POST['update_contact'])) {
    $endereco1 = $_POST['endereco_linha1'];
    $endereco2 = $_POST['endereco_linha2'];
    $telefone = $_POST['telefone_principal'];
    $email = $_POST['email_principal'];
    
    // Atualizar ou inserir informações de contato
    $contatos = [
        ['endereco_linha1', $endereco1, 'endereco'],
        ['endereco_linha2', $endereco2, 'endereco'],
        ['telefone_principal', $telefone, 'telefone'],
        ['email_principal', $email, 'email']
    ];
    
    $error = false;
    foreach ($contatos as $contato) {
        $chave = $contato[0];
        $valor = $contato[1];
        $tipo = $contato[2];
        
        $sql = "INSERT INTO informacoes_contato (chave, valor, tipo) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $chave, $valor, $tipo);
        if (!mysqli_stmt_execute($stmt)) {
            $error = true;
            break;
        }
    }
    
    if (!$error) {
        $success_message = "Informações de contato atualizadas com sucesso!";
    } else {
        $error_message = "Erro ao atualizar informações de contato: " . mysqli_error($conn);
    }
}

// Processar formulário de redes sociais
if (isset($_POST['update_social'])) {
    // Obter IDs existentes
    $ids = isset($_POST['social_id']) ? $_POST['social_id'] : [];
    $nomes = $_POST['social_nome'];
    $urls = $_POST['social_url'];
    $icones = $_POST['social_icone'];
    $ordens = $_POST['social_ordem'];
    $ativos = isset($_POST['social_ativo']) ? $_POST['social_ativo'] : [];
    
    // Converter ativos para formato de array com IDs como chaves
    $ativos_map = [];
    foreach ($ativos as $ativo_id) {
        $ativos_map[$ativo_id] = 1;
    }
    
    // Atualizar redes sociais existentes
    $error = false;
    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i];
        $nome = $nomes[$i];
        $url = $urls[$i];
        $icone = $icones[$i];
        $ordem = $ordens[$i];
        $ativo = isset($ativos_map[$id]) ? 1 : 0;
        
        $sql = "UPDATE redes_sociais SET 
                nome = ?, 
                url = ?, 
                icone = ?, 
                ordem = ?, 
                ativo = ? 
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssiii", $nome, $url, $icone, $ordem, $ativo, $id);
        if (!mysqli_stmt_execute($stmt)) {
            $error = true;
            break;
        }
    }
    
    // Inserir nova rede social se fornecida
    if (!$error && !empty($_POST['new_social_nome']) && !empty($_POST['new_social_url'])) {
        $nome = $_POST['new_social_nome'];
        $url = $_POST['new_social_url'];
        $icone = $_POST['new_social_icone'];
        $ordem = $_POST['new_social_ordem'];
        
        $sql = "INSERT INTO redes_sociais (nome, url, icone, ordem, ativo) VALUES (?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $nome, $url, $icone, $ordem);
        if (!mysqli_stmt_execute($stmt)) {
            $error = true;
        }
    }
    
    if (!$error) {
        $success_message = "Redes sociais atualizadas com sucesso!";
    } else {
        $error_message = "Erro ao atualizar redes sociais: " . mysqli_error($conn);
    }
}

// Processar formulário de cursos
if (isset($_POST['update_course'])) {
    $course_ids = $_POST['course_id'];
    $titles = $_POST['titulo'];
    $categories = $_POST['categoria'];
    $descriptions = $_POST['descricao'];
    $ratings = $_POST['avaliacao'];
    $imagens = $_POST['imagem'];
    $links = $_POST['link'];
    $duracoes = $_POST['duracao'];
    $statuses = isset($_POST['ativo']) ? $_POST['ativo'] : [];
    
    // Converter status para formato de array com IDs como chaves
    $status_map = [];
    foreach ($statuses as $status_id) {
        $status_map[$status_id] = 1;
    }
    
    $error = false;
    for ($i = 0; $i < count($course_ids); $i++) {
        $id = $course_ids[$i];
        $titulo = $titles[$i];
        $categoria = $categories[$i];
        $descricao = $descriptions[$i];
        $avaliacao = $ratings[$i];
        $imagem = $imagens[$i];
        $link = $links[$i];
        $duracao = $duracoes[$i];
        $ativo = isset($status_map[$id]) ? 1 : 0;
        
        $sql = "UPDATE cursos SET 
                titulo = ?, 
                categoria = ?,
                descricao = ?,
                avaliacao = ?,
                imagem = ?,
                link = ?,
                duracao = ?,
                ativo = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssdsssii", $titulo, $categoria, $descricao, $avaliacao, $imagem, $link, $duracao, $ativo, $id);
        if (!mysqli_stmt_execute($stmt)) {
            $error = true;
            break;
        }
    }
    
    if (!$error) {
        $success_message = "Cursos atualizados com sucesso!";
    } else {
        $error_message = "Erro ao atualizar cursos: " . mysqli_error($conn);
    }
} else if (isset($_POST['action']) && $_POST['action'] == 'toggle_curso_status') {
    // Processar alteração de status do curso
    $curso_id = $_POST['curso_id'];
    $ativo = $_POST['ativo'] ? 1 : 0;
    
    $sql = "UPDATE cursos SET ativo = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $ativo, $curso_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Status do curso atualizado com sucesso!";
        // Recarregar os dados após atualização
        $result_cursos = mysqli_query($conn, $query_cursos);
        $cursos = [];
        if ($result_cursos && mysqli_num_rows($result_cursos) > 0) {
            while ($row = mysqli_fetch_assoc($result_cursos)) {
                $cursos[] = $row;
            }
        }
    } else {
        $error_message = "Erro ao atualizar status do curso: " . mysqli_error($conn);
    }
}

// Buscar informações de contato
$query_contato = "SELECT chave, valor FROM informacoes_contato";
$result_contato = mysqli_query($conn, $query_contato);
$contatos = [];

if ($result_contato && mysqli_num_rows($result_contato) > 0) {
    while ($row = mysqli_fetch_assoc($result_contato)) {
        $contatos[$row['chave']] = $row['valor'];
    }
}

// Buscar redes sociais
$query_social = "SELECT id, nome, url, icone, ordem, ativo FROM redes_sociais ORDER BY ordem ASC";
$result_social = mysqli_query($conn, $query_social);
$redes_sociais = [];

if ($result_social && mysqli_num_rows($result_social) > 0) {
    while ($row = mysqli_fetch_assoc($result_social)) {
        $redes_sociais[] = $row;
    }
}

// Buscar cursos
$query_cursos = "SELECT id, titulo, categoria, descricao, imagem, avaliacao, duracao, ordem, ativo FROM cursos ORDER BY ordem ASC";
$result_cursos = mysqli_query($conn, $query_cursos);
$cursos = [];

if ($result_cursos && mysqli_num_rows($result_cursos) > 0) {
    while ($row = mysqli_fetch_assoc($result_cursos)) {
        $cursos[] = $row;
    }
}

// Buscar próxima ordem para nova rede social
$proxima_ordem = 1;
if (!empty($redes_sociais)) {
    $max_ordem = 0;
    foreach ($redes_sociais as $rede) {
        if ($rede['ordem'] > $max_ordem) {
            $max_ordem = $rede['ordem'];
        }
    }
    $proxima_ordem = $max_ordem + 1;
}

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

?>

<!-- Alertas de sucesso/erro -->
<?php if ($success_message): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if ($error_message): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Guias de navegação -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab == 'escolas' ? 'active' : ''; ?>" href="?tab=escolas">
            <i class="bi bi-buildings me-1"></i> Escolas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab == 'cursos' ? 'active' : ''; ?>" href="?tab=cursos">
            <i class="bi bi-mortarboard me-1"></i> Cursos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab == 'redes_sociais' ? 'active' : ''; ?>" href="?tab=redes_sociais">
            <i class="bi bi-share me-1"></i> Redes Sociais
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab == 'contatos' ? 'active' : ''; ?>" href="?tab=contatos">
            <i class="bi bi-envelope me-1"></i> Informações de Contato
        </a>
    </li>
</ul>

<!-- Conteúdo das guias -->
<?php if ($active_tab == 'escolas'): ?>
<!-- Guia de Escolas -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Gestão de Escolas</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i> A plataforma permite cadastrar até 5 escolas.
        </div>
        
        <?php if (!$result_escolas || mysqli_num_rows($result_escolas) == 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-4 text-muted">Nenhuma escola cadastrada.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php while ($escola = mysqli_fetch_assoc($result_escolas)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="m-0 font-weight-bold">Ordem: <?php echo $escola['ordem']; ?></h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input escola-status" type="checkbox"
                                               data-id="<?php echo $escola['id']; ?>"
                                               <?php echo $escola['publicada_site'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">
                                            <?php echo $escola['publicada_site'] ? 'Publicada' : 'Não publicada'; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($escola['nome']); ?></h5>
                            <p class="card-text">
                                <span class="badge bg-secondary"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($escola['cidade']); ?></span>
                            </p>
                            
                            <?php if (!empty($escola['telefone']) || !empty($escola['email'])): ?>
                            <div class="mt-2 small">
                                <?php if (!empty($escola['telefone'])): ?>
                                <p class="mb-1"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($escola['telefone']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($escola['email'])): ?>
                                <p class="mb-1"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($escola['email']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($escola['website'])): ?>
                                <p class="mb-1"><i class="bi bi-globe"></i> <a href="<?php echo htmlspecialchars($escola['website']); ?>" target="_blank">Website</a></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-end align-items-center mt-3">
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editEscolaModal<?php echo $escola['id']; ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal de Edição para esta escola -->
                <div class="modal fade" id="editEscolaModal<?php echo $escola['id']; ?>" tabindex="-1" 
                     aria-labelledby="editEscolaModalLabel<?php echo $escola['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="post" action="?tab=escolas">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editEscolaModalLabel<?php echo $escola['id']; ?>">Editar Escola</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label for="nome<?php echo $escola['id']; ?>" class="form-label">Nome da Escola</label>
                                            <input type="text" class="form-control" id="nome<?php echo $escola['id']; ?>" name="escola_nome" 
                                                value="<?php echo htmlspecialchars($escola['nome']); ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cidade<?php echo $escola['id']; ?>" class="form-label">Cidade</label>
                                            <input type="text" class="form-control" id="cidade<?php echo $escola['id']; ?>" name="escola_cidade" 
                                                value="<?php echo htmlspecialchars($escola['cidade']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="endereco<?php echo $escola['id']; ?>" class="form-label">Endereço</label>
                                            <input type="text" class="form-control" id="endereco<?php echo $escola['id']; ?>" name="escola_endereco" 
                                                value="<?php echo htmlspecialchars($escola['endereco']); ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="ordem<?php echo $escola['id']; ?>" class="form-label">Ordem</label>
                                            <input type="number" class="form-control" id="ordem<?php echo $escola['id']; ?>" name="escola_ordem" 
                                                value="<?php echo $escola['ordem']; ?>" min="1" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="coordenador<?php echo $escola['id']; ?>" class="form-label">Coordenador</label>
                                            <input type="text" class="form-control" id="coordenador<?php echo $escola['id']; ?>" name="escola_coordenador" 
                                                value="<?php echo htmlspecialchars($escola['coordenador']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="telefone<?php echo $escola['id']; ?>" class="form-label">Telefone</label>
                                            <input type="text" class="form-control" id="telefone<?php echo $escola['id']; ?>" name="escola_telefone" 
                                                value="<?php echo htmlspecialchars($escola['telefone']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email<?php echo $escola['id']; ?>" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email<?php echo $escola['id']; ?>" name="escola_email" 
                                                value="<?php echo htmlspecialchars($escola['email']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="website<?php echo $escola['id']; ?>" class="form-label">Website</label>
                                            <input type="url" class="form-control" id="website<?php echo $escola['id']; ?>" name="escola_website" 
                                                value="<?php echo htmlspecialchars($escola['website']); ?>" placeholder="https://">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="escola_publicada" 
                                                id="publicada<?php echo $escola['id']; ?>" value="1" 
                                                <?php echo $escola['publicada_site'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="publicada<?php echo $escola['id']; ?>">
                                                Publicar no site
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="escola_id" value="<?php echo $escola['id']; ?>">
                                    <input type="hidden" name="action" value="update_escola">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Formulário para atualização de status -->
            <form id="statusForm" method="post" action="?tab=escolas" style="display: none;">
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="escola_id" id="status_school_id">
                <input type="hidden" name="publicada" id="publicar_value">
                <button type="submit" id="statusSubmitBtn"></button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Script para gerenciar status das escolas -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar estilo para hover nos switches de status
    const style = document.createElement('style');
    style.textContent = `
        .form-check-input.escola-status:hover {
            cursor: pointer;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.5);
            transition: box-shadow 0.2s;
        }
        .form-check-input.escola-status + label:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    `;
    document.head.appendChild(style);

    // Monitorar switches de status nos cards
    const statusSwitches = document.querySelectorAll('.escola-status');
    statusSwitches.forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            const escolaId = this.getAttribute('data-id');
            document.getElementById('status_school_id').value = escolaId;
            document.getElementById('publicar_value').value = this.checked ? 1 : 0;
            
            // Atualizar o texto da label
            const label = this.nextElementSibling;
            if (label) {
                label.textContent = this.checked ? 'Publicada' : 'Não publicada';
            }
            
            // Enviar formulário
            document.getElementById('statusSubmitBtn').click();
        });
    });
});
</script>

<?php elseif ($active_tab == 'cursos'): ?>
<!-- Guia de Cursos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Gestão de Cursos</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill me-2"></i> A plataforma exibe exatamente 3 cursos na página inicial. Não é possível adicionar ou remover cursos, apenas editá-los.
        </div>
        
        <?php if (empty($cursos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-4 text-muted">Nenhum curso encontrado.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($cursos as $index => $curso): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img class="card-img-top" src="<?php echo '../../' . htmlspecialchars($curso['imagem']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>" style="height: 180px; object-fit: cover;">
                            <div class="position-absolute top-0 start-0 bg-dark text-white px-2 py-1">
                                Ordem: <?php echo $curso['ordem']; ?>
                            </div>
                            <div class="position-absolute top-0 end-0 form-check form-switch mt-1 me-2">
                                <input class="form-check-input curso-status" type="checkbox" 
                                       data-id="<?php echo $curso['id']; ?>" 
                                       <?php echo $curso['ativo'] ? 'checked' : ''; ?>>
                                <label class="form-check-label text-white">
                                    <?php echo $curso['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($curso['titulo']); ?></h5>
                            <p class="card-text text-muted mb-1">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($curso['categoria']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($curso['duracao']); ?></span>
                            </p>
                            <p class="card-text small">
                                <?php echo mb_strimwidth(htmlspecialchars($curso['descricao']), 0, 100, "..."); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <?php 
                                    $avaliacao = floatval($curso['avaliacao']);
                                    $estrelas_cheias = floor($avaliacao);
                                    $estrela_meia = ($avaliacao - $estrelas_cheias) >= 0.5 ? 1 : 0;
                                    $estrelas_vazias = 5 - $estrelas_cheias - $estrela_meia;
                                    
                                    // Estrelas cheias
                                    for ($i = 0; $i < $estrelas_cheias; $i++) {
                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                    }
                                    // Estrela meia
                                    if ($estrela_meia) {
                                        echo '<i class="bi bi-star-half text-warning"></i>';
                                    }
                                    // Estrelas vazias
                                    for ($i = 0; $i < $estrelas_vazias; $i++) {
                                        echo '<i class="bi bi-star text-warning"></i>';
                                    }
                                    ?>
                                    <span class="ms-1"><?php echo number_format($avaliacao, 1); ?></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal<?php echo $curso['id']; ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Formulário para atualização de status de curso -->
            <form id="cursoStatusForm" method="post" action="?tab=cursos">
                <input type="hidden" name="action" value="toggle_curso_status">
                <input type="hidden" name="curso_id" id="status_curso_id">
                <input type="hidden" name="ativo" id="status_curso_ativo">
                <button type="submit" id="cursoStatusSubmitBtn" style="display: none;"></button>
            </form>
            
            <!-- Formulário para submissão em massa (usado pelo modal) -->
            <form id="cursoFormMassa" method="post" action="?tab=cursos" style="display: none;">
                <input type="hidden" name="update_course" value="1">
                <?php foreach ($cursos as $curso): ?>
                <input type="hidden" name="course_id[]" value="<?php echo $curso['id']; ?>">
                <input type="hidden" name="titulo[]" value="<?php echo htmlspecialchars($curso['titulo']); ?>">
                <input type="hidden" name="categoria[]" value="<?php echo htmlspecialchars($curso['categoria']); ?>">
                <input type="hidden" name="descricao[]" value="<?php echo htmlspecialchars($curso['descricao']); ?>">
                <input type="hidden" name="imagem[]" value="<?php echo htmlspecialchars($curso['imagem']); ?>">
                <input type="hidden" name="link[]" value="<?php echo htmlspecialchars($curso['link'] ?? ''); ?>">
                <input type="hidden" name="duracao[]" value="<?php echo htmlspecialchars($curso['duracao']); ?>">
                <input type="hidden" name="avaliacao[]" value="<?php echo $curso['avaliacao']; ?>">
                <?php endforeach; ?>
                <div id="statusContainer">
                    <!-- Checkboxes serão adicionados via JavaScript -->
                </div>
                <button type="submit" id="cursoFormMassaSubmitBtn"></button>
            </form>
            
            <!-- Modais de Edição de Curso -->
            <?php foreach ($cursos as $curso): ?>
            <div class="modal fade" id="editModal<?php echo $curso['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $curso['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post" action="?tab=cursos">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $curso['id']; ?>">Editar Curso</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="titulo<?php echo $curso['id']; ?>" class="form-label">Título</label>
                                        <input type="text" class="form-control" id="titulo<?php echo $curso['id']; ?>" name="titulo[]" 
                                               value="<?php echo htmlspecialchars($curso['titulo']); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="categoria<?php echo $curso['id']; ?>" class="form-label">Categoria</label>
                                        <input type="text" class="form-control" id="categoria<?php echo $curso['id']; ?>" name="categoria[]" 
                                               value="<?php echo htmlspecialchars($curso['categoria']); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="duracao<?php echo $curso['id']; ?>" class="form-label">Duração</label>
                                        <input type="text" class="form-control" id="duracao<?php echo $curso['id']; ?>" name="duracao[]" 
                                               value="<?php echo htmlspecialchars($curso['duracao']); ?>" placeholder="3 Anos" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao<?php echo $curso['id']; ?>" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao<?php echo $curso['id']; ?>" name="descricao[]" 
                                              rows="4" required><?php echo htmlspecialchars($curso['descricao']); ?></textarea>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="imagem<?php echo $curso['id']; ?>" class="form-label">Imagem</label>
                                        <input type="text" class="form-control" id="imagem<?php echo $curso['id']; ?>" name="imagem[]" 
                                               value="<?php echo htmlspecialchars($curso['imagem']); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="link<?php echo $curso['id']; ?>" class="form-label">Link</label>
                                        <input type="url" class="form-control" id="link<?php echo $curso['id']; ?>" name="link[]" 
                                               value="<?php echo htmlspecialchars($curso['link']); ?>" placeholder="https://exemplo.com" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="avaliacao<?php echo $curso['id']; ?>" class="form-label">Avaliação</label>
                                        <input type="number" class="form-control" id="avaliacao<?php echo $curso['id']; ?>" name="avaliacao[]" 
                                               value="<?php echo $curso['avaliacao']; ?>" min="1" max="5" step="0.1" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="course_id[]" value="<?php echo $curso['id']; ?>">
                                        <input class="form-check-input modal-status" type="checkbox" name="ativo[]" 
                                               value="<?php echo $curso['id']; ?>" 
                                               id="ativo<?php echo $curso['id']; ?>" 
                                               <?php echo $curso['ativo'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ativo<?php echo $curso['id']; ?>">
                                            Curso ativo
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="m-0">Prévia da Imagem</h6>
                                            </div>
                                            <div class="card-body text-center">
                                                <img src="<?php echo '../../' . htmlspecialchars($curso['imagem']); ?>" 
                                                     alt="Prévia" class="img-fluid img-thumbnail" 
                                                     style="max-height: 150px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="update_course" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($active_tab == 'redes_sociais'): ?>
<!-- Guia de Redes Sociais -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Gestão de Redes Sociais</h6>
    </div>
    <div class="card-body">
        <form method="post" action="?tab=redes_sociais">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>URL</th>
                            <th>Ícone</th>
                            <th>Ordem</th>
                            <th>Ativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($redes_sociais as $rede): ?>
                        <tr>
                            <td>
                                <input type="hidden" name="social_id[]" value="<?php echo $rede['id']; ?>">
                                <input type="text" class="form-control form-control-sm" name="social_nome[]" 
                                       value="<?php echo htmlspecialchars($rede['nome']); ?>" required>
                            </td>
                            <td>
                                <input type="url" class="form-control form-control-sm" name="social_url[]" 
                                       value="<?php echo htmlspecialchars($rede['url']); ?>" required>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="social_icone[]" 
                                       value="<?php echo htmlspecialchars($rede['icone']); ?>" required>
                            </td>
                            <td style="width: 70px">
                                <input type="number" class="form-control form-control-sm" name="social_ordem[]" 
                                       value="<?php echo $rede['ordem']; ?>" min="1" required>
                            </td>
                            <td class="text-center" style="width: 50px">
                                <input type="checkbox" name="social_ativo[]" value="<?php echo $rede['id']; ?>" 
                                       <?php echo $rede['ativo'] ? 'checked' : ''; ?>>
                            </td>
                            <td class="text-center" style="width: 80px">
                                <button type="button" class="btn btn-sm btn-danger remove-social" 
                                        data-id="<?php echo $rede['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($rede['nome']); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Nova rede social -->
                        <tr class="table-light">
                            <td>
                                <input type="text" class="form-control form-control-sm" name="new_social_nome" 
                                       placeholder="Nova rede social">
                            </td>
                            <td>
                                <input type="url" class="form-control form-control-sm" name="new_social_url" 
                                       placeholder="https://">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="new_social_icone" 
                                       placeholder="bi bi-facebook" value="bi bi-">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" name="new_social_ordem" 
                                       value="<?php echo $proxima_ordem; ?>" min="1">
                            </td>
                            <td class="text-center">
                                <small class="text-muted">Auto</small>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info" role="alert">
                <i class="bi bi-lightbulb me-2"></i> Dica: Para identificar os ícones, use o padrão "bi bi-" seguido do nome do ícone desejado.
                <br>Por exemplo: <code>bi bi-facebook</code>, <code>bi bi-instagram</code>, <code>bi bi-linkedin</code> ou <code>bi bi-youtube</code>.
            </div>
            
            <button type="submit" name="update_social" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Atualizar Redes Sociais
            </button>
        </form>
        
        <!-- Formulário para remoção de rede social -->
        <form id="removeForm" method="post" action="?tab=redes_sociais" style="display: none;">
            <input type="hidden" name="action" value="remove_social">
            <input type="hidden" name="social_id" id="remove_social_id">
        </form>
        
        <!-- Script para remoção de rede social -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeBtns = document.querySelectorAll('.remove-social');
            removeBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const socialId = this.getAttribute('data-id');
                    const socialName = this.getAttribute('data-name');
                    
                    if (confirm('Tem certeza que deseja remover a rede social "' + socialName + '"?')) {
                        document.getElementById('remove_social_id').value = socialId;
                        document.getElementById('removeForm').submit();
                    }
                });
            });
        });
        </script>
    </div>
</div>

<?php elseif ($active_tab == 'contatos'): ?>
<!-- Guia de Informações de Contato -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informações de Contato</h6>
    </div>
    <div class="card-body">
        <form method="post" action="?tab=contatos">
            <div class="mb-3">
                <label for="endereco_linha1" class="form-label">Endereço (Linha 1)</label>
                <input type="text" class="form-control" id="endereco_linha1" name="endereco_linha1" 
                       value="<?php echo isset($contatos['endereco_linha1']) ? htmlspecialchars($contatos['endereco_linha1']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="endereco_linha2" class="form-label">Endereço (Linha 2)</label>
                <input type="text" class="form-control" id="endereco_linha2" name="endereco_linha2" 
                       value="<?php echo isset($contatos['endereco_linha2']) ? htmlspecialchars($contatos['endereco_linha2']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefone_principal" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone_principal" name="telefone_principal" 
                       value="<?php echo isset($contatos['telefone_principal']) ? htmlspecialchars($contatos['telefone_principal']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email_principal" class="form-label">Email</label>
                <input type="email" class="form-control" id="email_principal" name="email_principal" 
                       value="<?php echo isset($contatos['email_principal']) ? htmlspecialchars($contatos['email_principal']) : ''; ?>" required>
            </div>
            <button type="submit" name="update_contact" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Atualizar Informações de Contato
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar estilo para hover nos switches de status
    const style = document.createElement('style');
    style.textContent = `
        .form-check-input.escola-status:hover,
        .form-check-input.curso-status:hover {
            cursor: pointer;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.5);
            transition: box-shadow 0.2s;
        }
        .form-check-input.escola-status + label:hover,
        .form-check-input.curso-status + label:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    `;
    document.head.appendChild(style);

    // Monitorar switches de status nos cards de escolas
    const statusSwitches = document.querySelectorAll('.escola-status');
    statusSwitches.forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            const escolaId = this.getAttribute('data-id');
            document.getElementById('status_school_id').value = escolaId;
            document.getElementById('publicar_value').value = this.checked ? 1 : 0;
            
            // Atualizar o texto da label
            const label = this.nextElementSibling;
            if (label) {
                label.textContent = this.checked ? 'Publicada' : 'Não publicada';
            }
            
            // Enviar formulário
            document.getElementById('statusSubmitBtn').click();
        });
    });
    
    // Monitorar switches de status nos cards de cursos
    const cursoStatusSwitches = document.querySelectorAll('.curso-status');
    cursoStatusSwitches.forEach(function(switchEl) {
        switchEl.addEventListener('change', function() {
            const cursoId = this.getAttribute('data-id');
            document.getElementById('status_curso_id').value = cursoId;
            document.getElementById('status_curso_ativo').value = this.checked ? 1 : 0;
            
            // Atualizar o texto da label
            const label = this.nextElementSibling;
            if (label) {
                label.textContent = this.checked ? 'Ativo' : 'Inativo';
            }
            
            // Enviar formulário
            document.getElementById('cursoStatusSubmitBtn').click();
        });
    });
});
</script>