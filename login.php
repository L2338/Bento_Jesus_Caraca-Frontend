<?php
session_start();

// Verificar se já está logado
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

// Processar o login
if (!empty($_POST)) {
    try {
        // Receber e sanitizar os dados
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        // Verificação temporária (até o banco de dados estar configurado)
        if ($username === 'admin' && $password === 'senha123') {
            // Definir variáveis de sessão necessárias para o dashboard
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            
            // Log de acesso bem-sucedido
            error_log("Login bem-sucedido para usuário: " . $username);
            
            // Redirecionar para o dashboard administrativo
            header("Location: admin/dashboard.php");
            exit();
        } else {
            // Tentar banco de dados se a verificação temporária falhar
            $conn = require 'ConfigBD.php';
            
            // Sanitizar dados para consulta
            $username = $conn->real_escape_string($username);
            $password = sha1($conn->real_escape_string($password));
            
            // Consulta segura
            $query = "SELECT * FROM Administradores WHERE admin = '$username' AND senha = '$password'";
            $login = $conn->query($query);

            if (mysqli_num_rows($login) == 1) {
                // obter os dados do usuário
                $user = mysqli_fetch_assoc($login);
                
                // Definir variáveis de sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                
                // Log de acesso bem-sucedido
                error_log("Login bem-sucedido para usuário: " . $username);
                
                // Redirecionar para o dashboard administrativo
                header("Location: admin/dashboard.php");
                exit();
            } else {
                // Login falhou
                $_SESSION['login_error'] = "Nome de utilizador ou senha incorretos";
                error_log("Tentativa de login falhou para usuário: " . $username);
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        error_log("Erro no login: " . $e->getMessage());
        $_SESSION['login_error'] = "Erro ao processar o login. Por favor, tente novamente mais tarde.";
    }
}

// Verificar mensagem de logout
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $_SESSION['login_success'] = "Logout realizado com sucesso!";
}

// Verificar erro de login
if (isset($_GET['error']) && $_GET['error'] === 'login_required') {
    $_SESSION['login_error'] = "Por favor, faça login para acessar o painel administrativo.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
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
                    
                    <?php
                    // Verificar se há erros de login
                    if (isset($_SESSION['login_error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    
                    // Verificar se há mensagens de sucesso
                    if (isset($_SESSION['login_success'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['login_success'] . '</div>';
                        unset($_SESSION['login_success']);
                    }
                    ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>