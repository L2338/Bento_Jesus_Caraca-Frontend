<?php
session_start();

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_POST['obra_id']) || !is_numeric($_POST['obra_id'])) {
    $_SESSION['mensagem'] = 'ID de obra inválido';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: dashboard.php');
    exit();
}

// Obter ID da obra
$id = intval($_POST['obra_id']);

// Incluir conexão com o banco de dados
$conn = require '../ConfigBD.php';

// Iniciar transação
mysqli_begin_transaction($conn);

try {
    // Primeiro obter informações da obra para excluir arquivos físicos
    $query = "SELECT pdf, imagem_capa FROM obras WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception("Obra não encontrada");
    }
    
    $obra = mysqli_fetch_assoc($result);
    
    // Excluir a obra do banco de dados
    $query = "DELETE FROM obras WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    
    if (!$result) {
        throw new Exception("Erro ao excluir obra: " . mysqli_error($conn));
    }
    
    // Verificar se houve exclusão
    if (mysqli_affected_rows($conn) === 0) {
        throw new Exception("Obra não encontrada para exclusão");
    }
    
    // Excluir arquivo PDF se existir
    if (!empty($obra['pdf'])) {
        $caminhoPDF = '../assets/pdf/Obras/' . $obra['pdf'];
        if (file_exists($caminhoPDF)) {
            if (!unlink($caminhoPDF)) {
                error_log("Não foi possível excluir o arquivo: " . $caminhoPDF);
            }
        }
    }
    
    // Excluir imagem se existir
    if (!empty($obra['imagem_capa'])) {
        $caminhoImagem = '../assets/img/obras/' . $obra['imagem_capa'];
        if (file_exists($caminhoImagem)) {
            if (!unlink($caminhoImagem)) {
                error_log("Não foi possível excluir a imagem: " . $caminhoImagem);
            }
        }
    }
    
    // Confirmar transação
    mysqli_commit($conn);
    
    // Definir mensagem de sucesso
    $_SESSION['mensagem'] = "Obra excluída com sucesso";
    $_SESSION['tipo_mensagem'] = 'success';
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    mysqli_rollback($conn);
    
    // Registrar erro
    error_log("Erro em excluir_obra.php: " . $e->getMessage());
    
    // Definir mensagem de erro
    $_SESSION['mensagem'] = "Erro ao excluir obra: " . $e->getMessage();
    $_SESSION['tipo_mensagem'] = 'danger';
}

// Fechar conexão
mysqli_close($conn);

// Redirecionar de volta para o dashboard
header('Location: dashboard.php');
exit(); 