<?php
/**
 * API para salvar dados de uma obra
 * Recebe os dados via POST e retorna resultado em JSON
 */

// Garantir que nenhuma saída seja enviada antes dos cabeçalhos
ob_start();

// Desativar a exibição de erros para o navegador (serão logados apenas)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Adicionar log para depuração
file_put_contents(__DIR__ . '/debug_save.log', date('Y-m-d H:i:s') . ' - Requisição recebida' . PHP_EOL, FILE_APPEND);
file_put_contents(__DIR__ . '/debug_save.log', 'POST: ' . print_r($_POST, true) . PHP_EOL, FILE_APPEND);
file_put_contents(__DIR__ . '/debug_save.log', 'FILES: ' . print_r($_FILES, true) . PHP_EOL, FILE_APPEND);

// Configurar manipuladores de erro personalizados
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    file_put_contents(__DIR__ . '/debug_save.log', "Erro PHP [$errno]: $errstr em $errfile:$errline" . PHP_EOL, FILE_APPEND);
    
    // Não deixar o erro ser exibido
    return true;
});

set_exception_handler(function($exception) {
    file_put_contents(__DIR__ . '/debug_save.log', "Exceção não capturada: " . $exception->getMessage() . PHP_EOL . $exception->getTraceAsString() . PHP_EOL, FILE_APPEND);
    
    // Garantir que a resposta seja JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro interno no servidor: ' . $exception->getMessage()]);
    exit;
});

try {
    // Incluir configurações e funções
    require_once __DIR__ . '/../config/app-config.php';
    require_once __DIR__ . '/../core/functions.php';
    require_once '../../ConfigBD.php';

    // Verificar se o usuário está logado
    require_login();

    // Configurar para JSON
    header('Content-Type: application/json');

    // Verificar se é POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit;
    }

    // Pegar dados
    $id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $titulo = $_POST['titulo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $id_tema = !empty($_POST['id_tema']) ? (int)$_POST['id_tema'] : null;
    $ano = !empty($_POST['ano']) ? (int)$_POST['ano'] : null;

    // Validar título
    if (empty($titulo)) {
        echo json_encode(['success' => false, 'message' => 'O título é obrigatório']);
        exit;
    }

    // Se for edição, buscar dados atuais
    if ($id) {
        $sql = "SELECT * FROM obras WHERE id = $id";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $obra_atual = $result->fetch_assoc();
            $pdf = $obra_atual['pdf'];
            $imagem_capa = $obra_atual['imagem_capa'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Obra não encontrada']);
            exit;
        }
    } else {
        $pdf = '';
        $imagem_capa = '';
    }

    // Processar PDF
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $diretorio_pdfs = '../../assets/pdf/Obras';
        
        if (!file_exists($diretorio_pdfs)) {
            mkdir($diretorio_pdfs, 0755, true);
        }
        
        $pdf = uniqid() . '_' . basename($_FILES['pdf']['name']);
        
        if (move_uploaded_file($_FILES['pdf']['tmp_name'], "$diretorio_pdfs/$pdf")) {
            // Se for edição, excluir PDF antigo
            if ($id && !empty($obra_atual['pdf'])) {
                $pdf_antigo = "$diretorio_pdfs/{$obra_atual['pdf']}";
                if (file_exists($pdf_antigo)) {
                    unlink($pdf_antigo);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar PDF']);
            exit;
        }
    } else if (!$id && empty($pdf)) {
        echo json_encode(['success' => false, 'message' => 'PDF obrigatório para novas obras']);
        exit;
    }

    // Processar imagem (opcional)
    if (isset($_FILES['imagem_capa']) && $_FILES['imagem_capa']['error'] === UPLOAD_ERR_OK) {
        $diretorio_imagens = '../../assets/img/obras';
        
        if (!file_exists($diretorio_imagens)) {
            mkdir($diretorio_imagens, 0755, true);
        }
        
        $imagem_capa = uniqid() . '_' . basename($_FILES['imagem_capa']['name']);
        
        if (move_uploaded_file($_FILES['imagem_capa']['tmp_name'], "$diretorio_imagens/$imagem_capa")) {
            // Se for edição, excluir imagem antiga
            if ($id && !empty($obra_atual['imagem_capa'])) {
                $imagem_antiga = "$diretorio_imagens/{$obra_atual['imagem_capa']}";
                if (file_exists($imagem_antiga)) {
                    unlink($imagem_antiga);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar imagem']);
            exit;
        }
    }

    // Salvar no banco
    if ($id) {
        // Atualizar obra existente
        if ($id_tema === null) {
            $sql = "UPDATE obras SET titulo = '$titulo', descricao = '$descricao', 
                    pdf = '$pdf', imagem_capa = '$imagem_capa', autor = '$autor', 
                    id_tema = NULL, ano = " . ($ano === null ? "NULL" : $ano) . " 
                    WHERE id = $id";
        } else {
            $sql = "UPDATE obras SET titulo = '$titulo', descricao = '$descricao', 
                    pdf = '$pdf', imagem_capa = '$imagem_capa', autor = '$autor', 
                    id_tema = $id_tema, ano = " . ($ano === null ? "NULL" : $ano) . " 
                    WHERE id = $id";
        }
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Obra atualizada com sucesso', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $conn->error]);
        }
    } else {
        // Inserir nova obra
        if ($id_tema === null) {
            $sql = "INSERT INTO obras (titulo, descricao, pdf, imagem_capa, autor, id_tema, ano) 
                    VALUES ('$titulo', '$descricao', '$pdf', '$imagem_capa', '$autor', NULL, " . 
                    ($ano === null ? "NULL" : $ano) . ")";
        } else {
            $sql = "INSERT INTO obras (titulo, descricao, pdf, imagem_capa, autor, id_tema, ano) 
                    VALUES ('$titulo', '$descricao', '$pdf', '$imagem_capa', '$autor', $id_tema, " . 
                    ($ano === null ? "NULL" : $ano) . ")";
        }
        
        if ($conn->query($sql)) {
            $novo_id = $conn->insert_id;
            echo json_encode(['success' => true, 'message' => 'Obra adicionada com sucesso', 'id' => $novo_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar: ' . $conn->error]);
        }
    }

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug_save.log', "Exceção geral: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    error_log("Erro ao salvar obra: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno no servidor: ' . $e->getMessage()]);
}

// Limpar qualquer saída em buffer e garantir que apenas JSON seja enviado
$output = ob_get_clean();
if (!empty($output)) {
    file_put_contents(__DIR__ . '/debug_save.log', "Saída capturada antes de enviar JSON: " . $output . PHP_EOL, FILE_APPEND);
} 