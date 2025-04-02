/**
 * Script principal do painel administrativo
 * Contém funções e inicializações para todas as páginas do admin
 */

document.addEventListener('DOMContentLoaded', function() {
    /**
     * Alternância da sidebar em dispositivos móveis
     * Permite colapsar/expandir o menu lateral em dispositivos pequenos
     */
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.sidebar').classList.toggle('d-none');
        });
    }

    /**
     * Validação de formulários
     * Aplica validação Bootstrap a formulários com a classe .needs-validation
     */
    const forms = document.querySelectorAll('.needs-validation');
    if (forms.length > 0) {
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    /**
     * Auto-fechamento de alertas
     * Fecha alertas de sucesso automaticamente após alguns segundos
     */
    const successAlerts = document.querySelectorAll('.alert-success');
    if (successAlerts.length > 0) {
        Array.from(successAlerts).forEach(alert => {
            setTimeout(() => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                } else {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        alert.remove();
                    }, 150);
                }
            }, 5000);
        });
    }

    /**
     * Tooltips
     * Inicializa tooltips do Bootstrap onde existirem
     */
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0) {
        Array.from(tooltipTriggerList).map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Máscaras para campos de formulário
     * Aplicar máscaras para formatos especiais de input (requer libs externas)
     */
    const dateMasks = document.querySelectorAll('.date-mask');
    if (dateMasks.length > 0 && typeof IMask !== 'undefined') {
        Array.from(dateMasks).forEach(element => {
            IMask(element, {
                mask: '00/00/0000'
            });
        });
    }

    /**
     * Confirmação de exclusão
     * Solicita confirmação antes de executar operações de exclusão
     */
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    if (deleteButtons.length > 0) {
        Array.from(deleteButtons).forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }
}); 