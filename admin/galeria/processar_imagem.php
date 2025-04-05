<?php
// Habilitar exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de depuração
$debug_log = fopen("debug_upload.log", "a");
fwrite($debug_log, "=== Nova requisição em " . date('Y-m-d H:i:s') . " ===\n");
fwrite($debug_log, "Método: " . $_SERVER['REQUEST_METHOD'] . "\n");

// Configuração básica
session_start();
require_once '../config/app-config.php';
require_once '../core/functions.php';
require_login();
$conn = require '../../ConfigBD.php';

// Verificar se é um POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fwrite($debug_log, "Erro: Requisição não é POST\n");
    fclose($debug_log);
    
    $_SESSION['mensagem'] = 'Erro: Método de requisição inválido.';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: index.php');
    exit;
}

// Log de dados recebidos
fwrite($debug_log, "POST data: " . print_r($_POST, true) . "\n");
fwrite($debug_log, "FILES data: " . print_r($_FILES, true) . "\n");

// Diretório para upload
$upload_dir = '../../assets/img/galeria/';
fwrite($debug_log, "Diretório de upload: $upload_dir\n");

// Verificar se o diretório existe, caso contrário cria
if (!file_exists($upload_dir)) {
    fwrite($debug_log, "Diretório não existe, tentando criar...\n");
    $mkdir_result = mkdir($upload_dir, 0755, true);
    fwrite($debug_log, "Resultado da criação: " . ($mkdir_result ? "Sucesso" : "Falha") . "\n");
}

// Verificar permissões do diretório
fwrite($debug_log, "Permissões do diretório: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "\n");
fwrite($debug_log, "Diretório gravável: " . (is_writable($upload_dir) ? "Sim" : "Não") . "\n");

// Verificar se o diretório de miniaturas existe, caso contrário cria
$thumbs_dir = $upload_dir . 'thumbs/';
if (!file_exists($thumbs_dir)) {
    fwrite($debug_log, "Diretório de miniaturas não existe, tentando criar...\n");
    $mkdir_result = mkdir($thumbs_dir, 0755, true);
    fwrite($debug_log, "Resultado da criação: " . ($mkdir_result ? "Sucesso" : "Falha") . "\n");
}

// Processar formulário
$descricao = trim($_POST['descricao'] ?? '');
$tema = (int)($_POST['tema'] ?? 0);

// Validar entradas
$errors = [];

if (empty($descricao)) {
    $errors[] = 'A descrição da imagem é obrigatória.';
}

if ($tema <= 0) {
    $errors[] = 'Selecione uma categoria válida.';
}

// Verificar o upload do arquivo
if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Ocorreu um erro no upload da imagem. Código: ' . ($_FILES['imagem']['error'] ?? 'desconhecido');
} else {
    // Verificar o tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/png'];
    $file_type = $_FILES['imagem']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $errors[] = 'Apenas imagens JPG e PNG são permitidas.';
    }
    
    // Verificar tamanho do arquivo (máximo 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB em bytes
    if ($_FILES['imagem']['size'] > $max_size) {
        $errors[] = 'O tamanho máximo permitido é 5MB.';
    }
}

// Se não houver erros, processamos o upload
if (empty($errors)) {
    try {
        // Gerar nome único para o arquivo
        $file_extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $file_destination = $upload_dir . $new_filename;
        
        // Mover o arquivo para o diretório
        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $file_destination)) {
            // Criar miniatura
            $thumb_destination = $thumbs_dir . $new_filename;
            create_thumbnail($file_destination, $thumb_destination, 200);
            
            // Preparar o caminho relativo para salvar no banco de dados
            $db_path = 'assets/img/galeria/' . $new_filename;
            
            // Log para depuração do caminho
            fwrite($debug_log, "Caminho salvo no BD: $db_path\n");
            
            // Inserir no banco de dados
            $sql = "INSERT INTO imagens (descricao, imagem, id_tema_imagem) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $descricao, $db_path, $tema);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensagem'] = 'Imagem adicionada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Erro ao salvar no banco de dados: ' . mysqli_error($conn);
                
                // Caso ocorra erro no banco, removemos o arquivo já enviado
                if (file_exists($file_destination)) {
                    unlink($file_destination);
                }
                if (file_exists($thumb_destination)) {
                    unlink($thumb_destination);
                }
            }
        } else {
            $errors[] = 'Falha ao mover o arquivo enviado.';
        }
    } catch (Exception $e) {
        $errors[] = 'Erro: ' . $e->getMessage();
    }
}

// Se houver erros, voltamos para a página com mensagens de erro
if (!empty($errors)) {
    $_SESSION['mensagem'] = implode('<br>', $errors);
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: index.php');
    exit;
}

/**
 * Função para criar miniatura
 */
function create_thumbnail($source, $destination, $max_width) {
    list($width, $height) = getimagesize($source);
    
    // Calcular proporção para redimensionar
    $ratio = $max_width / $width;
    $new_width = $max_width;
    $new_height = $height * $ratio;
    
    // Criar nova imagem
    $thumb = imagecreatetruecolor($new_width, $new_height);
    
    // Carregar a imagem original
    $source_ext = pathinfo($source, PATHINFO_EXTENSION);
    
    if ($source_ext == 'jpg' || $source_ext == 'jpeg') {
        $source_image = imagecreatefromjpeg($source);
    } elseif ($source_ext == 'png') {
        $source_image = imagecreatefrompng($source);
        
        // Preservar transparência para PNG
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    } else {
        return false;
    }
    
    // Redimensionar
    imagecopyresampled($thumb, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Salvar miniatura
    if ($source_ext == 'jpg' || $source_ext == 'jpeg') {
        imagejpeg($thumb, $destination, 85);
    } elseif ($source_ext == 'png') {
        imagepng($thumb, $destination, 8);
    }
    
    // Liberar memória
    imagedestroy($source_image);
    imagedestroy($thumb);
    
    return true;
} 