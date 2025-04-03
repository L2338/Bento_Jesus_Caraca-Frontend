<?php
/**
 * API para buscar dados de uma obra específica
 * Retorna os dados em formato JSON para uso no modal
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Configurar cabeçalho para JSON
header('Content-Type: application/json');

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID não fornecido']);
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados e criar a classe Obra
$conn = require_once '../../ConfigBD.php';

/**
 * Classe para operações de obras - versão simplificada apenas para get_obra.php
 */
class ObraGet {
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
}

// Criar instância da classe Obra
$obraModel = new ObraGet($conn);

// Buscar a obra pelo ID
$obra = $obraModel->buscarPorId($id);

if (!$obra) {
    echo json_encode(['error' => 'Obra não encontrada']);
    exit;
}

// Retornar os dados da obra
echo json_encode($obra); 