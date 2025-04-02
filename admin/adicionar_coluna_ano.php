<?php
// Script para adicionar a coluna ano à tabela obras

// Conexão direta ao banco de dados para garantir que funcione
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bento_caraça';  // Ajuste conforme seu banco de dados

// Conectar ao banco de dados
$conn = mysqli_connect($host, $user, $password, $database);

// Verificar conexão
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Verificar se a coluna já existe
$checkColumn = "SHOW COLUMNS FROM obras LIKE 'ano'";
$result = mysqli_query($conn, $checkColumn);

if (mysqli_num_rows($result) == 0) {
    // A coluna não existe, vamos adicioná-la
    $addColumn = "ALTER TABLE obras ADD COLUMN ano INT NULL AFTER autor";
    if (mysqli_query($conn, $addColumn)) {
        echo "Coluna 'ano' adicionada com sucesso à tabela 'obras'!";
    } else {
        echo "Erro ao adicionar coluna 'ano': " . mysqli_error($conn);
    }
} else {
    echo "A coluna 'ano' já existe na tabela 'obras'.";
}

// Fechar conexão
mysqli_close($conn);
?> 