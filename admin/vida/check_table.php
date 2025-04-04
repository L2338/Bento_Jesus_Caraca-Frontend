<?php
// Conexão com o banco de dados
$conn = mysqli_connect('localhost', 'root', '', 'bento_jesus_caraca');
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Verificar estrutura da tabela
echo "<h2>Estrutura da tabela textos_secoes</h2>";
$result = mysqli_query($conn, "DESCRIBE textos_secoes");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
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
} else {
    echo "Erro ao consultar estrutura: " . mysqli_error($conn);
}

// Verificar registros existentes
echo "<h2>Registros na tabela textos_secoes</h2>";
$result = mysqli_query($conn, "SELECT * FROM textos_secoes");
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        $first_row = mysqli_fetch_assoc($result);
        foreach (array_keys($first_row) as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";
        
        // Reposicionar o ponteiro do resultado no início
        mysqli_data_seek($result, 0);
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if ($key == 'conteudo') {
                    // Limitar o tamanho do conteúdo para não sobrecarregar a página
                    echo "<td>" . htmlspecialchars(substr($value, 0, 100)) . "...</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum registro encontrado.";
    }
} else {
    echo "Erro ao consultar registros: " . mysqli_error($conn);
}

// Proposta de SQL para criar o registro
echo "<h2>SQL para inserir texto introdutório</h2>";
echo "<pre>";
echo "INSERT INTO textos_secoes (chave, conteudo) VALUES ('vida_introducao', '&lt;p&gt;Bento de Jesus Caraça (1901-1948) foi um matemático, professor, pensador e ativista português cuja vida e obra deixaram um legado duradouro na educação, matemática e na luta pela democratização da cultura em Portugal. Nascido em uma família humilde, alcançou os mais altos patamares acadêmicos por seu brilhantismo intelectual, tornando-se Professor Catedrático aos 28 anos. Sua vida foi marcada pela dedicação incansável à democratização do conhecimento e pela oposição ao regime salazarista, o que lhe custou a carreira universitária e, possivelmente, contribuiu para sua morte prematura aos 47 anos. A cronologia a seguir apresenta os momentos mais significativos de sua notável trajetória.&lt;/p&gt;');";
echo "</pre>";

mysqli_close($conn);
?> 