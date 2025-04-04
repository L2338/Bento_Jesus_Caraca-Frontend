<?php
/**
 * Script para inserção de cursos padrão
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verifica se o usuário está logado
require_login();

// Incluir conexão com banco de dados
$conn = require '../../ConfigBD.php';

// Carregar o cabeçalho
require_once __DIR__ . '/../templates/header.php';

// Definir breadcrumbs
echo generate_breadcrumbs([
    'Dashboard' => ADMIN_URL . 'dashboard.php',
    'Escolas e Informações' => ADMIN_URL . 'escolas/index.php',
    'Inserir Cursos Padrão' => '#'
]);

// Verificar se a ação foi confirmada
$confirmed = isset($_POST['confirm']) && $_POST['confirm'] === 'yes';
$messages = [];

if ($confirmed) {
    // Verificar se a tabela cursos existe
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'cursos'");
    if (mysqli_num_rows($check_table) === 0) {
        // Criar a tabela cursos se não existir
        $create_table = "CREATE TABLE cursos (
            id INT(11) NOT NULL AUTO_INCREMENT,
            titulo VARCHAR(255) NOT NULL,
            categoria VARCHAR(255) NOT NULL,
            descricao TEXT NOT NULL,
            imagem VARCHAR(255) NOT NULL DEFAULT 'assets/img/courses/default.jpg',
            link VARCHAR(255) NOT NULL DEFAULT 'https://escolasbjc.pt',
            avaliacao DECIMAL(3,1) NOT NULL DEFAULT 4.5,
            duracao VARCHAR(50) NOT NULL DEFAULT '3 Anos',
            ordem INT(11) NOT NULL DEFAULT 1,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        if (!mysqli_query($conn, $create_table)) {
            $messages[] = [
                'type' => 'danger',
                'text' => "Erro ao criar a tabela cursos: " . mysqli_error($conn)
            ];
        } else {
            $messages[] = [
                'type' => 'success',
                'text' => "Tabela 'cursos' criada com sucesso."
            ];
        }
    } else {
        // Verificar se precisamos adicionar colunas
        $colunas_necessarias = [
            'imagem' => "ALTER TABLE cursos ADD COLUMN imagem VARCHAR(255) NOT NULL DEFAULT 'assets/img/courses/default.jpg' AFTER descricao",
            'link' => "ALTER TABLE cursos ADD COLUMN link VARCHAR(255) NOT NULL DEFAULT 'https://escolasbjc.pt' AFTER imagem",
            'duracao' => "ALTER TABLE cursos ADD COLUMN duracao VARCHAR(50) NOT NULL DEFAULT '3 Anos' AFTER avaliacao"
        ];

        foreach ($colunas_necessarias as $coluna => $sql) {
            $check_coluna = mysqli_query($conn, "SHOW COLUMNS FROM cursos LIKE '$coluna'");
            
            if (mysqli_num_rows($check_coluna) === 0) {
                if (!mysqli_query($conn, $sql)) {
                    $messages[] = [
                        'type' => 'danger',
                        'text' => "Erro ao adicionar a coluna '$coluna': " . mysqli_error($conn)
                    ];
                } else {
                    $messages[] = [
                        'type' => 'success',
                        'text' => "Coluna '$coluna' adicionada com sucesso."
                    ];
                }
            }
        }
    }

    // Verificar se já existem cursos
    $check_courses = mysqli_query($conn, "SELECT COUNT(*) as count FROM cursos");
    $count = mysqli_fetch_assoc($check_courses)['count'];

    if ($count > 0) {
        $messages[] = [
            'type' => 'warning',
            'text' => "Já existem $count cursos na tabela. Nenhum novo curso será inserido."
        ];
    } else {
        // Array com cursos padrão
        $cursos = [
            [
                'titulo' => 'Gestão e Programação de Sistemas Informáticos',
                'categoria' => 'Tecnologia',
                'descricao' => 'Torna-te especialista na criação e gestão de sistemas informáticos. Aprende sobre desenvolvimento de software, administração de redes e segurança.',
                'imagem' => 'assets/img/courses/course-1.jpg',
                'link' => 'https://escolasbjc.pt/cursos/gpsi',
                'avaliacao' => 4.7,
                'duracao' => '3 Anos',
                'ordem' => 1,
                'ativo' => 1
            ],
            [
                'titulo' => 'Gestão de Comunicação, Marketing e Publicidade',
                'categoria' => 'Marketing',
                'descricao' => 'Aprende a criar campanhas eficazes, gerenciar redes sociais e otimizar a presença digital de empresas com estratégias modernas de comunicação.',
                'imagem' => 'assets/img/courses/course-2.jpg',
                'link' => 'https://escolasbjc.pt/cursos/marketing',
                'avaliacao' => 4.5,
                'duracao' => '3 Anos',
                'ordem' => 2,
                'ativo' => 1
            ],
            [
                'titulo' => 'Técnica de Gestão de Equipamento Informático',
                'categoria' => 'Tecnologia',
                'descricao' => 'Aprende a instalar, reparar e configurar equipamentos informáticos e redes. O curso oferece uma formação completa em hardware e infraestrutura.',
                'imagem' => 'assets/img/courses/course-3.jpg',
                'link' => 'https://escolasbjc.pt/cursos/tgei',
                'avaliacao' => 4.5,
                'duracao' => '3 Anos',
                'ordem' => 3,
                'ativo' => 1
            ]
        ];

        // Inserir cursos
        $success = true;
        $inserted_count = 0;
        
        foreach ($cursos as $curso) {
            $sql = "INSERT INTO cursos (titulo, categoria, descricao, imagem, link, avaliacao, duracao, ordem, ativo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => "Erro ao preparar a declaração: " . mysqli_error($conn)
                ];
                $success = false;
                continue;
            }
            
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssdsis", 
                $curso['titulo'], 
                $curso['categoria'], 
                $curso['descricao'], 
                $curso['imagem'], 
                $curso['link'], 
                $curso['avaliacao'], 
                $curso['duracao'], 
                $curso['ordem'], 
                $curso['ativo']
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => "Erro ao inserir o curso '" . $curso['titulo'] . "': " . mysqli_stmt_error($stmt)
                ];
                $success = false;
            } else {
                $inserted_count++;
            }
            
            mysqli_stmt_close($stmt);
        }
        
        if ($success) {
            $messages[] = [
                'type' => 'success',
                'text' => "$inserted_count cursos inseridos com sucesso."
            ];
        }
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Inserir Cursos Padrão</h6>
    </div>
    <div class="card-body">
        <?php if ($messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo $message['type']; ?>" role="alert">
                    <?php echo $message['text']; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="mt-3">
                <a href="<?php echo ADMIN_URL; ?>escolas/index.php?tab=cursos" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para Gestão de Cursos
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> 
                Este script irá criar a tabela 'cursos' se ela não existir e inserir 3 cursos padrão.
                Nenhuma ação será tomada se já existirem cursos na tabela.
            </div>

            <form method="post" action="">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="confirm" value="yes" id="confirm">
                    <label class="form-check-label" for="confirm">
                        Sim, eu quero inserir os cursos padrão
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-download me-1"></i> Executar Script
                </button>
                <a href="<?php echo ADMIN_URL; ?>escolas/index.php?tab=cursos" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php
// Carregar o rodapé
require_once __DIR__ . '/../templates/footer.php';
?> 