<?php
session_start();

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['mensagem'] = 'ID de obra inválido';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: dashboard.php');
    exit();
}

// Obter ID da obra
$id = intval($_GET['id']);

// Incluir conexão com o banco de dados
$conn = require '../ConfigBD.php';

// Consultar obra por ID
$query = "SELECT * FROM obras WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificar se a obra foi encontrada
if (mysqli_num_rows($result) === 0) {
    $_SESSION['mensagem'] = 'Obra não encontrada';
    $_SESSION['tipo_mensagem'] = 'danger';
    header('Location: dashboard.php');
    exit();
}

// Obter dados da obra
$obra = mysqli_fetch_assoc($result);

// Fechar conexão
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Obra - <?php echo htmlspecialchars($obra['titulo']); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ac062a;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .obra-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .obra-titulo {
            color: var(--primary-color);
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .obra-info {
            margin-bottom: 20px;
        }
        
        .obra-info .label {
            font-weight: bold;
            color: #555;
        }
        
        .obra-capa {
            max-width: 300px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-voltar {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            margin-right: 10px;
        }
        
        .btn-voltar:hover {
            background-color: #8c0523;
            border-color: #8c0523;
            color: white;
        }
        
        .pdf-preview {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Visualizar Obra</li>
                    </ol>
                </nav>
                
                <div class="obra-container">
                    <h1 class="obra-titulo"><?php echo htmlspecialchars($obra['titulo']); ?></h1>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <?php if (!empty($obra['imagem_capa'])): ?>
                                <img src="../assets/img/obras/<?php echo htmlspecialchars($obra['imagem_capa']); ?>" alt="Capa da obra" class="img-fluid obra-capa">
                            <?php else: ?>
                                <div class="text-center p-5 bg-light mb-4">
                                    <i class="fas fa-book fa-5x text-muted"></i>
                                    <p class="mt-3">Sem imagem de capa</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="obra-info">
                                <p><span class="label">ID:</span> <?php echo $obra['id']; ?></p>
                                <p><span class="label">Autor:</span> <?php echo htmlspecialchars($obra['autor'] ?? 'Não informado'); ?></p>
                                <p><span class="label">Ano de Publicação:</span> <?php echo isset($obra['ano']) ? htmlspecialchars($obra['ano']) : 'Não informado'; ?></p>
                                <p><span class="label">Categoria:</span> <?php echo isset($obra['categoria']) ? htmlspecialchars($obra['categoria']) : 'Não informado'; ?></p>
                                <p><span class="label">Data de Cadastro:</span> <?php echo isset($obra['data_cadastro']) ? date('d/m/Y H:i', strtotime($obra['data_cadastro'])) : 'Não informado'; ?></p>
                                
                                <div class="mt-4">
                                    <h5>Descrição</h5>
                                    <p><?php echo nl2br(htmlspecialchars($obra['descricao'] ?? 'Sem descrição')); ?></p>
                                </div>
                                
                                <?php if (!empty($obra['pdf'])): ?>
                                    <div class="mt-4">
                                        <h5>Arquivo PDF</h5>
                                        <a href="../assets/pdf/Obras/<?php echo htmlspecialchars($obra['pdf']); ?>" target="_blank" class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i> Visualizar PDF
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <a href="dashboard.php" class="btn btn-voltar">
                            <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
                        </a>
                        <button class="btn btn-warning edit-obra" data-id="<?php echo $obra['id']; ?>">
                            <i class="fas fa-edit"></i> Editar Obra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Botão de editar
            $('.edit-obra').on('click', function() {
                const id = $(this).data('id');
                window.location.href = `dashboard.php?editar=${id}`;
            });
        });
    </script>
</body>
</html> 