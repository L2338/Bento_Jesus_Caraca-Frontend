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
?> 