<?php
// Caminho correto para os arquivos de configuração
require_once "../config/app-config.php"; 
require_once "../config/config.php";

// Texto introdutório
$texto = '<p>Bento de Jesus Caraça (1901-1948) foi um matemático, professor, pensador e ativista português cuja vida e obra deixaram um legado duradouro na educação, matemática e na luta pela democratização da cultura em Portugal. Nascido em uma família humilde, alcançou os mais altos patamares acadêmicos por seu brilhantismo intelectual, tornando-se Professor Catedrático aos 28 anos. Sua vida foi marcada pela dedicação incansável à democratização do conhecimento e pela oposição ao regime salazarista, o que lhe custou a carreira universitária e, possivelmente, contribuiu para sua morte prematura aos 47 anos. A cronologia a seguir apresenta os momentos mais significativos de sua notável trajetória.</p>';

// Verificar se já existe um registro para o texto de introdução
$checkQuery = "SELECT id FROM textos_secoes WHERE chave = 'vida_introducao'";
$checkResult = $conn->query($checkQuery);

if ($checkResult && $checkResult->num_rows > 0) {
    // Atualizar o texto existente
    $id = $checkResult->fetch_assoc()['id'];
    $updateQuery = "UPDATE textos_secoes SET conteudo = ?, data_atualizacao = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $texto, $id);
    $result = $stmt->execute();
    
    echo $result ? "Texto atualizado com sucesso!" : "Erro ao atualizar: " . $conn->error;
} else {
    // Inserir novo texto
    $insertQuery = "INSERT INTO textos_secoes (chave, conteudo, data_criacao, data_atualizacao) VALUES ('vida_introducao', ?, NOW(), NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("s", $texto);
    $result = $stmt->execute();
    
    echo $result ? "Texto inserido com sucesso!" : "Erro ao inserir: " . $conn->error;
}

// Redirecionar após 3 segundos
echo "<script>setTimeout(function(){ window.location = 'index.php'; }, 3000);</script>";
?> 