<?php
/**
 * Galeria de Imagens
 * Página de exibição da galeria de imagens do site
 */

// Incluir arquivos necessários
require_once 'header.php';

// Carregar a conexão com o banco de dados
$conn = require_once 'ConfigBD.php';

// Consulta para buscar categorias (temas) disponíveis
$sql_categorias = "SELECT id_tema_imagem, descricao FROM temas_imagens ORDER BY id_tema_imagem";
$result_categorias = mysqli_query($conn, $sql_categorias);

// Definir categoria selecionada (se houver filtro)
$filtro_categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

// Consulta para buscar todas as imagens (com filtro opcional)
$where_clause = $filtro_categoria > 0 ? "WHERE i.id_tema_imagem = $filtro_categoria" : "";
$sql_imagens = "SELECT i.id_imagem, i.imagem, i.descricao, t.descricao AS categoria, t.id_tema_imagem 
                FROM imagens i 
                LEFT JOIN temas_imagens t ON i.id_tema_imagem = t.id_tema_imagem 
                $where_clause
                ORDER BY i.id_imagem DESC";
$result_imagens = mysqli_query($conn, $sql_imagens);

// Função para obter a classe CSS da categoria
function get_categoria_badge_class($id_tema) {
    switch ($id_tema) {
        case 1: // Retratos
            return 'primary';
        case 2: // Amigos
            return 'success';
        case 3: // Viagens
            return 'info';
        default:
            return 'secondary';
    }
}

// Função para obter o texto da categoria
function get_categoria_label($id_tema, $categorias) {
    foreach ($categorias as $cat) {
        if ($cat['id_tema_imagem'] == $id_tema) {
            return $cat['descricao'];
        }
    }
    return 'Sem categoria';
}

// Armazenar categorias em um array para uso posterior
$categorias = [];
if ($result_categorias) {
    while ($cat = mysqli_fetch_assoc($result_categorias)) {
        $categorias[] = $cat;
    }
}
?>

<!-- Banner da página -->
<div class="container">
    <div class="row py-4">
        <div class="col-12 text-center">
            <h1>Galeria de Imagens</h1>
            <p class="lead">Conheça momentos da vida e obra de Bento de Jesus Caraça</p>
        </div>
    </div>
</div>

<!-- Filtros de categoria -->
<div class="container mb-4">
    <div class="card">
        <div class="card-body">
            <h5><i class="bi bi-funnel me-2"></i> Filtrar por Categoria</h5>
            
            <div class="btn-group mt-2">
                <a href="galeria.php" class="btn btn-outline-primary <?php echo $filtro_categoria === 0 ? 'active' : ''; ?>">
                    Todas
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a href="galeria.php?categoria=<?php echo $cat['id_tema_imagem']; ?>" 
                       class="btn btn-outline-<?php echo get_categoria_badge_class($cat['id_tema_imagem']); ?> <?php echo $filtro_categoria === (int)$cat['id_tema_imagem'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['descricao']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Galeria de imagens -->
<div class="container">
    <h2>Imagens na Galeria</h2>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result_imagens && mysqli_num_rows($result_imagens) > 0):
                            while ($imagem = mysqli_fetch_assoc($result_imagens)):
                                // Verificar se a imagem existe e construir caminho completo
                                $imagem_path = $imagem['imagem'];
                                
                                // Obter categoria
                                $categoria = !empty($imagem['categoria']) ? $imagem['categoria'] : 'Sem categoria';
                                $categoria_class = get_categoria_badge_class($imagem['id_tema_imagem']);
                        ?>
                            <tr>
                                <td class="text-center" style="width: 100px;">
                                    <a href="<?php echo $imagem_path; ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($imagem_path); ?>" 
                                             class="img-thumbnail" 
                                             alt="<?php echo htmlspecialchars($imagem['descricao']); ?>"
                                             style="max-width: 80px; max-height: 60px;"
                                             onerror="this.src='assets/img/no-image.jpg'">
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($imagem['descricao']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $categoria_class; ?>">
                                        <?php echo htmlspecialchars($categoria); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Não foram encontradas imagens na galeria.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Debug - Remover em produção -->
<div class="container mt-5 mb-5" style="display: none;">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Debug de Imagens</h5>
        </div>
        <div class="card-body">
            <h6>Verificação de caminhos de imagens:</h6>
            <ul class="list-group">
                <?php
                if ($result_imagens && mysqli_num_rows($result_imagens) > 0) {
                    // Resetar o ponteiro do resultado para o início
                    mysqli_data_seek($result_imagens, 0);
                    
                    while ($imagem = mysqli_fetch_assoc($result_imagens)) {
                        $imagem_path = $imagem['imagem'];
                        $caminho_absoluto = $_SERVER['DOCUMENT_ROOT'] . '/' . $imagem_path;
                        $exists = file_exists($caminho_absoluto);
                        echo '<li class="list-group-item ' . ($exists ? 'list-group-item-success' : 'list-group-item-danger') . '">';
                        echo '<strong>Caminho armazenado:</strong> ' . htmlspecialchars($imagem_path) . '<br>';
                        echo '<strong>Caminho absoluto:</strong> ' . htmlspecialchars($caminho_absoluto) . '<br>';
                        echo '<strong>Existe: </strong> ' . ($exists ? 'Sim' : 'Não') . '<br>';
                        echo '<strong>Document Root:</strong> ' . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '<br>';
                        echo '</li>';
                    }
                } else {
                    echo '<li class="list-group-item">Nenhuma imagem encontrada no banco de dados.</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
require_once 'footer.php';
?> 