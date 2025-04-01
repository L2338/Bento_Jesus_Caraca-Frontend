<?php
session_start();

// Verificar se já está logado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit();
}

// Processar o login
if (!empty($_POST)) {
    try {
        $conn = require 'ConfigBD.php';

        // Receber e sanitizar os dados
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = sha1(mysqli_real_escape_string($conn, $_POST['password']));

        // Consulta segura usando prepared statement - usando tabela Administradores
        $query = "SELECT * FROM Administradores WHERE admin = ? AND senha = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                
                // Log de acesso bem-sucedido
                error_log("Login bem-sucedido para usuário: " . $username);
                
                // Redirecionar para o dashboard administrativo
                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error_message = "Nome de utilizador ou senha incorretos";
                error_log("Tentativa de login falhou para usuário: " . $username);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            throw new Exception("Erro ao preparar a consulta");
        }
        
        mysqli_close($conn);
    } catch (Exception $e) {
        error_log("Erro no login: " . $e->getMessage());
        $error_message = "Erro ao processar o login. Por favor, tente novamente mais tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bento de Jesus Caraça</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .row {
            width: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        .col-md-12 {
            display: flex;
            justify-content: center;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
            
        .login-header h2 {
            color: #333;
            font-weight: 600;            
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn {
            width: 100%;
            padding: 10px;
            font-weight: 600;
            background-color:#ac062a;
            color:white;
        }
        .btn:hover {
            background-color: #8c0523;
            color: white;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login-container">
                    <div class="login-header">
                        <h2>Login</h2>
                        <p class="text-muted">Entre na sua conta</p>
                    </div>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de utilizador</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>