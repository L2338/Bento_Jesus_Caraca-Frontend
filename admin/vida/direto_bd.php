<?php
// Conexão direta com o banco de dados usando PDO
try {
    $conn = new PDO('mysql:host=localhost;dbname=bento_jesus_caraca', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Texto introdutório
    $texto = '<p>Bento de Jesus Caraça (1901-1948) foi um matemático, professor, pensador e ativista português cuja vida e obra deixaram um legado duradouro na educação, matemática e na luta pela democratização da cultura em Portugal. Nascido em uma família humilde, alcançou os mais altos patamares acadêmicos por seu brilhantismo intelectual, tornando-se Professor Catedrático aos 28 anos. Sua vida foi marcada pela dedicação incansável à democratização do conhecimento e pela oposição ao regime salazarista, o que lhe custou a carreira universitária e, possivelmente, contribuiu para sua morte prematura aos 47 anos. A cronologia a seguir apresenta os momentos mais significativos de sua notável trajetória.</p>';
    
    // Verificar se já existe um registro para o texto de introdução
    $checkQuery = "SELECT id FROM textos_secoes WHERE chave = 'vida_introducao'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Atualizar o texto existente
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        
        $updateQuery = "UPDATE textos_secoes SET conteudo = :conteudo WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':conteudo', $texto);
        $stmt->bindParam(':id', $id);
        $result = $stmt->execute();
        
        echo $result ? "Texto atualizado com sucesso!" : "Erro ao atualizar.";
    } else {
        // Inserir novo texto
        $insertQuery = "INSERT INTO textos_secoes (chave, conteudo) VALUES ('vida_introducao', :conteudo)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':conteudo', $texto);
        $result = $stmt->execute();
        
        echo $result ? "Texto inserido com sucesso!" : "Erro ao inserir.";
    }
    
    // Redirecionar após 3 segundos
    echo "<script>setTimeout(function(){ window.location = 'index.php'; }, 3000);</script>";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 