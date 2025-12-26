function showNotification(message, type = 'success', icon = 'check') {
    const toastEl = document.getElementById('liveToast');
    const toastHeader = toastEl.querySelector('.toast-header');
    const toastBody = toastEl.querySelector('.toast-body');

    // Limpiar clases
    toastHeader.className = 'toast-header';
    toastBody.className = 'toast-body';

    // Estilos según tipo
    switch (type) {
        case 'success':
            toastHeader.classList.add('bg-success', 'text-white');
            break;
        case 'danger':
            toastHeader.classList.add('bg-danger', 'text-white');
            break;
        case 'warning':
            toastHeader.classList.add('bg-warning', 'text-dark');
            break;
        default:
            toastHeader.classList.add('bg-primary', 'text-white');
    }

    toastBody.innerHTML = `<i class="fas fa-${icon} me-2"></i> ${message}`;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Ejemplo si quieres que funcione con botón con id "btnToast"
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnToast');
    if (btn) {
        btn.addEventListener('click', () => {
            showNotification('Este es un mensaje de prueba 🔔', 'success', 'check');
        });
    }
});
