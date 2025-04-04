<?php
/**
 * Admin - Seção de Legado
 * Gerencia as escolas e instituições que fazem parte do legado de Bento de Jesus Caraça
 */

// Define o título da página
$page_title = 'Legado';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Legado' => '#'
]);

// Incluir o cabeçalho
include_once '../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $page_title; ?></h1>
    <?php echo $breadcrumbs; ?>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-building me-1"></i>
                        Instituições no Legado
                    </div>
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Nova Instituição
                    </button>
                </div>
                <div class="card-body">
                    <!-- Lista de instituições -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Localização</th>
                                    <th scope="col">Fundação</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Exemplo de entrada -->
                                <tr>
                                    <td>Escola Profissional Bento de Jesus Caraça</td>
                                    <td>Escola Profissional</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1989</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sociedade Portuguesa de Matemática</td>
                                    <td>Associação</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1940</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Universidade Aberta</td>
                                    <td>Universidade</td>
                                    <td>Lisboa, Portugal</td>
                                    <td>1988</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mensagem quando não há instituições (escondida por padrão) -->
                    <div class="alert alert-info d-none">
                        <i class="bi bi-info-circle me-2"></i>
                        Não há instituições registradas no legado de Bento de Jesus Caraça.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-diagram-3 me-1"></i>
                        Conexões e Influências
                    </div>
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Nova Conexão
                    </button>
                </div>
                <div class="card-body">
                    <p class="lead">
                        Aqui pode gerenciar as relações, influências e conexões entre Bento de Jesus Caraça e outras
                        personalidades ou instituições que não são escolas ou entidades educativas.
                    </p>
                    
                    <!-- Lista de conexões (vazia por padrão) -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Não há conexões ou influências registradas. Adicione novos registros com o botão acima.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?>