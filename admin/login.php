<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                    // Iniciar sessão
                    session_start();
                    
                    // Verificar se há erros de login
                    if (isset($_SESSION['login_error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    
                    // Verificar se o formulário foi enviado
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        
                        // Aqui você adicionaria sua lógica de verificação de login
                        // Exemplo simples:
                        // Substitua esta verificação pela sua lógica de banco de dados
                        if ($username == "admin" && $password == "senha123") {
                            // Login bem-sucedido
                            $_SESSION['logged_in'] = true;
                            $_SESSION['username'] = $username;
                            
                            // Redirecionar para a página principal
                            header("Location: index.php");
                            exit();
                        } else {
                            // Login falhou
                            $_SESSION['login_error'] = "Nome de utilizador ou senha incorretos";
                            header("Location: login.php");
                            exit();
                        }
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
                        <button type="submit" class="btn ">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>