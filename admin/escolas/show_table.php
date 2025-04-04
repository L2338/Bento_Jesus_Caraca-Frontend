<?php
/**
 * Visualizador de tabela cursos
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

if (!$conn) {
    die("Erro de conexão com o banco de dados");
}

// Verificar se a tabela cursos existe
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'cursos'");
if (mysqli_num_rows($check_table) === 0) {
    die("A tabela 'cursos' não existe");
}

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Escolas e Informações' => ADMIN_URL . 'escolas/index.php',
    'Verificar Tabela Cursos' => '#'
]);
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Estrutura da tabela 'cursos'</h6>
    </div>
    <div class="card-body">
        <?php
        // Obter estrutura da tabela
        $result = mysqli_query($conn, "DESCRIBE cursos");
        if (!$result) {
            echo '<div class="alert alert-danger">Erro ao consultar a estrutura da tabela: ' . mysqli_error($conn) . '</div>';
        } else {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered">';
            echo '<thead><tr>
                <th>Campo</th>
                <th>Tipo</th>
                <th>Nulo</th>
                <th>Chave</th>
                <th>Padrão</th>
                <th>Extra</th>
            </tr></thead>';
            echo '<tbody>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['Field'] . '</td>';
                echo '<td>' . $row['Type'] . '</td>';
                echo '<td>' . $row['Null'] . '</td>';
                echo '<td>' . $row['Key'] . '</td>';
                echo '<td>' . $row['Default'] . '</td>';
                echo '<td>' . $row['Extra'] . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Verificação de colunas</h6>
    </div>
    <div class="card-body">
        <?php
        // Verificar se precisamos adicionar colunas
        $colunas_necessarias = [
            'imagem' => "ALTER TABLE cursos ADD COLUMN imagem VARCHAR(255) NOT NULL DEFAULT 'assets/img/courses/default.jpg' AFTER descricao",
            'link' => "ALTER TABLE cursos ADD COLUMN link VARCHAR(255) NOT NULL DEFAULT 'https://escolasbjc.pt' AFTER imagem",
            'duracao' => "ALTER TABLE cursos ADD COLUMN duracao VARCHAR(50) NOT NULL DEFAULT '3 Anos' AFTER avaliacao"
        ];

        $alerts = '';
        foreach ($colunas_necessarias as $coluna => $sql) {
            $check_coluna = mysqli_query($conn, "SHOW COLUMNS FROM cursos LIKE '$coluna'");
            
            if (mysqli_num_rows($check_coluna) === 0) {
                $alerts .= '<div class="alert alert-warning">A coluna \'' . $coluna . '\' não existe. Tentando adicionar...</div>';
                
                if (mysqli_query($conn, $sql)) {
                    $alerts .= '<div class="alert alert-success">✅ Coluna \'' . $coluna . '\' adicionada com sucesso.</div>';
                } else {
                    $alerts .= '<div class="alert alert-danger">❌ Erro ao adicionar a coluna \'' . $coluna . '\': ' . mysqli_error($conn) . '</div>';
                }
            } else {
                $alerts .= '<div class="alert alert-success">✅ A coluna \'' . $coluna . '\' já existe.</div>';
            }
        }
        
        echo $alerts;
        ?>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Dados da tabela 'cursos'</h6>
    </div>
    <div class="card-body">
        <?php
        // Mostrar dados da tabela
        $data_result = mysqli_query($conn, "SELECT * FROM cursos");

        if (!$data_result) {
            echo '<div class="alert alert-danger">Erro ao consultar dados: ' . mysqli_error($conn) . '</div>';
        } else if (mysqli_num_rows($data_result) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered">';
            
            // Cabeçalhos da tabela
            $fields = mysqli_fetch_fields($data_result);
            echo '<thead><tr>';
            foreach ($fields as $field) {
                echo '<th>' . $field->name . '</th>';
            }
            echo '</tr></thead>';
            
            // Dados
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($data_result)) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">Não há dados na tabela \'cursos\'</div>';
        }
        ?>
        
        <div class="mt-3">
            <a href="<?php echo ADMIN_URL; ?>escolas/index.php?tab=cursos" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i> Voltar para Gestão de Cursos
            </a>
        </div>
    </div>
</div>

<?php
// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?> 