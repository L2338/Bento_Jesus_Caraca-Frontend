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

// Verificar se a coluna ano existe
$checkAnoColumn = "SHOW COLUMNS FROM obras LIKE 'ano'";
$anoColumnResult = mysqli_query($conn, $checkAnoColumn);
$anoExists = (mysqli_num_rows($anoColumnResult) > 0);

// Construir consulta SQL
$query = "SELECT o.*";
if ($anoExists) {
    // Se a coluna ano existir, incluí-la na consulta
    $query .= ", t.Nome_tema as categoria FROM obras o LEFT JOIN Temas t ON o.id_tema = t.id_tema WHERE o.id = ?";
} else {
    // Se não existir, fazer a consulta sem essa coluna
    $query .= ", t.Nome_tema as categoria FROM obras o LEFT JOIN Temas t ON o.id_tema = t.id_tema WHERE o.id = ?";
}

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