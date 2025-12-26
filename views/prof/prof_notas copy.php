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
$codigo_asignacion = $_POST['codigo_asignacion'] ?? '';


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
            <main class="col-md-8 col-lg-9 px-5 py-4">

                <h3 class="text-primary mb-4"><i class="fas fa-clipboard me-2"></i>Registro de Notas</h3>

                <div class="card shadow border-0 mb-4">
                    <div class="card-body">
                        <!-- Información del curso -->
                        <p><strong>Curso:</strong> <?= htmlspecialchars($nombre_curso) ?></p>
                        <p><strong>Horario:</strong> <?= htmlspecialchars($horario_texto) ?></p>
                        <p><strong>Código de Asignación:</strong>
                            <?= !empty($codigo_asignacion) ? htmlspecialchars($codigo_asignacion) : '<span class="text-danger">No disponible</span>' ?>
                        </p>

                        <!-- Formulario -->
                        <form id="formNotas" method="POST">
                            <input type="hidden" name="curso_id" id="curso_id" value="<?= $curso_id ?>">
                            <input type="hidden" name="horario_id" id="horario_id" value="<?= $horario_id ?>">
                            <input type="hidden" name="codigo_asignacion" value="<?= htmlspecialchars($codigo_asignacion) ?>">

                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th>Alumno</th>
                                            <th>Examen 1</th>
                                            <th>Examen 2</th>
                                            <th>Examen 3</th>
                                            <th>Promedio General</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaNotas">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Cargando...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Guardar Notas
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
            const tbody = document.getElementById('tablaNotas');

            fetch(`../../controllers/get_alumnos_matriculados.php?curso_id=${cursoId}&horario_id=${horarioId}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay alumnos.</td></tr>';
                    } else {
                        data.forEach(alumno => {
                            const n1 = alumno.n1 !== null ? alumno.n1 : '';
                            const n2 = alumno.n2 !== null ? alumno.n2 : '';
                            const n3 = alumno.n3 !== null ? alumno.n3 : '';

                            const row = `
                                <tr>
                                <td>${alumno.nombre}</td>
                                <td><input type="number" step="0.1" inputmode="decimal" lang="en" class="form-control nota" name="notas[${alumno.id}][n1]" value="${n1}" placeholder="--" min="0" max="20"></td>
                                <td><input type="number" step="0.1" inputmode="decimal" lang="en" class="form-control nota" name="notas[${alumno.id}][n2]" value="${n2}" placeholder="--" min="0" max="20"></td>
                                <td><input type="number" step="0.1" inputmode="decimal" lang="en" class="form-control nota" name="notas[${alumno.id}][n3]" value="${n3}" placeholder="--" min="0" max="20"></td>
                                <td class="text-center promedio">-</td>
                                <td class="text-center estado">-</td>
                                    </tr>`;

                            tbody.insertAdjacentHTML('beforeend', row);
                        });

                        configurarEventosNotas();
                        calcularPromedios();
                    }
                });

            function configurarEventosNotas() {
                tbody.querySelectorAll('.nota').forEach(input => {
                    input.addEventListener('input', () => {
                        input.value = input.value.replace(/^0+(?!$)/, ''); // eliminar ceros iniciales

                        const val = parseFloat(input.value);
                        if (val > 20) {
                            input.value = '';
                            showNotification('Nota no válida (máximo 20)', 'warning', 'exclamation-triangle');
                        }

                        calcularPromedios();
                    });
                });
            }

            function calcularPromedios() {
                tbody.querySelectorAll('tr').forEach(fila => {
                    const inputs = fila.querySelectorAll('.nota');
                    let suma = 0;
                    let count = 0;

                    inputs.forEach(input => {
                        const val = parseFloat(input.value);
                        if (!isNaN(val)) {
                            suma += val;
                            count++;
                        }
                    });

                    const promedioEl = fila.querySelector('.promedio');
                    const estadoEl = fila.querySelector('.estado');

                    if (count === 0) {
                        promedioEl.textContent = '--';
                        estadoEl.textContent = '--';
                        estadoEl.className = 'text-center estado text-secondary';
                    } else {
                        let promedio = (suma / 3).toFixed(0);
                        if (promedio < 10) promedio = '0' + promedio;

                        promedioEl.textContent = promedio;

                        if (promedio == 0.00) {
                            estadoEl.textContent = 'Pendiente';
                            estadoEl.className = 'text-center estado text-warning';
                        } else if (promedio >= 11) {
                            estadoEl.textContent = 'Aprobado';
                            estadoEl.className = 'text-center estado text-success';
                        } else {
                            estadoEl.textContent = 'Desaprobado';
                            estadoEl.className = 'text-center estado text-danger';
                        }
                    }
                });
            }

            document.getElementById('formNotas').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('../../controllers/insert_notas.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Notas guardadas correctamente', 'success', 'check-circle');
                            setTimeout(() => window.location.href = 'prof_courses.php', 2000);
                        } else {
                            showNotification(data.error || 'No se pudo guardar', 'danger', 'exclamation-circle');
                        }
                    })
                    .catch(() => {
                        showNotification('Error de red o servidor', 'danger', 'server');
                    });
            });

            document.getElementById('btnCancelar').addEventListener('click', () => {
                sessionStorage.setItem('flash_warning', 'Acción cancelada por el usuario');
                window.location.href = 'prof_courses.php';
            });

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
                new bootstrap.Toast(toastEl).show();
            }
        });
    </script>


</body>

</html>