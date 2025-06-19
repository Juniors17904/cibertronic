<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) die("Administrador no encontrado.");
include '../header.php';
?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <main class="col-md-7 col-lg-8 px-5 py-4">
                <div class="card shadow mb-4">
                    <!-- Header bien estructurado -->
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Matrículas Registradas</h4>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaMatricula">
                            <i class="fas fa-plus-circle me-1"></i> Nueva Matrícula
                        </button>
                    </div>

                    <!-- Contenido dentro del cuerpo de la tarjeta -->
                    <div class="card-body">
                        <?php
                        $sql = "SELECT m.id, CONCAT(a.nombre, ' ', a.apellidos) AS alumno,
                        ar.nombre_area AS area, c.nombre_curso AS curso,
                        CONCAT(h.dia, ' ', h.hora_inicio, ' - ', h.hora_fin) AS horario
                    FROM matriculas m
                    INNER JOIN alumnos a ON m.alumno_id = a.id
                    INNER JOIN cursos c ON m.curso_id = c.id
                    INNER JOIN areas ar ON c.id_area = ar.id
                    INNER JOIN horarios h ON m.horario_id = h.id
                    ORDER BY alumno ASC";
                        $matriculas = $conn->query($sql);
                        ?>

                        <?php if ($matriculas && $matriculas->num_rows > 0): ?>
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Alumno</th>
                                        <th>Área</th>
                                        <th>Curso</th>
                                        <th>Horario</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php $n = 1;
                                    while ($row = $matriculas->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $n++ ?></td>
                                            <td><?= htmlspecialchars($row['alumno']) ?></td>
                                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['area']) ?></span></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['curso']) ?></span></td>
                                            <td><?= htmlspecialchars($row['horario']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-warning text-center mb-0">No hay matrículas registradas.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>


        </div>
    </div>



    <?php
    include '../modals/toast_notificacion.php';
    include '../modals/modal_nueva_matricula.php';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cuando cambia el área
        document.getElementById('area')?.addEventListener('change', function() {
            const areaId = this.value;
            const curso = document.getElementById('curso');
            const horario = document.getElementById('horario');

            curso.disabled = true;
            horario.disabled = true;
            curso.innerHTML = '<option value="">Cargando cursos...</option>';
            horario.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

            if (!areaId) return;

            fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                .then(res => res.json())
                .then(data => {
                    curso.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.nombre_curso;
                        curso.appendChild(opt);
                    });
                    curso.disabled = false;
                });
        });

        // Cuando cambia el curso
        document.getElementById('curso')?.addEventListener('change', function() {
            const cursoId = this.value;
            const horario = document.getElementById('horario');

            horario.disabled = true;
            horario.innerHTML = '<option value="">Cargando horarios...</option>';

            if (!cursoId) return;

            fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                .then(res => res.json())
                .then(data => {
                    horario.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                    data.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h.id;
                        opt.textContent = `${h.dia} ${h.hora_inicio} - ${h.hora_fin}`;
                        horario.appendChild(opt);
                    });
                    horario.disabled = false;
                });
        });

        // Envío del formulario
        document.getElementById('formMatriculaModal')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);

            fetch('../../controllers/insert_matricula.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(data => {
                    const modalEl = document.getElementById('modalNuevaMatricula');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    modalEl.addEventListener('hidden.bs.modal', () => {
                        document.querySelector('.modal-backdrop')?.remove();
                        showNotification(
                            data.success ? 'Matrícula guardada correctamente.' : data.message,
                            data.success ? 'success' : 'danger',
                            data.success ? 'check' : 'times'
                        );
                        setTimeout(() => location.reload(), 1500);
                    }, {
                        once: true
                    });
                })
                .catch(err => {
                    console.error('❌ Error al guardar:', err);
                    showNotification('Error de red al registrar matrícula', 'danger', 'exclamation-triangle');
                });
        });

        function showNotification(message, type, icon) {
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
        }

        document.getElementById('modalNuevaMatricula').addEventListener('hidden.bs.modal', () => {
            const toast = bootstrap.Toast.getInstance(document.getElementById('liveToast'));
            toast?.hide();
        });
    </script>


</body>

</html>