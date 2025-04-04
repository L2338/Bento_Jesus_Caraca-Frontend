<?php
/**
 * Funções utilitárias do sistema administrativo
 */

/**
 * Verifica se o usuário está logado, caso contrário redireciona para login
 * 
 * @return void
 */
function require_login() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Redirecionar para página de login na raiz
        header('Location: ' . SITE_URL . 'login.php?error=login_required');
        exit;
    }
}

/**
 * Gera um token CSRF para formulários
 * 
 * @return string Token CSRF
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica um token CSRF
 * 
 * @param string $token Token recebido do formulário
 * @return bool Verdadeiro se o token for válido
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Sanitiza uma string para saída segura
 * 
 * @param string $str String para sanitizar
 * @return string String sanitizada
 */
function sanitize_output($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Gera breadcrumbs para navegação
 * 
 * @param array $links Array associativo com rótulos e links
 * @return string HTML dos breadcrumbs
 */
function generate_breadcrumbs($links) {
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb bg-light p-2 mb-4">';
    
    $i = 0;
    $count = count($links);
    
    foreach ($links as $label => $url) {
        $i++;
        if ($i == $count) {
            // Último item (atual)
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . sanitize_output($label) . '</li>';
        } else {
            // Links intermediários
            $html .= '<li class="breadcrumb-item"><a href="' . $url . '">' . sanitize_output($label) . '</a></li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Função temporária para autenticação (substituir por validação real com banco de dados)
 * 
 * @param string $username Nome de usuário
 * @param string $password Senha
 * @return bool Se a autenticação foi bem-sucedida
 */
function authenticate_user($username, $password) {
    // Usuário e senha temporários para testes
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['user_name'] = 'Administrador';
        $_SESSION['user_role'] = 'admin';
        $_SESSION['last_login'] = time();
        return true;
    }
    return false;
}

/**
 * Gera uma mensagem flash para exibir na próxima página
 * 
 * @param string $type Tipo de mensagem (success, error, warning, info)
 * @param string $message Texto da mensagem
 * @return void
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
        'created_at' => time()
    ];
}

/**
 * Obtém e limpa a mensagem flash se existir
 * 
 * @return array|null Mensagem flash ou null se não existir
 */
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        
        // Verificar se a mensagem não expirou (10 minutos)
        if (time() - $message['created_at'] > 600) {
            return null;
        }
        
        return $message;
    }
    return null;
}

/**
 * Exibe mensagem flash se existir
 * 
 * @return string HTML da mensagem ou string vazia
 */
function display_flash_message() {
    $flash = get_flash_message();
    if (!$flash) {
        return '';
    }
    
    $alert_class = 'alert-info';
    switch ($flash['type']) {
        case 'success':
            $alert_class = 'alert-success';
            break;
        case 'error':
            $alert_class = 'alert-danger';
            break;
        case 'warning':
            $alert_class = 'alert-warning';
            break;
    }
    
    return '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">
        ' . sanitize_output($flash['message']) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>';
}

/**
 * Faz o logout do usuário
 * 
 * @return void
 */
function logout_user() {
    // Limpar variáveis de sessão
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role']);
    unset($_SESSION['last_login']);
    
    // Opcional: destruir a sessão completamente
    // session_destroy();
}

/**
 * Centraliza conteúdo HTML de forma simplificada e eficiente
 * 
 * @param string $content O conteúdo HTML a ser centralizado
 * @return string O conteúdo HTML com alinhamento centralizado
 */
function centralizar_conteudo($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Verificar se o conteúdo já tem um wrapper
    if (strpos($content, '<div class="text-center"') === false) {
        // Envolver todo o conteúdo em um div centralizado
        return '<div class="text-center" style="text-align: center;">' . $content . '</div>';
    }
    
    return $content;
}

/**
 * Obtém notificações não lidas do usuário
 * 
 * @param int $limit Limite de notificações a serem retornadas
 * @return array Notificações não lidas
 */
function get_unread_notifications($limit = 5) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = (int)$_SESSION['user_id'];
    
    // Verificar se a tabela existe
    $tableCheck = $conn->query("SHOW TABLES LIKE 'Notificacoes'");
    if ($tableCheck->num_rows === 0) {
        // Tabela não existe, criar
        $conn->query("CREATE TABLE IF NOT EXISTS `Notificacoes` (
            `id_notificacao` int(11) NOT NULL AUTO_INCREMENT,
            `id_admin` int(11) NOT NULL,
            `tipo` varchar(50) NOT NULL,
            `titulo` varchar(100) NOT NULL,
            `mensagem` text NOT NULL,
            `link` varchar(255) DEFAULT NULL,
            `lida` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id_notificacao`),
            KEY `id_admin` (`id_admin`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
    
    // Buscar notificações não lidas
    $query = "SELECT * FROM Notificacoes 
              WHERE (id_admin = ? OR id_admin = 0) 
              AND lida = 0 
              ORDER BY created_at DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    return $notifications;
}

/**
 * Retorna o número de notificações não lidas
 * 
 * @return int Número de notificações não lidas
 */
function get_notification_count() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    
    // Verificar se a tabela existe
    $tableCheck = $conn->query("SHOW TABLES LIKE 'Notificacoes'");
    if ($tableCheck->num_rows === 0) {
        return 0;
    }
    
    // Contar notificações não lidas
    $query = "SELECT COUNT(*) AS total FROM Notificacoes 
              WHERE (id_admin = ? OR id_admin = 0) 
              AND lida = 0";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return (int)$row['total'];
}

/**
 * Marca uma notificação como lida
 * 
 * @param int $notification_id ID da notificação
 * @return bool Se a operação foi bem-sucedida
 */
function mark_notification_read($notification_id) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $notification_id = (int)$notification_id;
    
    $query = "UPDATE Notificacoes SET lida = 1 
              WHERE id_notificacao = ? 
              AND (id_admin = ? OR id_admin = 0)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $notification_id, $user_id);
    
    return $stmt->execute();
}

/**
 * Marca todas as notificações do usuário como lidas
 * 
 * @return bool Se a operação foi bem-sucedida
 */
function mark_all_notifications_read() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    
    $query = "UPDATE Notificacoes SET lida = 1 
              WHERE (id_admin = ? OR id_admin = 0) 
              AND lida = 0";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    return $stmt->execute();
}

/**
 * Adiciona uma nova notificação
 * 
 * @param int $user_id ID do usuário (0 para todos os usuários)
 * @param string $tipo Tipo da notificação (info, success, warning, danger)
 * @param string $titulo Título da notificação
 * @param string $mensagem Conteúdo da notificação
 * @param string $link Link opcional para mais detalhes
 * @return bool Se a operação foi bem-sucedida
 */
function add_notification($user_id, $tipo, $titulo, $mensagem, $link = null) {
    global $conn;
    
    $user_id = (int)$user_id;
    
    $query = "INSERT INTO Notificacoes (id_admin, tipo, titulo, mensagem, link) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $tipo, $titulo, $mensagem, $link);
    
    return $stmt->execute();
}

/**
 * Obtém ícone correspondente ao tipo de notificação
 * 
 * @param string $tipo Tipo da notificação
 * @return string Classe de ícone Bootstrap
 */
function get_notification_icon($tipo) {
    switch ($tipo) {
        case 'success':
            return 'bi-check-circle-fill';
        case 'warning':
            return 'bi-exclamation-triangle-fill';
        case 'danger':
            return 'bi-x-circle-fill';
        case 'obra':
            return 'bi-book-fill';
        case 'usuario':
            return 'bi-person-fill';
        case 'sistema':
            return 'bi-gear-fill';
        default:
            return 'bi-bell-fill';
    }
}

/**
 * Obtém classe de cor correspondente ao tipo de notificação
 * 
 * @param string $tipo Tipo da notificação
 * @return string Classe de cor Bootstrap
 */
function get_notification_color($tipo) {
    switch ($tipo) {
        case 'success':
            return 'text-success';
        case 'warning':
            return 'text-warning';
        case 'danger':
            return 'text-danger';
        case 'obra':
            return 'text-primary';
        case 'usuario':
            return 'text-info';
        case 'sistema':
            return 'text-secondary';
        default:
            return 'text-primary';
    }
}
?> 