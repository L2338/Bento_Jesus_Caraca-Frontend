<?php
// Configuração básica
session_start();
require_once '../config/app-config.php';
require_once '../core/functions.php';
require_login();
$conn = require '../../ConfigBD.php';

// Verificar se é um POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Obter e validar os dados do formulário
$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
$descricao = trim($_POST['descricao'] ?? '');
$tema = (int)($_POST['tema'] ?? 0);

// Validar entradas
$errors = [];

if ($id <= 0) {
    $errors[] = 'ID da imagem inválido.';
}

if (empty($descricao)) {
    $errors[] = 'A descrição da imagem é obrigatória.';
}

if ($tema <= 0) {
    $errors[] = 'Selecione uma categoria válida.';
}

// Se não houver erros, atualizamos o registro
if (empty($errors)) {
    try {
        // Verificar se a imagem existe
        $check_sql = "SELECT id_imagem FROM imagens WHERE id_imagem = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            $errors[] = 'Imagem não encontrada.';
        } else {
            // Atualizar no banco de dados
            $sql = "UPDATE imagens SET descricao = ?, id_tema_imagem = ? WHERE id_imagem = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $descricao, $tema, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensagem'] = 'Imagem atualizada com sucesso!';
                $_SESSION['tipo_mensagem'] = 'success';
            } else {
                $errors[] = 'Erro ao atualizar no banco de dados: ' . mysqli_error($conn);
            }
        }
    } catch (Exception $e) {
        $errors[] = 'Erro: ' . $e->getMessage();
    }
}

// Se houver erros, armazenamos na sessão
if (!empty($errors)) {
    $_SESSION['mensagem'] = implode('<br>', $errors);
    $_SESSION['tipo_mensagem'] = 'danger';
}

// Redirecionar de volta para a página principal
header('Location: index.php');
exit; 