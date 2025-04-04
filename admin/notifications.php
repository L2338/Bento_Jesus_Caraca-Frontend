<?php
/**
 * Página de gerenciamento de notificações
 */

// Incluir configurações e funções
require_once __DIR__ . '/config/app-config.php';
require_once __DIR__ . '/core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Notificações';

// Definir breadcrumbs
$breadcrumbs = [
    ['url' => 'dashboard.php', 'titulo' => 'Dashboard'],
    ['url' => 'notifications.php', 'titulo' => 'Notificações']
];

// Obter conexão com o banco de dados
$conn = require_once __DIR__ . '/../ConfigBD.php';

// Processar ações
$successMessage = '';
$errorMessage = '';

// Marcar todas como lidas se solicitado
if (isset($_GET['mark_all_read'])) {
    if (mark_all_notifications_read()) {
        $successMessage = "Todas as notificações foram marcadas como lidas.";
    } else {
        $errorMessage = "Erro ao marcar notificações como lidas.";
    }
}

// Marcar uma notificação específica como lida
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $notification_id = (int)$_GET['id'];
    if (mark_notification_read($notification_id)) {
        $successMessage = "Notificação marcada como lida.";
    } else {
        $errorMessage = "Erro ao marcar notificação como lida.";
    }
}

// Obter todas as notificações (lidas e não lidas)
$user_id = (int)$_SESSION['user_id'];
$query = "SELECT * FROM Notificacoes 
          WHERE (id_admin = ? OR id_admin = 0) 
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Incluir o cabeçalho
include_once 'templates/header.php';
?>

<style>
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid #e9ecef;
    }
    
    .notification-item:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .notification-item.unread {
        border-left-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }
    
    .notification-item.read {
        opacity: 0.7;
    }
    
    .notification-empty {
        padding: 50px 0;
        text-align: center;
        color: #6c757d;
    }
    
    .notification-empty i {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>

<div class="container-fluid">
    <!-- Alertas -->
    <?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>
    
    <?php if ($errorMessage): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $errorMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php endif; ?>

    <!-- Título da Página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Notificações</h1>
        <a href="?mark_all_read=1" class="btn btn-sm btn-primary">
            <i class="bi bi-check-all me-1"></i> Marcar todas como lidas
        </a>
    </div>
    
    <!-- Lista de Notificações -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Todas as Notificações</h6>
        </div>
        <div class="card-body p-0">
            <?php if (count($notifications) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item notification-item <?php echo $notification['lida'] ? 'read' : 'unread'; ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="notification-icon bg-<?php echo $notification['tipo']; ?> me-3">
                                        <i class="bi <?php echo get_notification_icon($notification['tipo']); ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($notification['titulo']); ?></h6>
                                        <p class="mb-1"><?php echo htmlspecialchars($notification['mensagem']); ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($notification['link']): ?>
                                        <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!$notification['lida']): ?>
                                        <a href="?mark_read=1&id=<?php echo $notification['id_notificacao']; ?>" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="notification-empty">
                    <i class="bi bi-bell-slash"></i>
                    <h5>Nenhuma notificação</h5>
                    <p>Você não tem nenhuma notificação para exibir.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include_once 'templates/footer.php';
?> 