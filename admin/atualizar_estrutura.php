<?php
// Script para adicionar a coluna ano e atualizar as obras com seus anos históricos

// Conexão direta ao banco de dados
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

echo "<h2>Atualizando estrutura do banco de dados</h2>";

// 1. Verificar se a coluna ano existe e adicioná-la se necessário
$checkColumn = "SHOW COLUMNS FROM obras LIKE 'ano'";
$result = mysqli_query($conn, $checkColumn);

if (mysqli_num_rows($result) == 0) {
    // A coluna não existe, vamos adicioná-la
    $addColumn = "ALTER TABLE obras ADD COLUMN ano INT NULL AFTER autor";
    if (mysqli_query($conn, $addColumn)) {
        echo "<p style='color:green'>✓ Coluna 'ano' adicionada com sucesso à tabela 'obras'!</p>";
    } else {
        echo "<p style='color:red'>✗ Erro ao adicionar coluna 'ano': " . mysqli_error($conn) . "</p>";
        exit;
    }
} else {
    echo "<p>A coluna 'ano' já existe na tabela 'obras'.</p>";
}

// 2. Atualizar os anos das obras com valores históricos conhecidos
echo "<h3>Atualizando anos das obras...</h3>";

$obras = [
    'Conceitos Fundamentais da Matemática' => 1941,
    'Conceitos Fundamentais da Matemática-Vol.1' => 1941,
    'Conceitos Fundamentais da Matemática-Vol.2' => 1942,
    'Lições de Álgebra e Análise' => 1935,
    'A Cultura Integral do Indivíduo' => 1933,
    'J.D. Bernal - Uma Apreciação Crítica' => 1946,
    'A Cultura Integral do Homem' => 1939,
    'O Fascismo é a Guerra e a Negação da Ciência' => 1938,
    'Galileu Galilei' => 1944,
    'Aspectos do Problema Cultural Português' => 1941,
    'A Matematemática na Antiguidade' => 1942,
    'Interpolação e Integração Numérica' => 1941
];

$atualizacoes = 0;

// Iniciar transação
mysqli_begin_transaction($conn);

try {
    foreach ($obras as $titulo => $ano) {
        $titulo_sanitizado = mysqli_real_escape_string($conn, $titulo);
        
        // Atualizar o ano da obra
        $query = "UPDATE obras SET ano = ? WHERE titulo LIKE ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $ano, "%$titulo_sanitizado%");
        mysqli_stmt_execute($stmt);
        
        // Verificar se houve atualização
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $atualizacoes++;
            echo "<p>Obra '<strong>$titulo</strong>' atualizada com o ano <strong>$ano</strong>.</p>";
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Confirmar transação
    mysqli_commit($conn);
    
    echo "<h3>Resultado:</h3>";
    echo "<p>Total de <strong>$atualizacoes</strong> obras atualizadas com sucesso.</p>";
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    mysqli_rollback($conn);
    
    echo "<p style='color:red'>Erro: " . $e->getMessage() . "</p>";
}

// Mostrar link para voltar ao dashboard
echo "<div style='margin-top: 30px'>";
echo "<a href='dashboard.php' style='padding: 10px 15px; background-color: #ac062a; color: white; text-decoration: none; border-radius: 4px;'>Voltar ao Dashboard</a>";
echo "</div>";

// Fechar conexão
mysqli_close($conn);
?> 