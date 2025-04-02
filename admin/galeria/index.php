<?php
/**
 * Admin - Gestão de Galeria
 * Gerencia as imagens do site organizadas por categorias
 */

// Incluir arquivos de configuração e funções
require_once '../core/config.php';
require_once '../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir título da página
$page_title = "Gestão de Galeria";
$breadcrumbs = generate_breadcrumbs([
    ['Admin', 'dashboard.php'],
    ['Galeria', '']
]);

// Incluir o cabeçalho
include_once '../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo $page_title; ?></h1>
    <?php echo $breadcrumbs; ?>

    <!-- Filtros de Categorias -->
    <div class="row mt-3 mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <i class="bi bi-funnel me-1"></i> Filtrar por Categoria
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active">Todas</button>
                        <button type="button" class="btn btn-outline-primary">Retratos</button>
                        <button type="button" class="btn btn-outline-primary">Manuscritos</button>
                        <button type="button" class="btn btn-outline-primary">Documentos</button>
                        <button type="button" class="btn btn-outline-primary">Escolas</button>
                        <button type="button" class="btn btn-outline-primary">Eventos</button>
                    </div>
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-circle"></i> Nova Categoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão para adicionar nova imagem -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Visualização da Galeria</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
            <i class="bi bi-cloud-arrow-up"></i> Carregar Nova Imagem
        </button>
    </div>

    <!-- Grid de Imagens -->
    <div class="row">
        <!-- Exemplo de imagem 1 -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/300x200?text=Retrato+BJC" class="card-img-top" alt="Retrato">
                    <span class="position-absolute top-0 end-0 badge bg-primary m-2">Retrato</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Retrato de Bento de Jesus Caraça</h5>
                    <p class="card-text small">Retrato formal utilizado em documentos oficiais da época.</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                    <small class="text-muted">Adicionado em: 10/08/2023</small>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exemplo de imagem 2 -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/300x200?text=Manuscrito" class="card-img-top" alt="Manuscrito">
                    <span class="position-absolute top-0 end-0 badge bg-success m-2">Manuscrito</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Manuscrito de 1943</h5>
                    <p class="card-text small">Página de anotações pessoais sobre matemática e filosofia.</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                    <small class="text-muted">Adicionado em: 15/07/2023</small>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exemplo de imagem 3 -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/300x200?text=Documento" class="card-img-top" alt="Documento">
                    <span class="position-absolute top-0 end-0 badge bg-info m-2">Documento</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Certificado Académico</h5>
                    <p class="card-text small">Documento oficial do Instituto Superior de Comércio.</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                    <small class="text-muted">Adicionado em: 03/09/2023</small>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exemplo de imagem 4 -->
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/300x200?text=Escola+EPBJC" class="card-img-top" alt="Escola">
                    <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">Escola</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Escola Profissional BJC</h5>
                    <p class="card-text small">Fachada do edifício principal em Lisboa.</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                    <small class="text-muted">Adicionado em: 20/06/2023</small>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <nav aria-label="Navegação da galeria" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Próxima</a>
            </li>
        </ul>
    </nav>
</div>

<!-- Modal de Upload de Imagem -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Carregar Nova Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="imageTitle" class="form-label">Título da Imagem</label>
                        <input type="text" class="form-control" id="imageTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="imageDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="imageDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imageCategory" class="form-label">Categoria</label>
                        <select class="form-select" id="imageCategory" required>
                            <option value="">Selecione uma categoria</option>
                            <option value="retrato">Retrato</option>
                            <option value="manuscrito">Manuscrito</option>
                            <option value="documento">Documento</option>
                            <option value="escola">Escola</option>
                            <option value="evento">Evento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="imageFile" class="form-label">Selecionar Imagem</label>
                        <input class="form-control" type="file" id="imageFile" accept="image/*" required>
                        <div class="form-text">Formatos suportados: JPG, PNG, GIF. Tamanho máximo: 5MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="imageAlt" class="form-label">Texto Alternativo (para acessibilidade)</label>
                        <input type="text" class="form-control" id="imageAlt">
                        <div class="form-text">Descreva a imagem para utilizadores com deficiência visual.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success">Carregar Imagem</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Categoria -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="categoryName" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryColor" class="form-label">Cor da Etiqueta</label>
                        <select class="form-select" id="categoryColor" required>
                            <option value="primary">Azul (Primário)</option>
                            <option value="success">Verde (Sucesso)</option>
                            <option value="danger">Vermelho (Perigo)</option>
                            <option value="warning">Amarelo (Aviso)</option>
                            <option value="info">Azul Claro (Info)</option>
                            <option value="dark">Preto (Escuro)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Adicionar Categoria</button>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include_once '../templates/footer.php';
?>