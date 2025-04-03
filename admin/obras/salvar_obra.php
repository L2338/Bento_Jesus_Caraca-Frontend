<?php
/**
 * API para salvar dados de uma obra
 * Recebe os dados via POST e retorna resultado em JSON
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Conectar ao banco de dados
$conn = require_once '../../ConfigBD.php';

/**
 * Classe para operações com obras
 */
class ObraSave {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
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
     * Função auxiliar para processar upload de arquivo
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

// Criar instância da classe
$obraModel = new ObraSave($conn);

try {
    // Verificar se é uma atualização ou inserção
    $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    
    // Preparar dados da obra
    $dados = [
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'autor' => $_POST['autor'] ?? '',
        'id_tema' => $_POST['id_tema'] ?? '',
        'ano' => $_POST['ano'] ?? ''
    ];
    
    // Validar dados obrigatórios
    if (empty($dados['titulo'])) {
        echo json_encode(['success' => false, 'message' => 'O título é obrigatório']);
        exit;
    }
    
    // Se for atualização, buscar dados atuais da obra
    if ($id) {
        $obra_atual = $obraModel->buscarPorId($id);
        if (!$obra_atual) {
            echo json_encode(['success' => false, 'message' => 'Obra não encontrada']);
            exit;
        }
        
        // Manter dados de arquivos atuais
        $dados['pdf'] = $obra_atual['pdf'];
        $dados['imagem_capa'] = $obra_atual['imagem_capa'];
    }
    
    // Processar upload de PDF (se enviado)
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $diretorio_pdfs = '../../assets/pdf/Obras';
        
        // Verificar se o diretório existe, se não, criar
        if (!file_exists($diretorio_pdfs)) {
            mkdir($diretorio_pdfs, 0755, true);
        }
        
        // Tipos MIME permitidos para PDFs
        $tipos_permitidos = ['application/pdf'];
        
        // Tamanho máximo (20MB)
        $tamanho_max = 20 * 1024 * 1024;
        
        $nome_pdf = $obraModel->processarUpload($_FILES['pdf'], $diretorio_pdfs, $tipos_permitidos, $tamanho_max);
        
        if ($nome_pdf) {
            // Se for uma atualização e tiver um PDF anterior, remover
            if ($id && !empty($obra_atual['pdf'])) {
                $pdf_antigo = $diretorio_pdfs . '/' . $obra_atual['pdf'];
                if (file_exists($pdf_antigo)) {
                    unlink($pdf_antigo);
                }
            }
            
            $dados['pdf'] = $nome_pdf;
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao processar o arquivo PDF']);
            exit;
        }
    } else if (!$id) {
        // Se for uma nova obra, PDF é obrigatório
        echo json_encode(['success' => false, 'message' => 'O arquivo PDF é obrigatório para novas obras']);
        exit;
    }
    
    // Processar upload de imagem de capa (opcional)
    if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
        $diretorio_imagens = '../../assets/img/obras';
        
        // Verificar se o diretório existe, se não, criar
        if (!file_exists($diretorio_imagens)) {
            mkdir($diretorio_imagens, 0755, true);
        }
        
        // Tipos MIME permitidos para imagens
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        
        // Tamanho máximo (5MB)
        $tamanho_max = 5 * 1024 * 1024;
        
        $nome_imagem = $obraModel->processarUpload($_FILES['imagem_capa'], $diretorio_imagens, $tipos_permitidos, $tamanho_max);
        
        if ($nome_imagem) {
            // Se for uma atualização e tiver uma imagem anterior, remover
            if ($id && !empty($obra_atual['imagem_capa'])) {
                $imagem_antiga = $diretorio_imagens . '/' . $obra_atual['imagem_capa'];
                if (file_exists($imagem_antiga)) {
                    unlink($imagem_antiga);
                }
            }
            
            $dados['imagem_capa'] = $nome_imagem;
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao processar a imagem de capa']);
            exit;
        }
    }
    
    // Salvar a obra (atualizar ou inserir)
    if ($id) {
        $resultado = $obraModel->atualizar($id, $dados);
    } else {
        $resultado = $obraModel->adicionar($dados);
    }
    
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Obra salva com sucesso', 'id' => $id ?: $resultado]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar obra']);
    }
    
} catch (Exception $e) {
    error_log("Erro ao salvar obra: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno no servidor: ' . $e->getMessage()]);
} 