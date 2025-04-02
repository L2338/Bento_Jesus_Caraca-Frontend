<?php
// Este script cria a tabela 'obras' no banco de dados
// Execute-o uma vez para configurar o banco de dados

// Incluir conexão
$conn = require '../ConfigBD.php';

// SQL para criar a tabela
$sql = "CREATE TABLE IF NOT EXISTS obras (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(100) DEFAULT 'Bento de Jesus Caraça',
    ano INT(4),
    categoria VARCHAR(50),
    descricao TEXT,
    arquivo VARCHAR(255),
    imagem VARCHAR(255),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

// Executar SQL
if (mysqli_query($conn, $sql)) {
    echo "Tabela 'obras' criada com sucesso ou já existente";
} else {
    echo "Erro ao criar tabela: " . mysqli_error($conn);
}

// Fechar conexão
mysqli_close($conn); 