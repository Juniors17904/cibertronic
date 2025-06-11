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
        const email = row.cells[1].textContent.toLowerCase();       // Col 1: Email
        const name = row.cells[2].textContent.toLowerCase();        // Col 2: Nombre
        const rol = row.cells[3].textContent.toLowerCase();        // Col 3: Rol
        const status = row.cells[5].textContent.trim().toLowerCase(); // Col 5: Estado (ACTIVO/INACTIVO)

        const matchesStatus =
            statusFilter === '' ||
            status === statusFilter.toLowerCase(); // Comparación EXACTA

        const matchesSearch = email.includes(searchText) || name.includes(searchText);
        const matchesRol = rolFilter === '' || rol.includes(rolFilter);

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
