<?php
// Script para atualizar os anos das obras principais de Bento de Jesus Caraça

// Incluir conexão com o banco de dados
require_once '../ConfigBD.php';

// Verificar conexão
if (!isset($conn)) {
    die("Falha na conexão com o banco de dados");
}

// Dados das obras com seus respectivos anos de publicação
$obras = [
    'Conceitos Fundamentais da Matemática' => 1941,
    'Lições de Álgebra e Análise' => 1935,
    'A Cultura Integral do Indivíduo' => 1933,
    'J.D. Bernal - Uma Apreciação Crítica' => 1946,
    'Conferências e Outros Escritos' => 1978, // Publicação póstuma
    'A Cultura Integral do Homem' => 1939,
    'O Fascismo é a Guerra e a Negação da Ciência' => 1938,
    'Galileu Galilei' => 1944,
    'Aspectos do Problema Cultural Português' => 1941,
    'A Matematemática na Antiguidade' => 1942
];

// Iniciar transação
mysqli_begin_transaction($conn);

try {
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
    
    // Confirmar transação
    mysqli_commit($conn);
    
    echo "<br>Total de $atualizacoes obras atualizadas com sucesso.";
    
} catch (Exception $e) {
    // Reverter transação em caso de erro
    mysqli_rollback($conn);
    
    echo "Erro ao atualizar anos das obras: " . $e->getMessage();
    error_log("Erro em atualizar_anos_obras.php: " . $e->getMessage());
}

// Fechar conexão
mysqli_close($conn);
?> 