/**
 * Gerenciamento de notificações
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const markAllReadBtn = document.querySelector('.mark-all-read');
    const notificationItems = document.querySelectorAll('.notification-item');
    const notificationCount = document.querySelector('.notification-count');
    
    // Função para atualizar contador de notificações
    function updateNotificationCount(count) {
        if (notificationCount) {
            if (count > 0) {
                notificationCount.textContent = count;
                notificationCount.style.display = '';
            } else {
                notificationCount.style.display = 'none';
            }
        }
    }
    
    // Marcar notificação individual como lida
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const notificationId = this.getAttribute('data-id');
            const link = this.getAttribute('href');
            
            // Se não for um link real, prevenir navegação
            if (link === 'javascript:void(0)') {
                e.preventDefault();
            }
            
            // Marcar como lida via AJAX
            const formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('notification_id', notificationId);
            
            fetch(`${ADMIN_URL}ajax/notifications.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationCount(data.count);
                }
            })
            .catch(error => console.error('Erro ao marcar notificação como lida:', error));
        });
    });
    
    // Marcar todas as notificações como lidas
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'mark_all_read');
            
            fetch(`${ADMIN_URL}ajax/notifications.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar UI
                    updateNotificationCount(0);
                    
                    // Limpar notificações no dropdown
                    const dropdown = document.querySelector('.notification-dropdown');
                    if (dropdown) {
                        const items = dropdown.querySelectorAll('.notification-item');
                        if (items.length > 0) {
                            // Remover itens
                            items.forEach(item => item.closest('li').remove());
                            
                            // Adicionar mensagem de nenhuma notificação
                            const noNotificationsItem = document.createElement('li');
                            noNotificationsItem.innerHTML = '<a class="dropdown-item text-center" href="#"><i class="bi bi-check-circle me-2"></i> Nenhuma notificação</a>';
                            
                            const divider = dropdown.querySelector('.dropdown-divider');
                            if (divider) {
                                dropdown.insertBefore(noNotificationsItem, divider.nextSibling);
                            }
                            
                            // Esconder o botão de marcar todas como lidas
                            markAllReadBtn.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => console.error('Erro ao marcar todas notificações como lidas:', error));
        });
    }
    
    // Atualizar notificações a cada 60 segundos
    setInterval(function() {
        const formData = new FormData();
        formData.append('action', 'get_notifications');
        
        fetch(`${ADMIN_URL}ajax/notifications.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationCount(data.count);
            }
        })
        .catch(error => console.error('Erro ao atualizar notificações:', error));
    }, 60000); // 60 segundos
}); 