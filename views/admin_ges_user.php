<?php
require_once '../controllers/auth_check.php';
require_once '../controllers/get_admin_data.php';

$admin = getAdminData($conn, $_SESSION['user_id']);

if (!$admin) {
    die("Administrador no encontrado.");
}
include 'header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>
            <main class="col-md-7 col-lg-8 px-5">
                <div class="container-fluid p-4">
                    <?php
                    // Consultas optimizadas con manejo de errores
                    try {
                        $admins = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='administrador'")->fetch_row()[0];
                        $profesores = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='profesor'")->fetch_row()[0];
                        $alumnos = $conn->query("SELECT COUNT(*) FROM usuarios WHERE rol='alumno'")->fetch_row()[0];
                    } catch (Exception $e) {
                        die("Error en consultas: " . $e->getMessage());
                    }
                    ?>

                    <!-- Tarjetas Dinámicas -->
                    <div class="row mb-4">
                        <!-- Tarjeta Administradores -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle text-muted">Administradores</h6>
                                            <h3 class="card-title mb-0"><?= htmlspecialchars($admins) ?></h3>
                                        </div>
                                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                                            <i class="fas fa-user-shield text-primary fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta Profesores -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle text-muted">Profesores</h6>
                                            <h3 class="card-title mb-0"><?= htmlspecialchars($profesores) ?></h3>
                                        </div>
                                        <div class="bg-success bg-opacity-10 p-3 rounded">
                                            <i class="fas fa-chalkboard-teacher text-success fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tarjeta Alumnos -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle text-muted">Alumnos</h6>
                                            <h3 class="card-title mb-0"><?= htmlspecialchars($alumnos) ?></h3>
                                        </div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded">
                                            <i class="fas fa-user-graduate text-info fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Gestión de Usuarios</h5>
                            <div>
                                <button class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                                <div class="btn-group">
                                    <button class="btn btn-danger btn-sm me-2" id="deleteSelected">
                                        <i class="fas fa-trash me-1"></i> Borrar
                                    </button>
                                    <button class="btn btn-success btn-sm">
                                        <i class="fas fa-file-import me-1"></i> Importar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Barra de Búsqueda Avanzada -->
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
                                        <option value="administrador">Administradores</option>
                                        <option value="profesor">Profesores</option>
                                        <option value="alumno">Alumnos</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Todos los estados</option>
                                        <option value="activo">Activos</option>
                                        <option value="inactivo">Inactivos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tabla Responsive -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50px"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                            <th>Email</th>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Teléfono</th>
                                            <th>Estado</th>
                                            <th width="100px">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Consulta optimizada para obtener datos de usuarios
                                        $query = "SELECT u.*, 
                                                    COALESCE(a.nombre, p.nombre, ad.nombre) AS nombre_completo,
                                                    COALESCE(a.apellidos, p.apellidos, ad.apellidos) AS apellidos_completos,
                                                    COALESCE(a.telefono, p.telefono, ad.telefono) AS telefono_completo
                                                    FROM usuarios u
                                                    LEFT JOIN alumnos a ON u.id = a.usuario_id
                                                    LEFT JOIN profesores p ON u.id = p.usuario_id
                                                    LEFT JOIN administrador ad ON u.id = ad.usuario_id
                                                    ORDER BY u.id";

                                        try {
                                            $result = $conn->query($query);

                                            if ($result === false) {
                                                throw new Exception("Error en la consulta: " . $conn->error);
                                            }

                                            while ($user = $result->fetch_assoc()):
                                                $badgeColor = match ($user['rol']) {
                                                    'administrador' => 'bg-danger',
                                                    'profesor' => 'bg-success',
                                                    default => 'bg-info'
                                                };
                                        ?>
                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input user-checkbox" value="<?= $user['id'] ?>"></td>
                                                    <td><?= htmlspecialchars($user['correo']) ?></td>
                                                    <td><?= htmlspecialchars($user['nombre_completo'] . ' ' . $user['apellidos_completos']) ?></td>
                                                    <td><span class="badge <?= $badgeColor ?>"><?= ucfirst($user['rol']) ?></span></td>
                                                    <td><?= htmlspecialchars($user['telefono_completo'] ?? 'N/A') ?></td>
                                                    <td><span class="badge <?= $user['estado'] == 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= ucfirst($user['estado']) ?>
                                                        </span></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary"
                                                                data-bs-toggle="tooltip"
                                                                title="Editar"
                                                                onclick="editUser(<?= $user['id'] ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger"
                                                                data-bs-toggle="tooltip"
                                                                title="Eliminar"
                                                                onclick="confirmDelete(<?= $user['id'] ?>)">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            endwhile;
                                        } catch (Exception $e) {
                                            echo "<tr><td colspan='7' class='text-center text-danger'>" . $e->getMessage() . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pie de tabla con paginación -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Mostrando <span id="showingCount"><?= $result->num_rows ?? 0 ?></span> de <?= $result->num_rows ?? 0 ?> registros
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para nuevo usuario -->
                    <form id="userForm" action="../controllers/create_user.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rol</label>
                                <select class="form-select" name="rol" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="profesor">Profesor</option>
                                    <option value="alumno">Alumno</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="apellidos" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="userForm" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para filtrado en tiempo real
        document.getElementById('searchInput').addEventListener('input', filterTable);
        document.getElementById('rolFilter').addEventListener('change', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);

        // Seleccionar/deseleccionar todos los checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Eliminar usuarios seleccionados
        document.getElementById('deleteSelected').addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(el => el.value);
            if (selectedIds.length > 0 && confirm(`¿Eliminar ${selectedIds.length} usuario(s) seleccionado(s)?`)) {
                // Aquí iría la llamada AJAX para eliminar
                console.log("IDs a eliminar:", selectedIds);
            }
        });

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

        function editUser(id) {
            // Aquí iría la lógica para editar un usuario
            console.log("Editar usuario ID:", id);
            // Podrías abrir un modal similar al de nuevo usuario pero precargado
        }

        function confirmDelete(id) {
            if (confirm("¿Estás seguro de eliminar este usuario?")) {
                // Aquí iría la llamada AJAX para eliminar
                console.log("Eliminar usuario ID:", id);
            }
        }
    </script>
</body>

</html>