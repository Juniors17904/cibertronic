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

// 👉 Detalles de asignación
$asig = $conn->query("
    SELECT 
        a.*,
        c.nombre_curso,
        h.dia, h.hora_inicio, h.hora_fin,
        p.nombre AS nombre_profesor,
        p.apellidos AS apellidos_profesor,
        p.dni AS dni_profesor,
        p.telefono AS telefono_profesor,
        p.codigo_usuario AS usuario_profesor_id,
        u.correo AS correo_profesor
    FROM asignaciones a
    JOIN cursos c ON a.curso_id = c.id
    JOIN horarios h ON a.horario_id = h.id
    JOIN profesores p ON a.profesor_id = p.id
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE a.codigo_asignacion = '{$conn->real_escape_string($asignacion_id)}'
")->fetch_assoc();

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
                            <!-- Columna 1 -->
                            <div class="col-md-6 mb-3">
                                <p><strong>Código:</strong> <?= htmlspecialchars($asig['codigo_asignacion']) ?></p>
                                <p><strong>Curso:</strong> <?= htmlspecialchars($asig['nombre_curso']) ?></p>
                                <p><strong>Horario:</strong> <?= htmlspecialchars("{$asig['dia']} {$asig['hora_inicio']} - {$asig['hora_fin']}") ?></p>
                            </div>
                            <!-- Columna 2 -->
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
                    <input id="searchInput" type="text" class="form-control w-auto" placeholder="Buscar alumno o código">
                    <select id="resultadoFilter" class="form-select w-auto">
                        <option value="">Todos los resultados</option>
                        <option value="Aprobado">Aprobado</option>
                        <option value="Desaprobado">Desaprobado</option>
                        <option value="Pendiente">Pendiente</option>
                    </select>
                </div>

                <!-- 📌 Tabla de Notas -->
                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalle de Notas</h5>
                    </div>
                    <div class="card-body">
                        <div class="mx-n3 mx-md-0">
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                                <table class="table table-bordered table-hover text-center table-sm mb-0">
                                    <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Código Alumno</th>
                                        <th>Alumno</th>
                                        <th>Nota 1</th>
                                        <th>Nota 2</th>
                                        <th>Nota 3</th>
                                        <th>Promedio</th>
                                        <th>Resultado</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaNotas">
                                    <tr>
                                        <td colspan="7">Cargando datos...</td>
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
            const tabla = document.getElementById('tablaNotas');
            const searchInput = document.getElementById('searchInput');
            const resultadoFilter = document.getElementById('resultadoFilter');
            let notas = [];

            fetch(`../../controllers/get_notas.php?asignacion_id=<?= urlencode($asignacion_id) ?>`)
                .then(res => res.json())
                .then(data => {
                    notas = data;
                    renderTabla();
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    tabla.innerHTML = '<tr><td colspan="7">Error al cargar los datos.</td></tr>';
                });

            [searchInput, resultadoFilter].forEach(el => el.addEventListener('input', renderTabla));

            function renderTabla() {
                tabla.innerHTML = '';

                const search = searchInput.value.toLowerCase();
                const filtroResultado = resultadoFilter.value;

                const filtradas = notas.map(d => {
                    let promedio = ((parseFloat(d.nota_01) + parseFloat(d.nota_02) + parseFloat(d.nota_03)) / 3).toFixed(1);
                    let resultado = promedio >= 11 ? 'Aprobado' : (promedio > 0 ? 'Desaprobado' : 'Pendiente');
                    return {
                        ...d,
                        promedio,
                        resultado
                    };
                }).filter(d => {
                    const coincideTexto = !search || d.alumno.toLowerCase().includes(search) || d.codigo_alumno.toLowerCase().includes(search);
                    const coincideResultado = !filtroResultado || d.resultado === filtroResultado;
                    return coincideTexto && coincideResultado;
                });

                if (filtradas.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="7">Sin resultados.</td></tr>';
                    return;
                }

                filtradas.forEach(d => {
                    const badge = d.resultado === 'Aprobado' ? 'bg-success' : d.resultado === 'Desaprobado' ? 'bg-danger' : 'bg-warning text-dark';
                    tabla.innerHTML += `
                <tr>
                    <td>${d.codigo_alumno}</td>
                    <td>${d.alumno}</td>
                    <td>${d.nota_01}</td>
                    <td>${d.nota_02}</td>
                    <td>${d.nota_03}</td>
                    <td>${d.promedio}</td>
                    <td><span class="badge ${badge}">${d.resultado}</span></td>
                </tr>`;
                });
            }
        });
    </script>

</body>

</html>