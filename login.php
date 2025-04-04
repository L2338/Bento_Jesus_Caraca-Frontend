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
    <title>Login Administrativo | Bento de Jesus Caraça</title>
    <meta name="description" content="Acesso ao painel administrativo do site Bento de Jesus Caraça">
    <meta name="keywords" content="login, painel administrativo, Bento de Jesus Caraça">
    
    <!-- Favicons -->
    <link rel="icon" href="assets/img/favicon.png" type="image/png">
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <meta name="theme-color" content="#ac062a">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    
    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('assets/img/index/hero-bn.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            position: relative;
        }

        /* Preloader */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #preloader .spinner-border {
            width: 3rem;
            height: 3rem;
            color: #ac062a !important;
        }

        /* Login container */
        .login-box {
            max-width: 450px;
            width: 90%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.5s ease;
        }

        .login-box.show {
            opacity: 1;
            transform: translateY(0);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            height: 70px;
            margin-bottom: 20px;
            -webkit-user-drag: none;
            user-drag: none;
            -webkit-user-select: none;
            user-select: none;
            pointer-events: none;
        }

        .login-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .login-header p.subtitle {
            color: #666;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .login-header p.admin-note {
            font-size: 13px;
            color: #ac062a;
            background-color: rgba(172, 6, 42, 0.1);
            padding: 8px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            height: 50px;
            padding-left: 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #ac062a;
            box-shadow: 0 0 0 0.2rem rgba(172, 6, 42, 0.15);
            background-color: #fff;
        }

        .form-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 18px;
            transition: all 0.3s;
        }

        .form-control:focus + .form-icon {
            color: #ac062a;
        }

        .btn-login {
            height: 50px;
            background-color: #ac062a;
            border: none;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #8a0522;
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(172, 6, 42, 0.3);
        }

        .btn-back {
            background-color: transparent;
            border: 1px solid #ddd;
            color: #666;
            font-weight: 500;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #f5f5f5;
            color: #333;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-back i {
            margin-right: 8px;
        }

        .alert {
            border-radius: 8px;
            padding: 12px 15px;
        }

        .site-footer {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        /* Animações suaves */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">A carregar...</span>
        </div>
    </div>
    
    <div class="login-box" data-aos="fade-up">
        <div class="login-header">
            <img src="assets/img/favicon.png" alt="Bento de Jesus Caraça">
            <h2>Área Administrativa</h2>
            <p class="subtitle">Bento de Jesus Caraça</p>
            <p class="admin-note"><i class="bi bi-shield-lock"></i> Acesso restrito a administradores autorizados</p>
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
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" placeholder="Nome de utilizador" required>
                <i class="bi bi-person form-icon"></i>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                <i class="bi bi-lock form-icon"></i>
            </div>
            <button type="submit" class="btn btn-login">Entrar</button>
        </form>
        
        <button class="btn btn-login btn-back" onclick="window.location.href='index.php'">
            <i class="bi bi-arrow-left"></i> Voltar ao site
        </button>
    </div>
    
    <div class="site-footer">
        &copy; <?php echo date('Y'); ?> Bento de Jesus Caraça - Todos os direitos reservados
    </div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar AOS
            AOS.init({
                duration: 600,
                easing: 'ease-in-out',
                once: true,
                mirror: false
            });
            
            // Função para mostrar a tela de login após o preloader
            function showLoginScreen() {
                const loginBox = document.querySelector('.login-box');
                loginBox.classList.add('show');
            }
            
            // Remove o preloader após o carregamento completo
            window.addEventListener('load', function() {
                // Simulação de carregamento para dashboard
                setTimeout(function() {
                    const preloader = document.querySelector('#preloader');
                    if (preloader) {
                        preloader.classList.add('fade-out');
                        setTimeout(() => {
                            preloader.remove();
                            showLoginScreen();
                        }, 300);
                    }
                }, 800); // Tempo de exibição do preloader
            });
        });
    </script>
</body>
</html>