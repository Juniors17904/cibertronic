<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
require_once '../../config/config.php';

$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) {
    die("Administrador no encontrado.");
}

$id_asig = $_GET['id_asig'] ?? '';
$asignacion_id = $_GET['asignacion_id'] ?? '';
if (!$asignacion_id) {
    die("ID de asignación no válido.");
}

// Consulta de detalles de la asignación
$sql = "SELECT 
    a.codigo_asignacion,
    c.nombre_curso,
    h.dia, h.hora_inicio, h.hora_fin,
    '' AS fecha_inicio, '' AS fecha_fin, 'Activo' AS estado,
    p.nombre AS nombre_profesor,
    p.apellidos AS apellidos_profesor,
    p.usuario_id AS usuario_profesor_id,
    u.correo AS correo_profesor,
    p.dni AS dni_profesor,
    p.telefono AS telefono_profesor
            FROM asignaciones a
            JOIN cursos c ON a.curso_id = c.id
            JOIN horarios h ON a.horario_id = h.id
            JOIN profesores p ON a.profesor_id = p.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE a.codigo_asignacion = ?
            LIMIT 1
                    ";



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $asignacion_id);
$stmt->execute();
$result = $stmt->get_result();
$asig = $result->fetch_assoc();

if (!$asig) {
    die("No se encontró información de esta asignación.");
}

include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>
            <main class="col-md-8 col-lg-9 px-2 px-md-5 py-4">

                <!-- 📌 Detalles de la Asignación -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalles de Asignación</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p><strong>Código:</strong> <?= htmlspecialchars($asig['codigo_asignacion']) ?></p>
                                <p><strong>Curso:</strong> <?= htmlspecialchars($asig['nombre_curso']) ?></p>
                                <p><strong>Horario:</strong> <?= htmlspecialchars("{$asig['dia']} {$asig['hora_inicio']} - {$asig['hora_fin']}") ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong>Profesor:</strong> <?= htmlspecialchars($asig['nombre_profesor'] . ' ' . $asig['apellidos_profesor']) ?></p>
                                <p><strong>Correo:</strong> <?= htmlspecialchars($asig['correo_profesor']) ?></p>
                                <p><strong>DNI Profesor:</strong> <?= htmlspecialchars($asig['dni_profesor']) ?></p>
                                <p><strong>Teléfono Profesor:</strong> <?= htmlspecialchars($asig['telefono_profesor']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📌 Filtros -->
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <select id="estadoFilter" class="form-select w-auto">
                        <option value="">Todos los estados</option>
                        <option value="Presente">Presente</option>
                        <option value="Ausente">Ausente</option>
                        <option value="Justificado">Justificado</option>
                    </select>

                    <input id="searchInput" type="text" class="form-control w-auto" placeholder="Buscar alumno o código">

                    <input id="dateFilter" type="date" class="form-control w-auto">
                </div>

                <!-- 📌 Tabla de Asistencias -->
                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalle de Asistencias</h5>
                    </div>
                    <div class="card-body">
                        <div class="mx-n3 mx-md-0">
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                                <table class="table table-bordered table-hover text-center table-sm mb-0">
                                    <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Código Alumno</th>
                                        <th>Alumno</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaAsistencias">
                                    <tr>
                                        <td colspan="4">Cargando datos...</td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- ✅ Botón Volver -->
                        <a href="admin_detalle_asignaciones.php?asignacion_id=<?= $id_asig ?>" class="btn btn-secondary mt-3">
                            <i class=" fas fa-arrow-left me-2"></i> Volver
                        </a>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.getElementById('tablaAsistencias');
            const estadoFilter = document.getElementById('estadoFilter');
            const searchInput = document.getElementById('searchInput');
            const dateFilter = document.getElementById('dateFilter');
            let asistencias = [];

            fetch(`../../controllers/get_asistencias.php?asignacion_id=<?= $asignacion_id ?>`)
                .then(res => res.json())
                .then(data => {
                    asistencias = data;
                    renderTabla();
                })
                .catch(err => {
                    console.error(err);
                    tabla.innerHTML = '<tr><td colspan="4">Error al cargar los datos.</td></tr>';
                });

            [estadoFilter, searchInput, dateFilter].forEach(el => {
                el.addEventListener('input', renderTabla);
            });

            function renderTabla() {
                tabla.innerHTML = '';

                const estado = estadoFilter.value.toLowerCase();
                const search = searchInput.value.toLowerCase();
                const fecha = dateFilter.value;

                const filtradas = asistencias.filter(a => {
                    const coincideEstado = !estado || a.estado.toLowerCase() === estado;
                    const coincideSearch = !search || a.alumno.toLowerCase().includes(search) || a.codigo_usuario.toLowerCase().includes(search);
                    const coincideFecha = !fecha || a.fecha === fecha;
                    return coincideEstado && coincideSearch && coincideFecha;
                });

                if (filtradas.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="4">Sin resultados.</td></tr>';
                    return;
                }

                filtradas.forEach(d => {
                    tabla.innerHTML += `
                <tr>
                    <td>${d.fecha}</td>
                    <td>${d.codigo_usuario}</td>
                    <td>${d.alumno}</td>
                    <td>
                        <span class="badge ${d.estado === 'Presente' ? 'bg-success' : d.estado === 'Ausente' ? 'bg-danger' : 'bg-warning text-dark'}">
                            ${d.estado}
                        </span>
                    </td>
                </tr>`;
                });
            }
        });
    </script>

</body>

</html>