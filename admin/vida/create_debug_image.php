<?php
// Script de diagnóstico para problemas com upload de imagens
require_once "../../admin/config/app-config.php";
require_once "../../admin/config/config.php";
require_once "../../admin/core/functions.php";

// Verificar se o usuário está logado
require_login();

// Definir o diretório de upload
$uploadDir = "../../assets/images/timeline/";

// Verificar permissões do servidor
echo "<h1>Diagnóstico de Upload de Imagens</h1>";

// 1. Verificar se o diretório existe e tem permissões corretas
echo "<h2>1. Verificação do Diretório</h2>";
if (!file_exists($uploadDir)) {
    echo "<p style='color:red'>O diretório de upload não existe!</p>";
    echo "<p>Tentando criar o diretório...</p>";
    
    if (mkdir($uploadDir, 0777, true)) {
        echo "<p style='color:green'>Diretório criado com sucesso!</p>";
    } else {
        echo "<p style='color:red'>Falha ao criar o diretório. Erro: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:green'>O diretório de upload existe.</p>";
}

// Verificar permissões
$perms = substr(sprintf('%o', fileperms($uploadDir)), -4);
$isWritable = is_writable($uploadDir);

echo "<p>Caminho do diretório: <code>$uploadDir</code></p>";
echo "<p>Permissões: <code>$perms</code></p>";
echo "<p>Gravável: <strong style='color:" . ($isWritable ? "green'>Sim" : "red'>Não") . "</strong></p>";

if (!$isWritable) {
    echo "<p style='color:red'>O PHP não tem permissão para gravar neste diretório. Conserte isso antes de continuar.</p>";
    echo "<p>Execute: <code>chmod 755 $uploadDir</code> ou <code>chmod 777 $uploadDir</code> no servidor.</p>";
}

// 2. Verificar configurações do PHP
echo "<h2>2. Configurações do PHP</h2>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "<li>file_uploads habilitado: " . (ini_get('file_uploads') ? 'Sim' : 'Não') . "</li>";
echo "</ul>";

// 3. Testar upload manual
echo "<h2>3. Teste de Upload</h2>";
?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="test_image" accept="image/jpeg,image/png">
    <button type="submit" name="upload_test">Testar Upload</button>
</form>

<?php
if (isset($_POST['upload_test']) && isset($_FILES['test_image'])) {
    echo "<h3>Resultados do teste:</h3>";
    echo "<pre>";
    print_r($_FILES['test_image']);
    echo "</pre>";
    
    if ($_FILES['test_image']['error'] === 0) {
        $tempFile = $_FILES['test_image']['tmp_name'];
        $targetFile = $uploadDir . 'teste_debug_' . time() . '.' . pathinfo($_FILES['test_image']['name'], PATHINFO_EXTENSION);
        
        echo "<p>Arquivo temporário: $tempFile</p>";
        echo "<p>Arquivo destino: $targetFile</p>";
        
        if (file_exists($tempFile)) {
            echo "<p style='color:green'>Arquivo temporário existe.</p>";
            
            // Verificar imagem
            $imgInfo = getimagesize($tempFile);
            if ($imgInfo) {
                echo "<p style='color:green'>É uma imagem válida.</p>";
                echo "<p>Dimensões: {$imgInfo[0]} x {$imgInfo[1]} pixels</p>";
                echo "<p>Tipo MIME: {$imgInfo['mime']}</p>";
                
                // Tentar mover
                if (move_uploaded_file($tempFile, $targetFile)) {
                    echo "<p style='color:green'>Upload realizado com sucesso!</p>";
                    echo "<p>O arquivo foi salvo em: $targetFile</p>";
                    
                    if (file_exists($targetFile)) {
                        echo "<p>Verificação final: o arquivo existe no destino.</p>";
                        echo "<p>Tamanho do arquivo: " . filesize($targetFile) . " bytes</p>";
                        echo "<p>Prévia da imagem:</p>";
                        echo "<img src='../../../assets/images/timeline/" . basename($targetFile) . "' style='max-width:300px; border:1px solid #ccc;'>";
                        
                        // Exibir como apareceria no círculo da timeline
                        echo "<p>Prévia no círculo da timeline:</p>";
                        echo "<div style='width:120px;height:120px;border-radius:50%;overflow:hidden;border:2px solid #ddd;'>";
                        echo "<img src='../../../assets/images/timeline/" . basename($targetFile) . "' style='width:100%;height:100%;object-fit:cover;'>";
                        echo "</div>";
                    } else {
                        echo "<p style='color:red'>ERRO: O arquivo não existe no destino após o upload!</p>";
                    }
                } else {
                    echo "<p style='color:red'>ERRO: Não foi possível mover o arquivo para o destino.</p>";
                    echo "<p>Detalhes do erro:</p>";
                    echo "<pre>";
                    print_r(error_get_last());
                    echo "</pre>";
                }
            } else {
                echo "<p style='color:red'>O arquivo não é uma imagem válida.</p>";
            }
        } else {
            echo "<p style='color:red'>ERRO: O arquivo temporário não existe!</p>";
        }
    } else {
        echo "<p style='color:red'>ERRO no upload: " . $_FILES['test_image']['error'] . "</p>";
        
        // Mapear códigos de erro para mensagens
        $errorMessages = [
            1 => 'O arquivo excede o tamanho máximo permitido pelo servidor (upload_max_filesize)',
            2 => 'O arquivo excede o tamanho máximo permitido pelo formulário (MAX_FILE_SIZE)',
            3 => 'O upload foi parcial',
            4 => 'Nenhum arquivo foi enviado',
            6 => 'Pasta temporária ausente',
            7 => 'Falha ao gravar arquivo em disco',
            8 => 'Uma extensão PHP interrompeu o upload'
        ];
        
        echo "<p>Significado do erro: " . ($errorMessages[$_FILES['test_image']['error']] ?? 'Erro desconhecido') . "</p>";
    }
}

// 4. Verificar arquivos existentes na pasta
echo "<h2>4. Arquivos Existentes</h2>";
$files = scandir($uploadDir);
if (count($files) > 2) {
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li><code>$file</code> (" . filesize($uploadDir . $file) . " bytes)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Nenhum arquivo encontrado no diretório de upload.</p>";
}

// 5. Verificar registros no banco de dados
echo "<h2>5. Registros no Banco de Dados</h2>";
$query = "SELECT id, titulo, data_periodo, imagem FROM timeline_blocos WHERE imagem != '' ORDER BY id DESC LIMIT 10";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Título</th><th>Data/Período</th><th>Nome da Imagem</th><th>Arquivo Existe</th><th>Prévia</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $imagePath = $uploadDir . $row['imagem'];
        $fileExists = file_exists($imagePath);
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['titulo']}</td>";
        echo "<td>{$row['data_periodo']}</td>";
        echo "<td><code>{$row['imagem']}</code></td>";
        echo "<td style='color:" . ($fileExists ? "green'>Sim" : "red'>Não") . "</td>";
        echo "<td>";
        
        if ($fileExists) {
            echo "<div style='width:60px;height:60px;border-radius:50%;overflow:hidden;'>";
            echo "<img src='../../../assets/images/timeline/{$row['imagem']}?v=" . time() . "' style='width:100%;height:100%;object-fit:cover;'>";
            echo "</div>";
        } else {
            echo "Não disponível";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Nenhum registro com imagem encontrado na tabela timeline_blocos.</p>";
}

// 6. Testes do formulário
echo "<h2>6. Teste do Formulário</h2>";
?>

<p>Antes de usar esta ferramenta, certifique-se de ter verificado:</p>
<ol>
    <li>O formulário em <code>timeline_edit.php</code> tem o atributo <code>enctype="multipart/form-data"</code>.</li>
    <li>O campo de input tem <code>name="imagem"</code> e <code>type="file"</code>.</li>
    <li>O PHP está processando corretamente a variável <code>$_FILES['imagem']</code>.</li>
</ol>

<h3>Teste do atributo enctype</h3>
<form action="" method="post">
    <input type="hidden" name="check_form" value="1">
    <button type="submit">Verificar formulário em timeline_edit.php</button>
</form>

<?php
if (isset($_POST['check_form'])) {
    $editFile = file_get_contents('timeline_edit.php');
    if (preg_match('/enctype\s*=\s*[\'"]multipart\/form-data[\'"]/', $editFile)) {
        echo "<p style='color:green'>O formulário tem o atributo enctype correto!</p>";
    } else {
        echo "<p style='color:red'>PROBLEMA: O formulário não tem o atributo enctype=\"multipart/form-data\"!</p>";
        echo "<p>Adicione este atributo ao elemento form para permitir o upload de arquivos.</p>";
    }
    
    if (preg_match('/<input[^>]*type\s*=\s*[\'"]file[\'"][^>]*name\s*=\s*[\'"]imagem[\'"]/', $editFile)) {
        echo "<p style='color:green'>O campo de upload de imagem está configurado corretamente!</p>";
    } else {
        echo "<p style='color:red'>PROBLEMA: O campo de upload de imagem não está configurado corretamente!</p>";
        echo "<p>Verifique se existe um input com type=\"file\" e name=\"imagem\".</p>";
    }
}

// 7. Informações sobre o problema e soluções
echo "<h2>7. Possíveis Soluções</h2>";
?>

<div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin-top: 20px;">
    <h3>Problema: Imagens não são atualizadas na base de dados</h3>
    
    <h4>Possíveis causas:</h4>
    <ul>
        <li><strong>Permissões de diretório:</strong> O PHP não consegue escrever no diretório de upload.</li>
        <li><strong>Configuração do formulário:</strong> Falta o atributo enctype="multipart/form-data".</li>
        <li><strong>Validação rejeitando a imagem:</strong> A imagem não passa em alguma validação (dimensões, tamanho, formato).</li>
        <li><strong>Erros de upload:</strong> Problemas ao mover o arquivo do diretório temporário para o destino final.</li>
        <li><strong>Cache do navegador:</strong> O navegador mostra a imagem antiga em vez da nova devido ao cache.</li>
    </ul>
    
    <h4>Soluções:</h4>
    <ol>
        <li>Dê permissão de escrita para o diretório de upload: <code>chmod 755</code> ou <code>chmod 777</code></li>
        <li>Verifique se o formulário tem <code>enctype="multipart/form-data"</code></li>
        <li>Use diferentes imagens para teste, respeitando as dimensões (150x150 até 600x600 pixels)</li>
        <li>Adicione <code>?v=<?php echo time(); ?></code> ao final das URLs de imagem para evitar o cache</li>
        <li>Verifique os logs de erro do PHP em <code>/xampp/logs/php_error_log</code></li>
    </ol>
</div>

<p><a href="timeline_edit.php" class="btn btn-primary">Voltar para Edição</a></p>

<?php
// Mostrar PHPInfo se solicitado
if (isset($_GET['phpinfo'])) {
    phpinfo();
}
?> 