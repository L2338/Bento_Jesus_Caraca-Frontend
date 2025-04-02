<?php
// Incluir conexão com banco de dados
$conn = require 'ConfigBD.php';

// Nova ordem para os blocos
$newOrder = [
    1 => 1,    // Nascimento e Primeiros Anos (1901-1906)
    2 => 2,    // Estudos Primários (1907-1910)
    3 => 3,    // Liceu de Santarém (1911-1914)
    4 => 4,    // Liceu Pedro Nunes em Lisboa (1914-1918)
    5 => 5,    // Ingresso no Ensino Superior (1918-1919)
    6 => 6,    // Início da Carreira Docente (1919-1923)
    7 => 7,    // Ascensão na Carreira Acadêmica (1924-1927)
    8 => 8,    // Intervenção Social e Cátedra (1928-1929)
    9 => 9,    // Consolidação Acadêmica (1930-1932)
    10 => 10,  // Cultura Integral e Oposição (1933-1934)
    11 => 11,  // Lições de Álgebra e Ativismo (1935-1936)
    12 => 12,  // Atividade Científica e Viagens (1937-1938)
    24 => 13,  // A Comuna Estrela (1937-1940)
    13 => 14,  // Gazeta de Matemática (1939-1940)
    14 => 15,  // Biblioteca Cosmos (1941-1942)
    15 => 16,  // Nova Fase Pessoal e Profissional (1943-1944)
    16 => 17,  // Ciência e Resistência (1944-1945)
    23 => 18,  // A Polémica com António Sérgio (1945)
    17 => 19,  // Expulsão e Perseguição (1946)
    18 => 20,  // Perseguição e Resistência (1947)
    19 => 21,  // Últimos Meses de Vida (1948)
    20 => 22,  // Falecimento e Legado (1948)
    21 => 23,  // Homenagens Póstumas (1949-1958)
    25 => 24,  // Influências Filosóficas (1930-1948)
    26 => 25,  // Legado Pedagógico (1935-Presente)
    22 => 26,  // Reconhecimento Nacional (1974-Presente)
    27 => 27   // Um Legado Eterno (Legado Eterno)
];

echo "<h1>Reorganizando Timeline</h1>";
echo "<p>Iniciando reorganização...</p>";

// Para cada bloco, atualizar sua ordem
foreach ($newOrder as $blockId => $newOrderValue) {
    $query = "UPDATE timeline_blocos SET ordem = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $newOrderValue, $blockId);
    
    if ($stmt->execute()) {
        echo "<p>Bloco ID $blockId: Nova ordem $newOrderValue - Atualizado com sucesso</p>";
    } else {
        echo "<p style='color:red'>Erro ao atualizar bloco ID $blockId: " . $stmt->error . "</p>";
    }
}

echo "<p>Reorganização concluída!</p>";
echo "<p><a href='vida.php'>Voltar para a página da timeline</a></p>";
?> 