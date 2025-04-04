<?php
/**
 * Handler AJAX para notificações
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado
require_login();

// Obter conexão com o banco de dados
$conn = require_once __DIR__ . '/../../ConfigBD.php';

// Inicializar resposta
$response = [
    'success' => false,
    'message' => 'Ação inválida'
];

// Verificar ação
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'mark_read':
            if (isset($_POST['notification_id'])) {
                $notification_id = (int)$_POST['notification_id'];
                if (mark_notification_read($notification_id)) {
                    $response['success'] = true;
                    $response['message'] = 'Notificação marcada como lida';
                    $response['count'] = get_notification_count();
                } else {
                    $response['message'] = 'Erro ao marcar notificação como lida';
                }
            }
            break;
            
        case 'mark_all_read':
            if (mark_all_notifications_read()) {
                $response['success'] = true;
                $response['message'] = 'Todas as notificações foram marcadas como lidas';
                $response['count'] = 0;
            } else {
                $response['message'] = 'Erro ao marcar notificações como lidas';
            }
            break;
            
        case 'get_notifications':
            $notifications = get_unread_notifications(10);
            $response['success'] = true;
            $response['notifications'] = $notifications;
            $response['count'] = count($notifications);
            $response['message'] = 'Notificações carregadas';
            break;
    }
}

// Enviar resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
exit; 