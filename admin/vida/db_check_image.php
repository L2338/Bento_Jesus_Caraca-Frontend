<?php
// Caminho simplificado para os arquivos de configuração
require_once "../../admin/config/app-config.php"; // Incluir primeiro para definir as constantes
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Definir o diretório de upload
$uploadDir = "../../assets/images/timeline/";

// Verificar se o diretório existe, se não, criar
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "<p>Diretório de upload criado: $uploadDir</p>";
} else {
    echo "<p>Diretório de upload: $uploadDir - Status: " . (is_writable($uploadDir) ? '<span style="color:green">OK</span>' : '<span style="color:red">Sem permissão de escrita</span>') . "</p>";
}

// Listar arquivos no diretório (simplificado)
echo "<h3>Imagens Existentes</h3>";
$files = scandir($uploadDir);
$imageCount = 0;

foreach ($files as $file) {
    if ($file != "." && $file != ".." && preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
        $imageCount++;
        if ($imageCount <= 5) { // Mostrar apenas 5 exemplos
            $imgInfo = getimagesize($uploadDir . $file);
            $dimensions = $imgInfo ? $imgInfo[0] . "x" . $imgInfo[1] . "px" : "Não disponível";
            echo "<div style='margin-bottom:10px'><b>$file</b> - $dimensions</div>";
        }
    }
}

if ($imageCount > 5) {
    echo "<p>...e mais " . ($imageCount - 5) . " imagens.</p>";
} elseif ($imageCount == 0) {
    echo "<p>Nenhuma imagem encontrada.</p>";
}

// Verificar registros no banco de dados (simplificado)
echo "<h3>Últimos 5 Registros com Imagem</h3>";
$query = "SELECT id, titulo, data_periodo, imagem FROM timeline_blocos WHERE imagem != '' ORDER BY id DESC LIMIT 5";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse'>";
    echo "<tr><th>ID</th><th>Título</th><th>Data</th><th>Imagem</th><th>Prévia</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $imagePath = $uploadDir . $row['imagem'];
        $imageExists = file_exists($imagePath);
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['titulo']}</td>";
        echo "<td>{$row['data_periodo']}</td>";
        echo "<td>{$row['imagem']}</td>";
        echo "<td>";
        if ($imageExists) {
            echo "<div style='width:60px;height:60px;border-radius:50%;overflow:hidden;margin:0 auto;'>";
            echo "<img src='../../../assets/images/timeline/{$row['imagem']}' style='width:100%;height:100%;object-fit:cover'>";
            echo "</div>";
        } else {
            echo "<span style='color:red'>Não encontrada</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum registro com imagem encontrado.</p>";
}

// Testar upload de imagem (versão simplificada)
echo "<h3>Teste de Upload</h3>";
echo "<p>Selecione uma imagem para testar o funcionamento do upload:</p>";
?>

<form action="" method="post" enctype="multipart/form-data" style="margin-bottom:20px">
    <input type="file" name="test_image" accept="image/jpeg,image/png">
    <button type="submit" name="upload_test">Testar Upload</button>
</form>

<?php
if (isset($_POST['upload_test']) && isset($_FILES['test_image'])) {
    if ($_FILES['test_image']['error'] === 0) {
        $tempFile = $_FILES['test_image']['tmp_name'];
        $targetFile = $uploadDir . 'test_' . time() . '_' . basename($_FILES['test_image']['name']);
        
        // Verificar tipo e dimensões
        $check = getimagesize($tempFile);
        if ($check !== false) {
            echo "<p>Dimensões da imagem: {$check[0]}x{$check[1]} pixels</p>";
            
            // Tentar fazer o upload
            if (move_uploaded_file($tempFile, $targetFile)) {
                echo "<div style='color:green;margin:10px 0'>✓ Upload realizado com sucesso</div>";
                
                // Exibir a imagem como será vista na timeline
                echo "<div style='display:flex;align-items:center;margin-top:10px'>";
                echo "<div style='width:120px;height:120px;border-radius:50%;overflow:hidden;border:2px solid #ddd'>";
                echo "<img src='../../../assets/images/timeline/" . basename($targetFile) . "' style='width:100%;height:100%;object-fit:cover'>";
                echo "</div>";
                echo "<div style='margin-left:15px'>Prévia no círculo da timeline (120x120px)</div>";
                echo "</div>";
            } else {
                echo "<div style='color:red;margin:10px 0'>✗ Falha no upload</div>";
            }
        } else {
            echo "<div style='color:red;margin:10px 0'>✗ O arquivo enviado não é uma imagem válida</div>";
        }
    } else {
        echo "<div style='color:red;margin:10px 0'>✗ Erro no upload: " . $_FILES['test_image']['error'] . "</div>";
    }
}

// Resumo de configurações (informações mais relevantes)
echo "<h3>Resumo de Configurações</h3>";
echo "<ul>";
echo "<li>Tamanho do círculo na timeline: <b>120x120 pixels</b></li>";
echo "<li>Dimensões ideais: <b>150x150</b> até <b>600x600</b> pixels</li>";
echo "<li>Formatos permitidos: <b>JPG, JPEG, PNG</b></li>";
echo "<li>Tamanho máximo: <b>3MB</b></li>";
echo "</ul>";

?> 