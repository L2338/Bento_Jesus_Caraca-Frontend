<?php
/**
 * Gerenciamento de Usuários Administradores
 */

// Incluir configurações e funções
require_once __DIR__ . '/config/app-config.php';
require_once __DIR__ . '/core/functions.php';

// Verificar se o usuário está logado
require_login();

// Definir variáveis da página
$page_title = 'Gerenciador Usuários';

// Definir breadcrumbs
$breadcrumbs = [
    'Dashboard' => 'dashboard.php',
    'Usuários' => 'users.php'
];

// Obter conexão com o banco de dados
$conn = require_once __DIR__ . '/../ConfigBD.php';

// Processar ações
$successMessage = '';
$errorMessage = '';

// Processar exclusão de usuário
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    
    // Verificar se o usuário não está tentando excluir a si mesmo
    if ($userId == $_SESSION['user_id']) {
        $errorMessage = "Você não pode excluir seu próprio usuário.";
    } else {
        // Excluir o usuário
        $deleteQuery = "DELETE FROM Administradores WHERE id_admin = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            $successMessage = "Usuário excluído com sucesso!";
            
            // Adicionar notificação de usuário excluído
            if (function_exists('add_notification')) {
                $username = $_POST['username'] ?? 'Desconhecido';
                add_notification(
                    0, // para todos os usuários
                    'usuario',
                    'Usuário excluído',
                    "O usuário {$username} foi removido do sistema.",
                    ADMIN_URL . 'users.php'
                );
            }
        } else {
            $errorMessage = "Erro ao excluir usuário: " . $conn->error;
        }
    }
}

// Processar adição/edição de usuário
if (isset($_POST['save_user'])) {
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Validar dados
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Nome de usuário é obrigatório.";
    }
    
    if (empty($email)) {
        $errors[] = "E-mail é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail inválido.";
    }
    
    if ($userId == 0 && empty($password)) {
        $errors[] = "Senha é obrigatória para novos usuários.";
    }
    
    // Verificar se o usuário já existe
    $checkQuery = "SELECT id_admin FROM Administradores WHERE admin = ? AND id_admin != ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("si", $username, $userId);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    
    if ($stmtCheck->num_rows > 0) {
        $errors[] = "Nome de usuário já está em uso.";
    }
    
    // Verificar se o email já existe
    $checkEmailQuery = "SELECT id_admin FROM Administradores WHERE email = ? AND id_admin != ?";
    $stmtCheckEmail = $conn->prepare($checkEmailQuery);
    $stmtCheckEmail->bind_param("si", $email, $userId);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();
    
    if ($stmtCheckEmail->num_rows > 0) {
        $errors[] = "E-mail já está em uso.";
    }
    
    if (empty($errors)) {
        if ($userId > 0) {
            // Atualizar usuário existente
            if (!empty($password)) {
                // Hash da senha
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $updateQuery = "UPDATE Administradores SET admin = ?, email = ?, senha = ? WHERE id_admin = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("sssi", $username, $email, $hashedPassword, $userId);
            } else {
                // Sem alterar a senha
                $updateQuery = "UPDATE Administradores SET admin = ?, email = ? WHERE id_admin = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssi", $username, $email, $userId);
            }
            
            if ($stmt->execute()) {
                $successMessage = "Usuário atualizado com sucesso!";
                
                // Adicionar notificação de usuário atualizado
                if (function_exists('add_notification')) {
                    add_notification(
                        0, // para todos os usuários
                        'usuario',
                        'Usuário atualizado',
                        "O usuário {$username} foi atualizado.",
                        ADMIN_URL . 'users.php'
                    );
                }
            } else {
                $errorMessage = "Erro ao atualizar usuário: " . $conn->error;
            }
        } else {
            // Adicionar novo usuário
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $insertQuery = "INSERT INTO Administradores (admin, email, senha) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sss", $username, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $successMessage = "Novo usuário adicionado com sucesso!";
                
                // Adicionar notificação de novo usuário
                if (function_exists('add_notification')) {
                    add_notification(
                        0, // para todos os usuários
                        'usuario',
                        'Novo usuário adicionado',
                        "O usuário {$username} foi adicionado ao sistema.",
                        ADMIN_URL . 'users.php'
                    );
                }
            } else {
                $errorMessage = "Erro ao adicionar usuário: " . $conn->error;
            }
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}

// Obter lista de usuários
$query = "SELECT id_admin, admin, email, created_at FROM Administradores ORDER BY admin";
$result = $conn->query($query);
$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Incluir o cabeçalho
include_once 'templates/header.php';
?>

<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        margin-right: 10px;
    }
    
    .user-card {
        transition: all 0.3s ease;
        border-radius: 10px;
    }
    
    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .user-actions {
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .user-card:hover .user-actions {
        visibility: visible;
        opacity: 1;
    }
    
    .add-user-card {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .add-user-card:hover {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
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
        <h1 class="h3 mb-0 text-gray-800">Gerenciar Usuários</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Adicionar Usuário
        </button>
    </div>
    
    <!-- Cards de Usuários -->
    <div class="row">
        <?php foreach ($users as $user): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card user-card border-0 shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="user-avatar bg-primary">
                                <?php echo strtoupper(substr($user['admin'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($user['admin']); ?></div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Administrador</div>
                            </div>
                            <?php if ($user['id_admin'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-success ms-auto">Você</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-clock me-1"></i> Registrado em <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                            </p>
                        </div>
                        
                        <div class="mt-auto user-actions">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-outline-primary me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal" 
                                        data-user-id="<?php echo $user['id_admin']; ?>"
                                        data-username="<?php echo htmlspecialchars($user['admin']); ?>"
                                        data-email="<?php echo htmlspecialchars($user['email']); ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <?php if ($user['id_admin'] != $_SESSION['user_id']): ?>
                                <button class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteUserModal"
                                        data-user-id="<?php echo $user['id_admin']; ?>"
                                        data-username="<?php echo htmlspecialchars($user['admin']); ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Card para adicionar usuário -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card add-user-card h-100 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="rounded-circle bg-light p-3 mb-3">
                            <i class="bi bi-person-plus-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="text-center">Adicionar Novo Usuário</h5>
                        <p class="text-muted text-center small">Clique para adicionar um novo administrador ao sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Adicionar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Excluir Usuário -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong id="delete_username"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="delete_user" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de edição
    const editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');
            
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_password').value = '';
        });
    }
    
    // Configurar modal de exclusão
    const deleteUserModal = document.getElementById('deleteUserModal');
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('delete_username').textContent = username;
        });
    }
});
</script>

<?php
include_once 'templates/footer.php';
?> 