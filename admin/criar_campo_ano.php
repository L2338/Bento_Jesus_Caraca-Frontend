<?php
// Script para adicionar o campo "ano" à tabela obras

// Incluir conexão com o banco de dados
require_once '../ConfigBD.php';

// Verificar conexão
if (!isset($conn)) {
    die("Falha na conexão com o banco de dados");
}

try {
    // Verificar se o campo "ano" já existe na tabela
    $query = "SHOW COLUMNS FROM obras LIKE 'ano'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 0) {
        // Campo não existe, vamos adicioná-lo
        $alterQuery = "ALTER TABLE obras ADD COLUMN ano INT NULL AFTER autor";
        if (mysqli_query($conn, $alterQuery)) {
            echo "Campo 'ano' adicionado com sucesso à tabela 'obras'.<br>";
        } else {
            throw new Exception("Erro ao adicionar campo 'ano': " . mysqli_error($conn));
        }
    } else {
        echo "O campo 'ano' já existe na tabela 'obras'.<br>";
    }
    
    // Atualizar os anos das obras
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
    
    // Contador de atualizações
    $atualizacoes = 0;
    
    // Percorrer obras e atualizar os anos
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
            echo "Obra '$titulo' atualizada com o ano $ano.<br>";
        }
        
        mysqli_stmt_close($stmt);
    }
    
    echo "<br>Total de $atualizacoes obras atualizadas com sucesso.";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
    error_log("Erro em criar_campo_ano.php: " . $e->getMessage());
}

// Fechar conexão
mysqli_close($conn);
?> 