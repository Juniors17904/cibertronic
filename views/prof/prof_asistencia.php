<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_prof_data.php';

$prof = getProfData($conn, $_SESSION['user_id']);
if (!$prof) {
    die("Profesor no encontrado.");
}

$nombre_curso = $_POST['nombre_curso'] ?? '';
$horario_texto = $_POST['horario_texto'] ?? '';
$curso_id = $_POST['curso_id'] ?? null;
$horario_id = $_POST['horario_id'] ?? null;


// Obtener código de asignación
$codigo_asignacion = '';
$sql = "SELECT codigo_asignacion FROM asignaciones WHERE curso_id = ? AND horario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $curso_id, $horario_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $codigo_asignacion = $row['codigo_asignacion'];
}
$stmt->close();




if (!$curso_id || !$horario_id) {
    die("Faltan datos.");
}

include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>
            <!-- <main class="col-md-7 col-lg-8 px-5 py-4"> -->
            <main class="col-md-8 col-lg-9 px-md-5 py-4">
                <h3 class="text-primary mb-4"><i class="fas fa-clipboard-check me-2"></i>Registrar Asistencia</h3>
                <div class="card shadow border-0 mb-4">
                    <div class="card-body">
                        <p><strong>Curso:</strong> <?= htmlspecialchars($nombre_curso) ?></p>
                        <p><strong>Horario:</strong> <?= htmlspecialchars($horario_texto) ?></p>
                        <p><strong>Código de Asignación:</strong> <?= htmlspecialchars($codigo_asignacion) ?></p>


                        <form id="formAsistencia" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Fecha:</label>
                                    <input type="date" id="fechaAsistencia" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <input type="hidden" name="curso_id" id="curso_id" value="<?= $curso_id ?>">
                            <input type="hidden" name="horario_id" id="horario_id" value="<?= $horario_id ?>">

                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered align-middle">


                                    <thead class="table-success text-center sticky-top">
                                        <tr id="theadRow">
                                            <th>Código de Alumno</th>
                                            <th>Nombres</th>
                                            <th>Apellidos</th>
                                            <th>Presente</th>
                                            <th>Ausente</th>
                                            <th>Justificado</th>
                                            <!-- Columna dinámica solo si hay estado -->
                                        </tr>
                                    </thead>


                                    <tbody id="tablaAlumnos">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Cargando...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Guardar Asistencia
                                </button>
                                <button type="button" id="btnCancelar" class="btn btn-secondary">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../modals/toast_notificacion.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cursoId = document.getElementById('curso_id').value;
            const horarioId = document.getElementById('horario_id').value;
            const fechaInput = document.getElementById('fechaAsistencia');
            const tbody = document.getElementById('tablaAlumnos');



            function cargarAlumnos(fecha) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>';

                fetch(`../../controllers/get_asistencia_fecha.php?curso_id=${cursoId}&horario_id=${horarioId}&fecha=${fecha}`)
                    .then(res => res.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        const theadRow = document.getElementById('theadRow');

                        // Quitar cabecera vieja si existe
                        if (document.getElementById('estadoHeader')) {
                            document.getElementById('estadoHeader').remove();
                        }

                        // Si hay estado en algún alumno, poner cabecera extra
                        const tieneEstado = data.some(al => al.estado);
                        if (tieneEstado) {
                            const th = document.createElement('th');
                            th.textContent = 'Estado';
                            th.id = 'estadoHeader';
                            theadRow.appendChild(th);
                        }

                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay alumnos.</td></tr>';
                        } else {
                            data.forEach(alumno => {
                                const estado = alumno.estado || '';
                                const checked = (valor) => estado === valor ? 'checked' : '';

                                let icono = '';
                                if (estado === 'Presente') {
                                    icono = `<i class="fas fa-check-circle text-success ms-2"></i>`;
                                } else if (estado === 'Ausente') {
                                    icono = `<i class="fas fa-times-circle text-danger ms-2"></i>`;
                                } else if (estado === 'Justificado') {
                                    icono = `<i class="fas fa-file-alt text-warning ms-2"></i>`;
                                }

                                const row = `
                                            <tr>
                                                <td class="text-center">${alumno.codigo_usuario}</td>
                                                <td>${alumno.nombre}</td>
                                                <td>${alumno.apellidos}</td>
                                                <td class="text-center"><input type="radio" name="asistencia[${alumno.id}]" value="Presente" ${checked('Presente')} required></td>
                                                <td class="text-center"><input type="radio" name="asistencia[${alumno.id}]" value="Ausente" ${checked('Ausente')}></td>
                                                <td class="text-center"><input type="radio" name="asistencia[${alumno.id}]" value="Justificado" ${checked('Justificado')}></td>
                                                ${tieneEstado ? `<td class="text-center">${icono}</td>` : ''}
                                            </tr>`;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                        }
                    })
                    .catch(() => {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar.</td></tr>';
                    });
            }




            // Cargar asistencia al iniciar
            cargarAlumnos(fechaInput.value);

            // Actualizar si cambia la fecha
            fechaInput.addEventListener('change', () => {
                cargarAlumnos(fechaInput.value);
            });

            // Enviar formulario
            document.getElementById('formAsistencia').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('../../controllers/insert_asistencia.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            sessionStorage.setItem('flash_success', 'Asistencia guardada correctamente');
                            window.location.href = 'prof_courses.php';
                        } else {
                            showNotification(data.error || 'No se pudo guardar', 'danger', 'exclamation-circle');
                        }
                    })
                    .catch(() => {
                        showNotification('Error de red o del servidor', 'danger', 'server');
                    });
            });

            // Cancelar
            document.getElementById('btnCancelar').addEventListener('click', () => {
                sessionStorage.setItem('flash_warning', 'Acción cancelada por el usuario');
                window.location.href = 'prof_courses.php';
            });

            // Toast Bootstrap
            function showNotification(message, type = 'success', icon = 'check-circle') {
                const toastEl = document.getElementById('liveToast');
                const toastHeader = toastEl.querySelector('.toast-header');
                const toastBody = toastEl.querySelector('.toast-body');

                toastHeader.className = 'toast-header';
                toastBody.className = 'toast-body';

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
                setTimeout(() => toast.hide(), 5000);
            }
        });
    </script>


</body>

</html>