// Funciones para la gestión de usuarios
document.addEventListener('DOMContentLoaded', function () {
    // Al cargar completamente el DOM, inicializa la gestión de usuarios
    initUserManagement();
});

function initUserManagement() {
    // Añade eventos de escucha a los filtros de búsqueda en la tabla

    // Input de texto para buscar por nombre/email
    document.getElementById('searchInput')?.addEventListener('input', filterTable);

    // Select para filtrar por rol (administrador, profesor, alumno)
    document.getElementById('rolFilter')?.addEventListener('change', filterTable);

    // Select para filtrar por estado (activo/inactivo)
    document.getElementById('statusFilter')?.addEventListener('change', filterTable);

    // Botón para mostrar/ocultar contraseña (si existe)
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', togglePasswordVisibility);
    }
}

function filterTable() {
    // Obtiene los valores actuales de los filtros
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const rolFilter = document.getElementById('rolFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    let visibleRows = 0;

    // Itera sobre todas las filas de la tabla de usuarios
    document.querySelectorAll('tbody tr').forEach(row => {
        // Obtiene los valores relevantes de cada celda (basado en la estructura de la tabla)
        const email = row.cells[1].textContent.toLowerCase();          // Columna Email
        const name = row.cells[2].textContent.toLowerCase();           // Columna Nombre
        const rol = row.cells[3].textContent.toLowerCase();            // Columna Rol (badge)
        const status = row.cells[5].textContent.trim().toLowerCase();  // Columna Estado

        // Verifica si coincide con el filtro de estado
        const matchesStatus = statusFilter === '' || status === statusFilter.toLowerCase();

        // Verifica si coincide con la búsqueda por nombre o correo
        const matchesSearch = email.includes(searchText) || name.includes(searchText);

        // Verifica si coincide con el filtro de rol
        const matchesRol = rolFilter === '' || rol.includes(rolFilter);

        // Si todos los filtros coinciden, muestra la fila; si no, la oculta
        if (matchesSearch && matchesRol && matchesStatus) {
            row.style.display = '';
            visibleRows++;
        } else {
            row.style.display = 'none';
        }
    });

    // Actualiza el contador de registros visibles
    document.getElementById('showingCount').textContent = visibleRows;
}

function togglePasswordVisibility() {
    // Alterna la visibilidad del campo de contraseña (si existe)

    const passwordField = document.getElementById('passwordField'); // Campo input password
    const icon = this.querySelector('i'); // Icono del botón

    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    // Cambia el icono entre ojo abierto y cerrado
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}
//--------------------------------------------/////////////////////////////////////////////////////////////////////7

///////////////////////////////////

// eliminación múltiple
document.getElementById('btnAbrirModalEliminarSeleccionados')?.addEventListener('click', function () {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        showNotification('No hay usuarios seleccionados.', 'warning', 'exclamation-triangle');
        return;
    }

    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);
    document.getElementById('btnConfirmarEliminarSeleccionados').dataset.ids = ids.join(',');

    const modal = new bootstrap.Modal(document.getElementById('modalEliminarSeleccionados'));
    modal.show();
});

// Confirmar eliminación múltiple
document.getElementById('btnConfirmarEliminarSeleccionados')?.addEventListener('click', function () {
    const ids = this.dataset.ids;
    fetch('../controllers/delete_users.php', {
        method: 'POST',
        body: new URLSearchParams({
            user_ids: ids
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminarSeleccionados')).hide();
                showNotification('Usuarios eliminados correctamente.', 'success', 'check-circle');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error: ' + data.message, 'danger', 'times-circle');
            }
        })
        .catch(err => {
            console.error('Error en fetch múltiple:', err);
            showNotification('Error de red al eliminar usuarios.', 'danger', 'times-circle');
        });
});

//////////////////////////////////////
// eliminación por fila
function confirmDelete(id) {
    document.getElementById('usuarioIdEliminar').value = id;
    let modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
    modal.show();
}

document.getElementById('btnConfirmarEliminar')?.addEventListener('click', function () {
    const id = document.getElementById('usuarioIdEliminar').value;

    fetch('../controllers/delete_users.php', {
        method: 'POST',
        body: new URLSearchParams({
            eliminar_individual: id
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario')).hide();
                showNotification('Usuario eliminado correctamente.', 'success', 'check-circle');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Error: ' + data.message, 'danger', 'times-circle');
            }
        })
        .catch(err => {
            console.error('Error al eliminar individual:', err);
            showNotification('Error de red al eliminar usuario.', 'danger', 'times-circle');
        });
});

////////////////////////////////////



function showNotification(message, type, icon) {
    var toastEl = document.getElementById('liveToast');
    var toastHeader = toastEl.querySelector('.toast-header');
    var toastBody = toastEl.querySelector('.toast-body');

    // Limpiar clases anteriores
    toastHeader.className = 'toast-header';
    toastBody.className = 'toast-body';

    // Añadir clases de color según el tipo
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
    var toast = new bootstrap.Toast(toastEl);
    toast.show();

    // Ocultar automáticamente después de 5 segundos
    setTimeout(() => toast.hide(), 5000);
}

function closeModalAndReload() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal'));
    if (modal) modal.hide();
    setTimeout(() => location.reload(), 500);
}
