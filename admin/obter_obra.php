<?php
session_start();

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

// Obter ID da obra
$id = intval($_GET['id']);

// Incluir conexão com o banco de dados
$conn = require '../ConfigBD.php';

// Consultar obra por ID
$query = "SELECT * FROM obras WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificar se a obra foi encontrada
if (mysqli_num_rows($result) === 0) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Obra não encontrada']);
    exit();
}

// Obter dados da obra
$obra = mysqli_fetch_assoc($result);

// Fechar consulta e conexão
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Retornar dados em formato JSON
header('Content-Type: application/json');
echo json_encode($obra);
exit(); 