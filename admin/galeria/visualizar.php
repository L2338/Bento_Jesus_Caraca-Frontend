<?php
// Configuração básica
session_start();
require_once '../config/app-config.php';
require_once '../core/functions.php';
require_login();
$conn = require '../../ConfigBD.php';

// Verificar se ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = (int)$_GET['id'];

// Título da página e breadcrumbs
$page_title = "Visualizar Imagem";
$breadcrumbs = [
    'Dashboard' => '../dashboard.php',
    'Galeria' => 'index.php',
    'Listar' => 'listar.php',
    'Visualizar' => ''
];

// Buscar imagem
$sql = "SELECT i.*, t.descricao AS tema_nome 
        FROM imagens i 
        LEFT JOIN temas_imagens t ON i.id_tema_imagem = t.id_tema_imagem 
        WHERE i.id_imagem = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: listar.php');
    exit;
}

$imagem = mysqli_fetch_assoc($result);

// Verificar se o arquivo existe
$arquivo_existe = file_exists('../../' . $imagem['imagem']);

// Incluir cabeçalho
include_once '../templates/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Visualizar Imagem</h1>
    
    <div class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detalhes da Imagem #<?php echo $imagem['id_imagem']; ?></h6>
            <div>
                <a href="editar.php?id=<?php echo $imagem['id_imagem']; ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="listar.php" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center mb-4">
                    <?php if ($arquivo_existe): ?>
                        <img src="<?php echo '../../' . htmlspecialchars($imagem['imagem']); ?>" 
                             alt="<?php echo htmlspecialchars($imagem['descricao']); ?>" 
                             class="img-fluid border" 
                             style="max-height: 400px;">
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> O arquivo de imagem não foi encontrado no caminho especificado.
                        </div>
                        <img src="../../assets/img/no-image.jpg" alt="Imagem não disponível" class="img-fluid border">
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px;">ID:</th>
                            <td><?php echo $imagem['id_imagem']; ?></td>
                        </tr>
                        <tr>
                            <th>Descrição:</th>
                            <td><?php echo htmlspecialchars($imagem['descricao']); ?></td>
                        </tr>
                        <tr>
                            <th>Categoria:</th>
                            <td><?php echo htmlspecialchars($imagem['tema_nome']); ?></td>
                        </tr>
                        <tr>
                            <th>Caminho:</th>
                            <td>
                                <?php echo htmlspecialchars($imagem['imagem']); ?>
                                <?php if (!$arquivo_existe): ?>
                                    <span class="badge bg-danger ms-2">Arquivo não encontrado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Data de Criação:</th>
                            <td><?php echo date('d/m/Y H:i', strtotime($imagem['data_criacao'])); ?></td>
                        </tr>
                    </table>
                    
                    <div class="mt-4">
                        <h6 class="font-weight-bold">Ações:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="editar.php?id=<?php echo $imagem['id_imagem']; ?>" class="btn btn-primary m-1">
                                <i class="bi bi-pencil"></i> Editar Imagem
                            </a>
                            
                            <?php if ($arquivo_existe): ?>
                                <a href="/<?php echo htmlspecialchars($imagem['imagem']); ?>" class="btn btn-info m-1" target="_blank">
                                    <i class="bi bi-eye"></i> Ver Tamanho Original
                                </a>
                            <?php endif; ?>
                            
                            <a href="listar.php?delete=<?php echo $imagem['id_imagem']; ?>" 
                               class="btn btn-danger m-1"
                               onclick="return confirm('Tem certeza que deseja excluir esta imagem?');">
                                <i class="bi bi-trash"></i> Excluir Imagem
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../templates/footer.php'; ?> 