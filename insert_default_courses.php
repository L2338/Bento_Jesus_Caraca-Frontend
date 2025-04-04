<?php
// Incluir a configuração do banco de dados
$conn = require 'ConfigBD.php';

if (!$conn) {
    die("Erro de conexão com o banco de dados");
}

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
        die("Erro ao criar a tabela cursos: " . mysqli_error($conn));
    }
    
    echo "Tabela 'cursos' criada com sucesso.<br>";
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
                echo "Erro ao adicionar a coluna '$coluna': " . mysqli_error($conn) . "<br>";
            } else {
                echo "Coluna '$coluna' adicionada com sucesso.<br>";
            }
        }
    }
}

// Verificar se já existem cursos
$check_courses = mysqli_query($conn, "SELECT COUNT(*) as count FROM cursos");
$count = mysqli_fetch_assoc($check_courses)['count'];

if ($count > 0) {
    echo "Já existem $count cursos na tabela. Nenhum novo curso será inserido.<br>";
    echo "<a href='admin/escolas/index.php?tab=cursos'>Ir para gestão de cursos</a>";
    exit;
}

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
foreach ($cursos as $curso) {
    $sql = "INSERT INTO cursos (titulo, categoria, descricao, imagem, link, avaliacao, duracao, ordem, ativo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "Erro ao preparar a declaração: " . mysqli_error($conn) . "<br>";
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
        echo "Erro ao inserir o curso '" . $curso['titulo'] . "': " . mysqli_stmt_error($stmt) . "<br>";
        $success = false;
    }
    
    mysqli_stmt_close($stmt);
}

if ($success) {
    echo "Cursos inseridos com sucesso.<br>";
} else {
    echo "Houve erros ao inserir alguns cursos.<br>";
}

echo "<a href='admin/escolas/index.php?tab=cursos'>Ir para gestão de cursos</a>";

mysqli_close($conn);
?> 