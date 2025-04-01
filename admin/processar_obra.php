<?php
session_start();

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir conexão com o banco de dados
    $conn = require '../ConfigBD.php';
    
    // Obter dados do formulário e sanitizar
    $obraId = isset($_POST['obra_id']) ? intval($_POST['obra_id']) : 0;
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
    
    // Iniciar transação
    mysqli_begin_transaction($conn);
    
    try {
        // Verificar se é atualização ou inserção
        if ($obraId > 0) {
            // Atualização de obra existente
            $query = "UPDATE obras SET 
                      titulo = ?, 
                      autor = ?, 
                      descricao = ?";
            
            $params = [$titulo, $autor, $descricao];
            $types = "sss";
            
            // Processar arquivo PDF se enviado
            if (!empty($_FILES['arquivo']['name'])) {
                $arquivoNome = processarArquivo('arquivo', '../assets/pdf/Obras/');
                if ($arquivoNome !== false) {
                    $query .= ", pdf = ?";
                    $params[] = $arquivoNome;
                    $types .= "s";
                }
            }
            
            // Processar imagem se enviada
            if (!empty($_FILES['imagem']['name'])) {
                $imagemNome = processarArquivo('imagem', '../assets/img/obras/');
                if ($imagemNome !== false) {
                    $query .= ", imagem_capa = ?";
                    $params[] = $imagemNome;
                    $types .= "s";
                }
            }
            
            $query .= " WHERE id = ?";
            $params[] = $obraId;
            $types .= "i";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $result = mysqli_stmt_execute($stmt);
            
            if (!$result) {
                throw new Exception("Erro ao atualizar obra: " . mysqli_error($conn));
            }
            
            $mensagem = "Obra atualizada com sucesso!";
        } else {
            // Inserção de nova obra
            $query = "INSERT INTO obras (titulo, autor, descricao";
            $params = [$titulo, $autor, $descricao];
            $types = "sss";
            
            // Processar arquivo PDF se enviado
            if (!empty($_FILES['arquivo']['name'])) {
                $arquivoNome = processarArquivo('arquivo', '../assets/pdf/Obras/');
                if ($arquivoNome !== false) {
                    $query .= ", pdf";
                    $params[] = $arquivoNome;
                    $types .= "s";
                }
            }
            
            // Processar imagem se enviada
            if (!empty($_FILES['imagem']['name'])) {
                $imagemNome = processarArquivo('imagem', '../assets/img/obras/');
                if ($imagemNome !== false) {
                    $query .= ", imagem_capa";
                    $params[] = $imagemNome;
                    $types .= "s";
                }
            }
            
            $query .= ") VALUES (" . str_repeat("?,", count($params) - 1) . "?)";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $result = mysqli_stmt_execute($stmt);
            
            if (!$result) {
                throw new Exception("Erro ao inserir obra: " . mysqli_error($conn));
            }
            
            $mensagem = "Obra cadastrada com sucesso!";
        }
        
        // Confirmar transação
        mysqli_commit($conn);
        
        // Redirecionar com mensagem de sucesso
        $_SESSION['mensagem'] = $mensagem;
        $_SESSION['tipo_mensagem'] = 'success';
        
    } catch (Exception $e) {
        // Reverter transação em caso de erro
        mysqli_rollback($conn);
        
        // Registrar erro
        error_log("Erro em processar_obra.php: " . $e->getMessage());
        
        // Redirecionar com mensagem de erro
        $_SESSION['mensagem'] = "Erro ao processar obra: " . $e->getMessage();
        $_SESSION['tipo_mensagem'] = 'danger';
    }
    
    // Fechar conexão
    mysqli_close($conn);
    
    // Redirecionar de volta para o dashboard
    header('Location: dashboard.php');
    exit();
}

/**
 * Função para processar upload de arquivo
 * 
 * @param string $inputName Nome do campo de upload no formulário
 * @param string $diretorio Diretório onde o arquivo será salvo
 * @return string|false Nome do arquivo em caso de sucesso, false em caso de erro
 */
function processarArquivo($inputName, $diretorio) {
    // Verificar se o diretório existe, se não, tentar criar
    if (!is_dir($diretorio)) {
        if (!mkdir($diretorio, 0755, true)) {
            error_log("Não foi possível criar o diretório: " . $diretorio);
            return false;
        }
    }
    
    // Verificar se o diretório tem permissão de escrita
    if (!is_writable($diretorio)) {
        error_log("Diretório sem permissão de escrita: " . $diretorio);
        return false;
    }
    
    // Obter extensão do arquivo
    $extensao = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
    
    // Verificar tipo de arquivo
    if ($inputName === 'arquivo' && $extensao !== 'pdf') {
        error_log("Tipo de arquivo não permitido para PDF: " . $extensao);
        return false;
    }
    
    if ($inputName === 'imagem' && !in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
        error_log("Tipo de arquivo não permitido para imagem: " . $extensao);
        return false;
    }
    
    // Gerar nome de arquivo único
    $novoNome = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $_FILES[$inputName]['name']);
    
    // Caminho completo do arquivo
    $caminhoCompleto = $diretorio . $novoNome;
    
    // Tentar fazer o upload
    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $caminhoCompleto)) {
        return $novoNome;
    } else {
        error_log("Falha ao fazer upload do arquivo para: " . $caminhoCompleto);
        return false;
    }
} 