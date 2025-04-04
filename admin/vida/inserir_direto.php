<?php
// Script simples que tenta inserir o texto diretamente
$conn = mysqli_connect('localhost', 'root', '', 'bento_jesus_caraca');

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

echo "<h1>Inserção direta do texto introdutório</h1>";

// Texto introdutório básico
$texto = "<p>Bento de Jesus Caraça (1901-1948) foi um matemático, professor, pensador e ativista português cuja vida e obra deixaram um legado duradouro na educação, matemática e na luta pela democratização da cultura em Portugal. Nascido em uma família humilde, alcançou os mais altos patamares acadêmicos por seu brilhantismo intelectual, tornando-se Professor Catedrático aos 28 anos. Sua vida foi marcada pela dedicação incansável à democratização do conhecimento e pela oposição ao regime salazarista, o que lhe custou a carreira universitária e, possivelmente, contribuiu para sua morte prematura aos 47 anos. A cronologia a seguir apresenta os momentos mais significativos de sua notável trajetória.</p>";

// Verificar se a tabela existe
$tabela_existe = mysqli_query($conn, "SHOW TABLES LIKE 'textos_secoes'");
if (mysqli_num_rows($tabela_existe) == 0) {
    echo "<p>A tabela 'textos_secoes' não existe. Criando...</p>";
    
    // Criar a tabela
    $sql_criar = "CREATE TABLE textos_secoes (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        chave VARCHAR(50) NOT NULL UNIQUE,
        conteudo TEXT NOT NULL
    )";
    
    if (mysqli_query($conn, $sql_criar)) {
        echo "<p>Tabela criada com sucesso!</p>";
    } else {
        echo "<p>Erro ao criar tabela: " . mysqli_error($conn) . "</p>";
    }
}

// Verificar se o registro já existe
$verificar = mysqli_query($conn, "SELECT id FROM textos_secoes WHERE chave = 'vida_introducao'");

if (mysqli_num_rows($verificar) > 0) {
    // Atualizar registro existente
    $id = mysqli_fetch_assoc($verificar)['id'];
    $stmt = mysqli_prepare($conn, "UPDATE textos_secoes SET conteudo = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $texto, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<p>Texto atualizado com sucesso!</p>";
    } else {
        echo "<p>Erro ao atualizar texto: " . mysqli_error($conn) . "</p>";
    }
} else {
    // Inserir novo registro
    $stmt = mysqli_prepare($conn, "INSERT INTO textos_secoes (chave, conteudo) VALUES ('vida_introducao', ?)");
    mysqli_stmt_bind_param($stmt, "s", $texto);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<p>Texto inserido com sucesso!</p>";
    } else {
        echo "<p>Erro ao inserir texto: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p><a href='index.php'>Voltar para a página principal</a></p>";

mysqli_close($conn);
?> 