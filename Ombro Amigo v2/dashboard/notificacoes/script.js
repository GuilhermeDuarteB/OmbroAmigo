document.addEventListener('DOMContentLoaded', function() {
    // Inicializar com todas as notificações visíveis
    filterNotifications('todas');
});

function filterNotifications(status) {
    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => {
        if (status === 'todas' || item.classList.contains(status)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    // Atualizar botões ativos
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(button => {
        button.classList.remove('active');
    });
    document.querySelector(`button[onclick="filterNotifications('${status}')"]`).classList.add('active');
} 