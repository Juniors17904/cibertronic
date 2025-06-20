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

            <main class="col-md-6 col-lg-7 px-5 py-4">
                <h3 class="text-primary mb-4"><i class="fas fa-clipboard me-2"></i>Registro de Notas</h3>

                <div class="card shadow border-0 mb-4">
                    <div class="card-body">
                        <p><strong>Curso:</strong> <?= htmlspecialchars($nombre_curso) ?></p>
                        <p><strong>Horario:</strong> <?= htmlspecialchars($horario_texto) ?></p>

                        <form id="formNotas" method="POST">
                            <input type="hidden" name="curso_id" id="curso_id" value="<?= $curso_id ?>">
                            <input type="hidden" name="horario_id" id="horario_id" value="<?= $horario_id ?>">

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-info text-center">
                                        <tr>
                                            <th>Alumno</th>
                                            <th>Nota 1</th>
                                            <th>Nota 2</th>
                                            <th>Nota 3</th>
                                            <th>Promedio</th>
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
                                <a href="prof_courses.php" class="btn btn-secondary">Cancelar</a>
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
                            const row = `
                        <tr>
                            <td>${alumno.nombre}</td>
                            <td><input type="number" class="form-control" name="notas[${alumno.id}][n1]" min="0" max="20"></td>
                            <td><input type="number" class="form-control" name="notas[${alumno.id}][n2]" min="0" max="20"></td>
                            <td><input type="number" class="form-control" name="notas[${alumno.id}][n3]" min="0" max="20"></td>
                            <td class="text-center text-secondary">-</td>
                            <td class="text-center text-secondary">-</td>
                        </tr>`;
                            tbody.insertAdjacentHTML('beforeend', row);
                        });
                    }
                });

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