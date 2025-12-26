<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';

$admin = getAdminData($conn, $_SESSION['user_id']);
$notification = $_SESSION['notification'] ?? null;
unset($_SESSION['notification']);
unset($_SESSION['show_modal']); // Limpiar modal

if (!$admin) {
    die("Administrador no encontrado.");
}
include '../header.php';

// ✅ Recibir filtro rol y estado
$rol = $_GET['rol'] ?? '';
$estado = $_GET['estado'] ?? '';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>
            <main class="col-md-7 col-lg-9 px-2 px-md-5">
                <div class="container-fluid p-4">
                    <?php
                    try {
                        $admins = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='administrador'")->fetch_row()[0];
                        $profesores = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='profesor'")->fetch_row()[0];
                        $alumnos = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='alumno'")->fetch_row()[0];
                    } catch (Exception $e) {
                        die("Error en consultas: " . $e->getMessage());
                    }
                    ?>

                    <!-- Tarjetas -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted">Administradores</h6>
                                        <h3><?= htmlspecialchars($admins) ?></h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-user-shield text-primary fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-success shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted">Profesores</h6>
                                        <h3><?= htmlspecialchars($profesores) ?></h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-chalkboard-teacher text-success fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-info shadow-sm h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted">Alumnos</h6>
                                        <h3><?= htmlspecialchars($alumnos) ?></h3>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-user-graduate text-info fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-users-cog me-2"></i>Gestión de Usuarios</h5>
                            <div>
                                <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                                <div class="btn-group">
                                    <button class="btn btn-danger btn-sm me-2" id="btnAbrirModalEliminarSeleccionados">
                                        <i class="fas fa-trash me-1"></i> Borrar
                                    </button>
                                    <button class="btn btn-success btn-sm">
                                        <i class="fas fa-file-import me-1"></i> Importar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filtros -->
                            <div class="row mb-3 g-2">

                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, email...">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <select class="form-select" id="rolFilter">
                                        <option value="">Todos los roles</option>
                                        <option value="administrador" <?= $rol == 'administrador' ? 'selected' : '' ?>>Administradores</option>
                                        <option value="profesor" <?= $rol == 'profesor' ? 'selected' : '' ?>>Profesores</option>
                                        <option value="alumno" <?= $rol == 'alumno' ? 'selected' : '' ?>>Alumnos</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Todos los estados</option>
                                        <option value="activo" <?= $estado == 'activo' ? 'selected' : '' ?>>Activos</option>
                                        <option value="inactivo" <?= $estado == 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tabla -->
                            <div class="table-responsive" style=" max-height: 500px; overflow-y: auto;">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th></th>
                                            <th>Código Usuario</th>
                                            <th>Email</th>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Rol</th>
                                            <th>Teléfono</th>
                                            <th>Estado</th>
                                            <th width="100px">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php
                                        $query = "SELECT 
                                                u.*, 
                                                COALESCE(a.nombre, p.nombre, ad.nombre) AS nombre_completo,
                                                COALESCE(a.apellidos, p.apellidos, ad.apellidos) AS apellidos_completos,
                                                COALESCE(a.telefono, p.telefono, ad.telefono) AS telefono_completo,
                                                COALESCE(a.dni, p.dni, ad.dni) AS dni_completo,
                                                COALESCE(a.codigo_usuario, p.codigo_usuario, ad.codigo_usuario) AS codigo_usuario
                                            FROM usuarios u
                                            LEFT JOIN alumnos a ON u.id = a.usuario_id
                                            LEFT JOIN profesores p ON u.id = p.usuario_id
                                            LEFT JOIN administrador ad ON u.id = ad.usuario_id
                                            WHERE 1";

                                        if ($rol !== '') {
                                            $query .= " AND u.rol = '$rol'";
                                        }
                                        if ($estado !== '') {
                                            $query .= " AND u.estado = '$estado'";
                                        }

                                        $query .= " ORDER BY u.id";


                                        try {
                                            $result = $conn->query($query);
                                            if ($result === false) throw new Exception("Error: " . $conn->error);
                                            while ($user = $result->fetch_assoc()):
                                                $badgeColor = match ($user['rol']) {
                                                    'administrador' => 'bg-danger',
                                                    'profesor' => 'bg-success',
                                                    default => 'bg-info'
                                                };
                                        ?>
                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input user-checkbox" value="<?= $user['id'] ?>"></td>
                                                    <td><?= htmlspecialchars($user['codigo_usuario'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($user['correo']) ?></td>
                                                    <td><?= htmlspecialchars($user['nombre_completo'] . ' ' . $user['apellidos_completos']) ?></td>
                                                    <td><?= htmlspecialchars($user['dni_completo'] ?? 'N/A') ?></td>
                                                    <td><span class="badge <?= $badgeColor ?>"><?= ucfirst($user['rol']) ?></span></td>
                                                    <td><?= htmlspecialchars($user['telefono_completo'] ?? 'N/A') ?></td>
                                                    <td><span class="badge <?= $user['estado'] == 'activo' ? 'bg-success' : 'bg-secondary' ?>"><?= ucfirst($user['estado']) ?></span></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary" onclick="editUser(<?= $user['id'] ?>)"><i class="fas fa-edit"></i></button>
                                                            <button class="btn btn-outline-danger" onclick="confirmDelete(<?= $user['id'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php endwhile;
                                        } catch (Exception $e) {
                                            echo "<tr><td colspan='8'>" . $e->getMessage() . "</td></tr>";
                                        } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-between">
                                <div class="text-muted">
                                    Mostrando <?= $result->num_rows ?? 0 ?> registros
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../modals/modal_eliminar_usuario.php'; ?>
    <?php include '../modals/modal_eliminar_multiple.php'; ?>
    <?php include '../modals/modal_agregar_nuevo_usuario.php'; ?>
    <?php include '../modals/toast_notificacion.php'; ?>
    <?php include '../modals/modal_editar_usuario.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/admin-users.js"></script>
    <script>
        ///////////////////////////////


        // editar usuario
        function editUser(id) {
            console.log('🛠️ Iniciando edición de usuario. ID recibido:', id);

            fetch(`../../controllers/get_user_data.php?id=${id}`)
                .then(res => {
                    console.log('📡 Respuesta recibida del servidor.');
                    return res.json();
                })
                .then(data => {
                    console.log('📦 Datos recibidos:', data);
                    if (data.success) {
                        const user = data.user;
                        console.log('✅ Usuario válido. Mostrando datos en el formulario.');

                        document.getElementById('editarUsuarioId').value = user.id;
                        document.getElementById('editarNombre').value = user.nombre;
                        document.getElementById('editarApellidos').value = user.apellidos;
                        document.getElementById('editarCorreo').value = user.correo;
                        document.getElementById('editarTelefono').value = user.telefono;
                        document.getElementById('editarRol').value = user.rol;
                        document.getElementById('editarEstado').value = user.estado;
                        document.getElementById('editarDNI').value = user.dni || '';

                        // La contraseña NO se llena por seguridad

                        const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
                        modal.show();
                    } else {
                        showNotification('No se pudo cargar el usuario.', 'danger', 'exclamation-triangle');
                    }
                })
                .catch(err => {
                    console.error('❌ Error al obtener datos del usuario:', err);
                    showNotification('Error de red al cargar usuario.', 'danger', 'times-circle');
                });
        }

        document.getElementById('formEditarUsuario')?.addEventListener('submit', function(e) {
            e.preventDefault(); // ⛔ evita recargar la página
            const form = this;

            const formData = new FormData(form);
            console.log('📤 Enviando datos del formulario:', Object.fromEntries(formData.entries()));

            fetch('../../controllers/update_user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    console.log('📥 Respuesta del servidor:', data);
                    if (data.success) {
                        showNotification('Usuario actualizado correctamente.', 'success', 'check-circle');
                        bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario')).hide();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'danger', 'times-circle');
                    }
                })
                .catch(err => {
                    console.error('❌ Error de red:', err);
                    showNotification('Error de red al actualizar usuario.', 'danger', 'times-circle');
                });
        });

        ///////////////////////////////////

        // eliminación múltiple
        document.getElementById('btnAbrirModalEliminarSeleccionados')?.addEventListener('click', function() {
            console.log('btn eliminar seleccionados');
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
        document.getElementById('btnConfirmarEliminarSeleccionados')?.addEventListener('click', function() {
            console.log('confirmar eliminar seleccionados');
            const ids = this.dataset.ids;
            fetch('../../controllers/delete_users.php', {
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
            console.log('btn confirmar eliminacion individual');
            document.getElementById('usuarioIdEliminar').value = id;
            let modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
            modal.show();
        }

        document.getElementById('btnConfirmarEliminar')?.addEventListener('click', function() {
            const id = document.getElementById('usuarioIdEliminar').value;

            fetch('../../controllers/delete_users.php', {
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

        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar modal solo si hay errores y específicamente se indica mostrar el modal
            <?php if (!empty($notification) && $notification['type'] === 'danger' && isset($_SESSION['show_modal'])): ?>
                var myModal = new bootstrap.Modal(document.getElementById('nuevoUsuarioModal'));
                myModal.show();
            <?php endif; ?>

            // Mostrar notificación toast para todos los mensajes
            <?php if (!empty($notification)): ?>
                showNotification(
                    '<?= addslashes($notification['message']) ?>',
                    '<?= $notification['type'] ?>',
                    '<?= $notification['icon'] ?>'
                );
            <?php endif; ?>

            // Prevenir múltiples envíos del formulario
            document.getElementById('userForm')?.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            });
        });

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

        //////////////////////////////////////////////

        document.getElementById('rolFilter').addEventListener('change', updateFilters);
        document.getElementById('statusFilter').addEventListener('change', updateFilters);

        function updateFilters() {
            const rol = document.getElementById('rolFilter').value;
            const estado = document.getElementById('statusFilter').value;
            let url = '?';
            if (rol) url += 'rol=' + rol + '&';
            if (estado) url += 'estado=' + estado;
            location.href = url;
        }


        //////////////////////////////////////////////////
    </script>

    <script>
        // ✅ Confirmar eliminación INDIVIDUAL verificando asignaciones si es profesor
        function confirmDelete(id) {
            console.log('⏳ Verificando asignaciones del usuario:', id);

            fetch(`../../controllers/get_asignaciones_por_profesor.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.total > 0) {
                        showNotification(`Este profesor tiene ${data.total} asignación(es). No se recomienda eliminarlo sin reasignar.`, 'warning', 'exclamation-triangle');
                    } else {
                        document.getElementById('usuarioIdEliminar').value = id;
                        let modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
                        modal.show();
                    }
                })
                .catch(err => {
                    console.error('❌ Error al verificar asignaciones:', err);
                    showNotification('Error de red al verificar asignaciones.', 'danger', 'times-circle');
                });
        }
    </script>


</body>

</html>