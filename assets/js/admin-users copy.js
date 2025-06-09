// Funciones para la gestión de usuarios
document.addEventListener('DOMContentLoaded', function () {
    // Inicialización de eventos
    initUserManagement();





});

function initUserManagement() {
    // Filtrado de tabla
    document.getElementById('searchInput')?.addEventListener('input', filterTable);
    document.getElementById('rolFilter')?.addEventListener('change', filterTable);
    document.getElementById('statusFilter')?.addEventListener('change', filterTable);

    // Selección múltiple
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Eliminar seleccionados
    document.getElementById('deleteSelected')?.addEventListener('click', deleteSelectedUsers);

    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', togglePasswordVisibility);
    }
}

function filterTable() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const rolFilter = document.getElementById('rolFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    let visibleRows = 0;

    document.querySelectorAll('tbody tr').forEach(row => {
        const email = row.cells[1].textContent.toLowerCase();
        const name = row.cells[2].textContent.toLowerCase();
        const rol = row.cells[3].textContent.toLowerCase();
        const status = row.cells[5].textContent.toLowerCase();

        const matchesSearch = email.includes(searchText) || name.includes(searchText);
        const matchesRol = rolFilter === '' || rol.includes(rolFilter);
        const matchesStatus = statusFilter === '' || status.includes(statusFilter);

        if (matchesSearch && matchesRol && matchesStatus) {
            row.style.display = '';
            visibleRows++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('showingCount').textContent = visibleRows;
}

function togglePasswordVisibility() {
    const passwordField = document.getElementById('passwordField');
    const icon = this.querySelector('i');

    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);

    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

// Funciones globales que podrían usarse desde otros lugares
function editUser(id) {
    console.log("Editar usuario ID:", id);
    // Implementación real aquí
}

// Función para confirmar eliminación (corregida)
function confirmDelete(id) {
    if (confirm("¿Estás seguro de eliminar este usuario?")) {
        fetch('../controllers/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + data.message);
            })
            .catch(error => console.error('Error:', error));
    }
}

// Función para eliminar múltiples (corregida)
function deleteSelectedUsers() {
    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(el => el.value);

    if (selectedIds.length === 0) return alert('No hay usuarios seleccionados');

    if (confirm(`¿Eliminar ${selectedIds.length} usuario(s)?`)) {
        const formData = new FormData();
        formData.append('user_ids', selectedIds.join(','));

        fetch('../controllers/delete_users.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + data.message);
            })
            .catch(error => console.error('Error:', error));
    }
}


