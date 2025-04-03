<?php
/**
 * Gestão de Obras - Página inicial
 */

// Habilitar depuração detalhada
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Define o título da página
$page_title = 'Gestão de Obras';

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

/**
 * Classe para operações CRUD de obras
 */
class Obra {
    private $conn;
    
    // Construtor que recebe a conexão com o banco de dados
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Obter todas as obras com ordenação e filtros opcionais
     * 
     * @param string $order_by Campo para ordenação
     * @param string $order_dir Direção da ordenação (ASC, DESC)
     * @param array $filtros Filtros a serem aplicados
     * @return array Lista de obras
     */
    public function listarTodas($order_by = 'id', $order_dir = 'ASC', $filtros = []) {
        $sql = "SELECT o.*, t.Nome_tema 
                FROM obras o 
                LEFT JOIN Temas t ON o.id_tema = t.id_tema";
        
        // Aplicar filtros se existirem
        if (!empty($filtros)) {
            $sql .= " WHERE ";
            $condicoes = [];
            
            if (isset($filtros['titulo']) && !empty($filtros['titulo'])) {
                $condicoes[] = "o.titulo LIKE '%" . $this->conn->real_escape_string($filtros['titulo']) . "%'";
            }
            
            if (isset($filtros['autor']) && !empty($filtros['autor'])) {
                $condicoes[] = "o.autor LIKE '%" . $this->conn->real_escape_string($filtros['autor']) . "%'";
            }
            
            if (isset($filtros['id_tema']) && !empty($filtros['id_tema'])) {
                $condicoes[] = "o.id_tema = " . (int)$filtros['id_tema'];
            }
            
            if (isset($filtros['ano']) && !empty($filtros['ano'])) {
                $condicoes[] = "o.ano = " . (int)$filtros['ano'];
            }
            
            $sql .= implode(" AND ", $condicoes);
        }
        
        // Adicionar ordenação
        $campos_permitidos = ['id', 'titulo', 'autor', 'ano'];
        $order_by = in_array($order_by, $campos_permitidos) ? $order_by : 'id';
        $order_dir = strtoupper($order_dir) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql .= " ORDER BY o.$order_by $order_dir";
        
        $result = $this->conn->query($sql);
        $obras = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $obras[] = $row;
            }
        }
        
        return $obras;
    }
    
    /**
     * Buscar uma obra específica pelo ID
     * 
     * @param int $id ID da obra
     * @return array|null Dados da obra ou null se não encontrada
     */
    public function buscarPorId($id) {
        $id = (int)$id;
        $sql = "SELECT o.*, t.Nome_tema 
                FROM obras o 
                LEFT JOIN Temas t ON o.id_tema = t.id_tema 
                WHERE o.id = $id";
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Adicionar uma nova obra
     * 
     * @param array $dados Dados da obra
     * @return int|bool ID da obra inserida ou false em caso de erro
     */
    public function adicionar($dados) {
        // Validar dados obrigatórios
        if (empty($dados['titulo']) || empty($dados['pdf'])) {
            return false;
        }
        
        // Preparar a query
        $sql = "INSERT INTO obras (titulo, descricao, pdf, imagem_capa, autor, id_tema, ano) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssii", 
                $dados['titulo'],
                $dados['descricao'] ?? '',
                $dados['pdf'],
                $dados['imagem_capa'] ?? '',
                $dados['autor'] ?? '',
                $dados['id_tema'] ? (int)$dados['id_tema'] : null,
                $dados['ano'] ? (int)$dados['ano'] : null
            );
            
            $result = $stmt->execute();
            if ($result) {
                return $this->conn->insert_id;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erro ao adicionar obra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualizar uma obra existente
     * 
     * @param int $id ID da obra
     * @param array $dados Dados atualizados
     * @return bool Sucesso ou falha
     */
    public function atualizar($id, $dados) {
        $id = (int)$id;
        
        // Validar dados obrigatórios
        if (empty($dados['titulo']) || empty($dados['pdf'])) {
            return false;
        }
        
        // Preparar a query
        $sql = "UPDATE obras SET 
                titulo = ?, 
                descricao = ?, 
                pdf = ?, 
                imagem_capa = ?, 
                autor = ?, 
                id_tema = ?, 
                ano = ? 
                WHERE id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssiii", 
                $dados['titulo'],
                $dados['descricao'] ?? '',
                $dados['pdf'],
                $dados['imagem_capa'] ?? '',
                $dados['autor'] ?? '',
                $dados['id_tema'] ? (int)$dados['id_tema'] : null,
                $dados['ano'] ? (int)$dados['ano'] : null,
                $id
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao atualizar obra: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Excluir uma obra
     * 
     * @param int $id ID da obra
     * @return bool Sucesso ou falha
     */
    public function excluir($id) {
        $id = (int)$id;
        
        // Obter informações da obra antes de excluir (para possível limpeza de arquivos)
        $obra = $this->buscarPorId($id);
        
        if (!$obra) {
            return false;
        }
        
        $sql = "DELETE FROM obras WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Erro ao preparar consulta: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        
        return $resultado;
    }
    
    /**
     * Obter todos os temas disponíveis
     * 
     * @return array Lista de temas
     */
    public function listarTemas() {
        $sql = "SELECT * FROM Temas ORDER BY Nome_tema ASC";
        $result = $this->conn->query($sql);
        $temas = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $temas[] = $row;
            }
        }
        
        return $temas;
    }
    
    /**
     * Contar total de obras cadastradas
     * 
     * @param array $filtros Filtros opcionais
     * @return int Total de obras
     */
    public function contarTotal($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM obras";
        
        // Aplicar filtros se existirem
        if (!empty($filtros)) {
            $sql .= " WHERE ";
            $condicoes = [];
            
            if (isset($filtros['titulo']) && !empty($filtros['titulo'])) {
                $condicoes[] = "titulo LIKE '%" . $this->conn->real_escape_string($filtros['titulo']) . "%'";
            }
            
            if (isset($filtros['autor']) && !empty($filtros['autor'])) {
                $condicoes[] = "autor LIKE '%" . $this->conn->real_escape_string($filtros['autor']) . "%'";
            }
            
            if (isset($filtros['id_tema']) && !empty($filtros['id_tema'])) {
                $condicoes[] = "id_tema = " . (int)$filtros['id_tema'];
            }
            
            if (isset($filtros['ano']) && !empty($filtros['ano'])) {
                $condicoes[] = "ano = " . (int)$filtros['ano'];
            }
            
            $sql .= implode(" AND ", $condicoes);
        }
        
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }
        
        return 0;
    }
    
    /**
     * Função auxiliar para processar upload de arquivo
     * 
     * @param array $file Dados do arquivo ($_FILES['campo'])
     * @param string $diretorio Diretório de destino
     * @param array $tipos_permitidos Tipos MIME permitidos
     * @param int $tamanho_max Tamanho máximo em bytes
     * @return string|false Nome do arquivo salvo ou false em caso de erro
     */
    public function processarUpload($file, $diretorio, $tipos_permitidos = [], $tamanho_max = 5242880) {
        // Verificar se o upload foi bem-sucedido
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Verificar tamanho
        if ($file['size'] > $tamanho_max) {
            return false;
        }
        
        // Verificar tipo, se especificado
        if (!empty($tipos_permitidos) && !in_array($file['type'], $tipos_permitidos)) {
            return false;
        }
        
        // Gerar nome único para o arquivo
        $nome_arquivo = uniqid() . '_' . basename($file['name']);
        $caminho_completo = $diretorio . '/' . $nome_arquivo;
        
        // Mover o arquivo para o diretório de destino
        if (move_uploaded_file($file['tmp_name'], $caminho_completo)) {
            return $nome_arquivo;
        }
        
        return false;
    }
}

// Conectar ao banco de dados
$conn = require_once '../../ConfigBD.php';

// Criar instância da classe Obra
$obraModel = new Obra($conn);

// Definir ordenação e filtros
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'id';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'ASC';

// Processar filtros
$filtros = [];
if (isset($_GET['filtro'])) {
    foreach ($_GET['filtro'] as $chave => $valor) {
        if (!empty($valor) && $chave != 'status') {
            $filtros[$chave] = $valor;
        }
    }
}

// Buscar obras filtradas
$obras = $obraModel->listarTodas($order_by, $order_dir, $filtros);

// Buscar temas para filtro
$temas = $obraModel->listarTemas();

// Mensagem de sucesso ou erro, se houver
$mensagem = '';
if (isset($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $mensagem = '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show">' . 
                $flash['message'] . 
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button></div>';
    unset($_SESSION['flash_message']);
}

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Obras' => '#'
]);

// Exibir mensagem flash, se houver
echo $mensagem;
?>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form method="get" action="index.php" class="row">
            <div class="col-md-3 mb-3">
                <label for="filtro-titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="filtro-titulo" name="filtro[titulo]" 
                       value="<?php echo isset($filtros['titulo']) ? htmlspecialchars($filtros['titulo']) : ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="filtro-autor" class="form-label">Autor</label>
                <input type="text" class="form-control" id="filtro-autor" name="filtro[autor]" 
                       value="<?php echo isset($filtros['autor']) ? htmlspecialchars($filtros['autor']) : ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="filtro-tema" class="form-label">Tema</label>
                <select class="form-select" id="filtro-tema" name="filtro[id_tema]">
                    <option value="">Todos</option>
                    <?php foreach ($temas as $tema): ?>
                    <option value="<?php echo $tema['id_tema']; ?>" <?php echo isset($filtros['id_tema']) && $filtros['id_tema'] == $tema['id_tema'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tema['Nome_tema']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="filtro-ano" class="form-label">Ano</label>
                <input type="number" class="form-control" id="filtro-ano" name="filtro[ano]" 
                       value="<?php echo isset($filtros['ano']) ? (int)$filtros['ano'] : ''; ?>">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter"></i> Filtrar
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Obras</h6>
        <button type="button" class="btn btn-sm btn-primary btn-novo" data-bs-toggle="modal" data-bs-target="#modalObra">
            <i class="bi bi-plus-circle"></i> Nova Obra
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>
                            <a href="?order_by=id&order_dir=<?php echo $order_by == 'id' && $order_dir == 'ASC' ? 'DESC' : 'ASC'; ?><?php echo !empty($filtros) ? '&filtro=' . http_build_query(['filtro' => $filtros]) : ''; ?>">
                                ID
                                <?php if ($order_by == 'id'): ?>
                                    <i class="bi bi-arrow-<?php echo $order_dir == 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?order_by=titulo&order_dir=<?php echo $order_by == 'titulo' && $order_dir == 'ASC' ? 'DESC' : 'ASC'; ?><?php echo !empty($filtros) ? '&filtro=' . http_build_query(['filtro' => $filtros]) : ''; ?>">
                                Título
                                <?php if ($order_by == 'titulo'): ?>
                                    <i class="bi bi-arrow-<?php echo $order_dir == 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?order_by=autor&order_dir=<?php echo $order_by == 'autor' && $order_dir == 'ASC' ? 'DESC' : 'ASC'; ?><?php echo !empty($filtros) ? '&filtro=' . http_build_query(['filtro' => $filtros]) : ''; ?>">
                                Autor
                                <?php if ($order_by == 'autor'): ?>
                                    <i class="bi bi-arrow-<?php echo $order_dir == 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Tema</th>
                        <th>
                            <a href="?order_by=ano&order_dir=<?php echo $order_by == 'ano' && $order_dir == 'ASC' ? 'DESC' : 'ASC'; ?><?php echo !empty($filtros) ? '&filtro=' . http_build_query(['filtro' => $filtros]) : ''; ?>">
                                Ano
                                <?php if ($order_by == 'ano'): ?>
                                    <i class="bi bi-arrow-<?php echo $order_dir == 'ASC' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($obras) > 0): ?>
                        <?php foreach ($obras as $obra): ?>
                            <tr>
                                <td><?php echo $obra['id']; ?></td>
                                <td><?php echo htmlspecialchars($obra['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($obra['autor']); ?></td>
                                <td><?php echo htmlspecialchars($obra['Nome_tema'] ?? 'Sem tema'); ?></td>
                                <td><?php echo $obra['ano'] ? $obra['ano'] : '-'; ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary btn-editar" title="Editar" data-id="<?php echo $obra['id']; ?>" data-mode="edit" data-bs-toggle="modal" data-bs-target="#modalObra">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info btn-visualizar" title="Visualizar" data-id="<?php echo $obra['id']; ?>" data-mode="view" data-bs-toggle="modal" data-bs-target="#modalObra">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-excluir" 
                                                data-bs-toggle="modal" data-bs-target="#modalExcluir" 
                                                data-id="<?php echo $obra['id']; ?>" 
                                                data-titulo="<?php echo htmlspecialchars($obra['titulo']); ?>"
                                                title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="py-5">
                                    <i class="bi bi-journal-x text-muted fa-3x mb-3"></i>
                                    <p class="mb-0 text-muted">Nenhuma obra encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExcluirLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a obra <strong id="tituloObra"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarExclusao" class="btn btn-danger">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição/Visualização de Obra -->
<div class="modal fade" id="modalObra" tabindex="-1" aria-labelledby="modalObraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalObraLabel">Detalhes da Obra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formObra" enctype="multipart/form-data">
                    <input type="hidden" id="obra_id" name="id" value="">
                    
                    <div class="row">
                        <!-- Título -->
                        <div class="col-md-8 mb-3">
                            <label for="obra_titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="obra_titulo" name="titulo" required>
                            <div class="invalid-feedback">O título é obrigatório.</div>
                        </div>
                        
                        <!-- Autor -->
                        <div class="col-md-4 mb-3">
                            <label for="obra_autor" class="form-label">Autor</label>
                            <input type="text" class="form-control" id="obra_autor" name="autor">
                        </div>
                        
                        <!-- Descrição -->
                        <div class="col-md-12 mb-3">
                            <label for="obra_descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="obra_descricao" name="descricao" rows="4"></textarea>
                        </div>
                        
                        <!-- Tema -->
                        <div class="col-md-6 mb-3">
                            <label for="obra_id_tema" class="form-label">Tema</label>
                            <select class="form-select" id="obra_id_tema" name="id_tema">
                                <option value="">Selecione um tema</option>
                                <?php foreach ($temas as $tema): ?>
                                <option value="<?php echo $tema['id_tema']; ?>">
                                    <?php echo htmlspecialchars($tema['Nome_tema']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Ano -->
                        <div class="col-md-6 mb-3">
                            <label for="obra_ano" class="form-label">Ano de Publicação</label>
                            <input type="number" class="form-control" id="obra_ano" name="ano" min="1000" max="<?php echo date('Y'); ?>">
                        </div>
                        
                        <!-- Visualização somente leitura de arquivos existentes -->
                        <div class="col-md-12 mb-3" id="arquivos_atuais">
                            <div class="card border p-3 bg-light">
                                <h6 class="mb-3">Arquivos Atuais</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">PDF Atual:</label>
                                        <div id="pdf_atual_container"></div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Imagem Atual:</label>
                                        <div id="imagem_atual_container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modo de edição apenas - Upload de novos arquivos -->
                        <div class="col-md-6 mb-3 edit-only" style="display: none;">
                            <label for="obra_pdf" class="form-label">Novo PDF</label>
                            <input type="file" class="form-control" id="obra_pdf" name="pdf" accept=".pdf">
                            <div class="form-text">Tamanho máximo: 20MB. Apenas arquivos PDF.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3 edit-only" style="display: none;">
                            <label for="obra_imagem_capa" class="form-label">Nova Imagem de Capa</label>
                            <input type="file" class="form-control" id="obra_imagem_capa" name="imagem_capa" accept="image/*">
                            <div class="form-text">Tamanho máximo: 5MB. Formatos: JPG, PNG, GIF.</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary edit-only" id="btnSalvarObra" style="display: none;">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>

<script>
// Aguardar o carregamento completo do DOM e das bibliotecas
window.addEventListener('load', function() {
    console.log('Página completamente carregada - Inicializando scripts');
    
    try {
        // Verificar se Bootstrap está disponível
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap não encontrado! O modal não funcionará.');
            alert('Erro: Bootstrap não está disponível. Algumas funcionalidades podem não funcionar corretamente.');
            return;
        } else {
            console.log('Bootstrap encontrado e disponível.');
        }
        
        // Configurar modal de exclusão
        var modalExcluir = document.getElementById('modalExcluir');
        console.log('Modal Excluir:', modalExcluir);
        
        if (!modalExcluir) {
            console.error('Modal de exclusão não encontrado!');
            return;
        }
        
        let idObraParaExcluir = 0;
        let modalExcluirBS = null;
        
        try {
            modalExcluirBS = new bootstrap.Modal(modalExcluir);
            console.log('Modal Excluir inicializado:', modalExcluirBS);
        } catch (error) {
            console.error('Erro ao inicializar modal de exclusão:', error);
            return;
        }
        
        modalExcluir.addEventListener('show.bs.modal', function(event) {
            try {
                const button = event.relatedTarget;
                if (!button) {
                    console.error('Botão que acionou o modal não encontrado!');
                    return;
                }
                
                idObraParaExcluir = button.getAttribute('data-id');
                const titulo = button.getAttribute('data-titulo');
                
                console.log('Preparando exclusão da obra:', idObraParaExcluir, titulo);
                
                const tituloObraElement = document.getElementById('tituloObra');
                if (tituloObraElement) {
                    tituloObraElement.textContent = titulo || 'obra selecionada';
                } else {
                    console.error('Elemento para exibir título da obra não encontrado!');
                }
            } catch (error) {
                console.error('Erro ao configurar modal de exclusão:', error);
                alert('Ocorreu um erro ao preparar a exclusão. Por favor, tente novamente.');
            }
        });
        
        // Configurar o botão de exclusão para usar AJAX
        const btnConfirmarExclusao = document.getElementById('btnConfirmarExclusao');
        if (btnConfirmarExclusao) {
            btnConfirmarExclusao.addEventListener('click', function() {
                if (idObraParaExcluir > 0) {
                    fetch('excluir.php?id=' + idObraParaExcluir)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro ao excluir obra');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (modalExcluirBS) {
                                modalExcluirBS.hide();
                            }
                            
                            if (data.success) {
                                alert('Obra excluída com sucesso!');
                                window.location.reload();
                            } else {
                                alert(data.message || 'Erro ao excluir obra');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao excluir obra. Por favor, tente novamente.');
                        });
                } else {
                    console.error('ID da obra para exclusão inválido:', idObraParaExcluir);
                    alert('Ocorreu um erro: ID da obra inválido.');
                }
            });
        } else {
            console.error('Botão de confirmar exclusão não encontrado!');
        }
        
        // Modal de obra (visualização e edição)
        let modalObra = document.getElementById('modalObra');
        let modalObraBS = null;
        
        if (modalObra) {
            try {
                modalObraBS = new bootstrap.Modal(modalObra);
                console.log('Modal Obra inicializado:', modalObraBS);
            } catch (error) {
                console.error('Erro ao inicializar modal de obra:', error);
                return;
            }
            
            // Vincular evento ao modal
            modalObra.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                
                // Garantir que o ID seja tratado como string ou null para evitar erros
                const id = button && button.getAttribute ? button.getAttribute('data-id') : null;
                const mode = button && button.getAttribute ? (button.getAttribute('data-mode') || 'view') : 'view'; // 'view' ou 'edit'
                
                console.log('Abrindo modal com id:', id, 'modo:', mode);
                
                try {
                    // Configurar título do modal
                    const modalObraLabel = document.getElementById('modalObraLabel');
                    if (modalObraLabel) {
                        modalObraLabel.textContent = mode === 'edit' ? 'Editar Obra' : (id ? 'Visualizar Obra' : 'Adicionar Nova Obra');
                    }
                    
                    // Configurar visibilidade dos elementos de edição
                    const editElements = document.querySelectorAll('.edit-only');
                    editElements.forEach(el => {
                        el.style.display = mode === 'edit' || !id ? 'block' : 'none';
                    });
                    
                    // Tornar os campos editáveis ou somente leitura
                    const formInputs = document.querySelectorAll('#formObra input, #formObra textarea, #formObra select');
                    formInputs.forEach(input => {
                        input.readOnly = mode !== 'edit' && id;
                        if (input.tagName === 'SELECT') {
                            input.disabled = mode !== 'edit' && id;
                        }
                    });
                    
                    // Se tiver ID, carrega os dados da obra, caso contrário prepara para nova obra
                    if (id) {
                        // Carregar dados da obra
                        carregarDadosObra(id);
                    } else {
                        // Limpar o formulário para nova obra
                        const formObra = document.getElementById('formObra');
                        if (formObra) {
                            formObra.reset();
                        }
                        
                        const obraIdField = document.getElementById('obra_id');
                        if (obraIdField) {
                            obraIdField.value = '';
                        }
                        
                        // Resetar arquivos exibidos
                        const pdfContainer = document.getElementById('pdf_atual_container');
                        if (pdfContainer) {
                            pdfContainer.innerHTML = '<span class="text-muted">Nenhum PDF disponível</span>';
                        }
                        
                        const imagemContainer = document.getElementById('imagem_atual_container');
                        if (imagemContainer) {
                            imagemContainer.innerHTML = '<span class="text-muted">Nenhuma imagem disponível</span>';
                        }
                        
                        // Marcar PDF como obrigatório para novas obras
                        const pdfInput = document.getElementById('obra_pdf');
                        if (pdfInput) {
                            pdfInput.required = true;
                        }
                    }
                } catch (error) {
                    console.error('Erro ao configurar modal:', error);
                    alert('Ocorreu um erro ao configurar o modal. Por favor, tente novamente.');
                }
            });
        }
    } catch (error) {
        console.error('Erro geral na inicialização:', error);
        alert('Ocorreu um erro ao carregar a página. Por favor, atualize a página e tente novamente.');
    }

    // Função para carregar dados da obra
    function carregarDadosObra(id) {
        try {
            fetch(`get_obra.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao buscar dados da obra');
                    }
                    return response.json();
                })
                .then(data => {
                    // Verificar se a resposta contém uma obra válida
                    if (!data || data.error) {
                        console.error('Erro:', data?.error || 'Dados da obra inválidos');
                        alert('Não foi possível carregar os dados da obra.');
                        return;
                    }
                    
                    // Salvar em uma variável local para uso no escopo
                    const obra = data;
                    
                    // Preencher o formulário com os dados
                    const obraIdField = document.getElementById('obra_id');
                    const obraTituloField = document.getElementById('obra_titulo');
                    const obraAutorField = document.getElementById('obra_autor');
                    const obraDescricaoField = document.getElementById('obra_descricao');
                    const obraAnoField = document.getElementById('obra_ano');
                    
                    if (obraIdField) obraIdField.value = obra.id;
                    if (obraTituloField) obraTituloField.value = obra.titulo;
                    if (obraAutorField) obraAutorField.value = obra.autor || '';
                    if (obraDescricaoField) obraDescricaoField.value = obra.descricao || '';
                    if (obraAnoField) obraAnoField.value = obra.ano || '';
                    
                    // Selecionar o tema
                    const temaSelect = document.getElementById('obra_id_tema');
                    if (temaSelect) {
                        if (obra.id_tema) {
                            temaSelect.value = obra.id_tema;
                        } else {
                            temaSelect.selectedIndex = 0;
                        }
                    }
                    
                    // Exibir arquivos atuais
                    const pdfContainer = document.getElementById('pdf_atual_container');
                    const imagemContainer = document.getElementById('imagem_atual_container');
                    
                    if (pdfContainer) {
                        if (obra.pdf) {
                            pdfContainer.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                    <span>${obra.pdf}</span>
                                    <a href="../../assets/pdf/Obras/${obra.pdf}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>
                            `;
                        } else {
                            pdfContainer.innerHTML = '<span class="text-muted">Nenhum PDF disponível</span>';
                        }
                    }
                    
                    if (imagemContainer) {
                        if (obra.imagem_capa) {
                            imagemContainer.innerHTML = `
                                <div>
                                    <img src="../../assets/img/obras/${obra.imagem_capa}" class="img-thumbnail" style="max-height: 100px">
                                    <div class="mt-1">${obra.imagem_capa}</div>
                                </div>
                            `;
                        } else {
                            imagemContainer.innerHTML = '<span class="text-muted">Nenhuma imagem disponível</span>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Não foi possível carregar os dados da obra. Por favor, tente novamente.');
                });
        } catch (error) {
            console.error('Erro ao carregar dados da obra:', error);
            alert('Falha ao tentar carregar os dados da obra.');
        }
    }
    
    // Manipular envio do formulário
    const btnSalvarObra = document.getElementById('btnSalvarObra');
    if (btnSalvarObra) {
        btnSalvarObra.addEventListener('click', function() {
            const form = document.getElementById('formObra');
            if (!form) {
                alert('Formulário não encontrado.');
                return;
            }
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const formData = new FormData(form);
            
            // Enviar dados para o servidor
            fetch('salvar_obra.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao salvar dados: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Fechar modal e recarregar a página
                    if (modalObraBS) {
                        modalObraBS.hide();
                    }
                    alert('Obra salva com sucesso!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Erro ao salvar a obra.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar: ' + error.message);
            });
        });
    }
    
    // Botão para adicionar nova obra - alternativa para o data-bs-toggle
    const btnNovo = document.querySelector('.btn-novo');
    if (btnNovo && !btnNovo.hasAttribute('data-bs-toggle')) {
        console.log('Configurando botão Nova Obra com event listener adicional');
        btnNovo.addEventListener('click', function() {
            console.log('Botão Nova Obra clicado via JS');
            // Limpar o formulário
            const formObra = document.getElementById('formObra');
            if (!formObra) {
                console.error('Formulário não encontrado');
                return;
            }
            
            formObra.reset();
            
            const obraIdField = document.getElementById('obra_id');
            if (obraIdField) {
                obraIdField.value = '';
            }
            
            // Resetar arquivos exibidos
            const pdfContainer = document.getElementById('pdf_atual_container');
            if (pdfContainer) {
                pdfContainer.innerHTML = '<span class="text-muted">Nenhum PDF disponível</span>';
            }
            
            const imagemContainer = document.getElementById('imagem_atual_container');
            if (imagemContainer) {
                imagemContainer.innerHTML = '<span class="text-muted">Nenhuma imagem disponível</span>';
            }
            
            // Configurar modo de edição
            const modalObraLabel = document.getElementById('modalObraLabel');
            if (modalObraLabel) {
                modalObraLabel.textContent = 'Adicionar Nova Obra';
            }
            
            // Mostrar campos de edição
            const editElements = document.querySelectorAll('.edit-only');
            editElements.forEach(el => {
                el.style.display = 'block';
            });
            
            // Tornar campos editáveis
            const formInputs = document.querySelectorAll('#formObra input, #formObra textarea, #formObra select');
            formInputs.forEach(input => {
                input.readOnly = false;
                if (input.tagName === 'SELECT') {
                    input.disabled = false;
                }
            });
            
            // Marcar PDF como obrigatório para novas obras
            const pdfInput = document.getElementById('obra_pdf');
            if (pdfInput) {
                pdfInput.required = true;
            }
            
            // Abrir o modal
            if (modalObraBS) {
                modalObraBS.show();
            }
        });
    }
});
</script>

<?php
// Adicionar JavaScript personalizado - remover esta linha devido ao script já estar no HTML
$extra_js = '';

// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?>