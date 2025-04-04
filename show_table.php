<?php
// Incluir a configuração do banco de dados
$conn = require 'ConfigBD.php';

if (!$conn) {
    die("Erro de conexão com o banco de dados");
}

// Verificar se a tabela cursos existe
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'cursos'");
if (mysqli_num_rows($check_table) === 0) {
    die("A tabela 'cursos' não existe");
}

// Obter estrutura da tabela
$result = mysqli_query($conn, "DESCRIBE cursos");
if (!$result) {
    die("Erro ao consultar a estrutura da tabela: " . mysqli_error($conn));
}

echo "<h2>Estrutura da tabela 'cursos'</h2>";
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th><th>Extra</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}

echo "</table>";

// Verificar se precisamos adicionar colunas
$colunas_necessarias = [
    'imagem' => "ALTER TABLE cursos ADD COLUMN imagem VARCHAR(255) NOT NULL DEFAULT 'assets/img/courses/default.jpg' AFTER descricao",
    'link' => "ALTER TABLE cursos ADD COLUMN link VARCHAR(255) NOT NULL DEFAULT 'https://escolasbjc.pt' AFTER imagem",
    'duracao' => "ALTER TABLE cursos ADD COLUMN duracao VARCHAR(50) NOT NULL DEFAULT '3 Anos' AFTER avaliacao"
];

echo "<h2>Verificação de colunas</h2>";

foreach ($colunas_necessarias as $coluna => $sql) {
    $check_coluna = mysqli_query($conn, "SHOW COLUMNS FROM cursos LIKE '$coluna'");
    
    if (mysqli_num_rows($check_coluna) === 0) {
        echo "A coluna '$coluna' não existe. Tentando adicionar...<br>";
        
        if (mysqli_query($conn, $sql)) {
            echo "✅ Coluna '$coluna' adicionada com sucesso.<br>";
        } else {
            echo "❌ Erro ao adicionar a coluna '$coluna': " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "✅ A coluna '$coluna' já existe.<br>";
    }
}

// Mostrar dados da tabela
echo "<h2>Dados da tabela 'cursos'</h2>";
$data_result = mysqli_query($conn, "SELECT * FROM cursos");

if (!$data_result) {
    die("Erro ao consultar dados: " . mysqli_error($conn));
}

if (mysqli_num_rows($data_result) > 0) {
    echo "<table border='1'>";
    
    // Cabeçalhos da tabela
    $fields = mysqli_fetch_fields($data_result);
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    
    // Dados
    while ($row = mysqli_fetch_assoc($data_result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Não há dados na tabela 'cursos'";
}

mysqli_close($conn);
?> 