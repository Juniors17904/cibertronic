<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_admin_data.php';
$admin = getAdminData($conn, $_SESSION['user_id']);
if (!$admin) die("Administrador no encontrado.");
include '../header.php';
?>


<?php
$area_id = $_GET['area_id'] ?? '';
$curso_id = $_GET['curso_id'] ?? '';
$horario_id = $_GET['horario_id'] ?? '';
?>



<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <main class="col-md-8 col-lg-9 px-md-5 py-4">

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Asignaciones Registradas</h4>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAsignarDocente">
                            <i class="fas fa-plus"></i> Nuevo Registro
                        </button>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT 
                                a.id, a.codigo_asignacion,
                                a.curso_id, a.horario_id, a.profesor_id,
                                c.nombre_curso,
                                h.dia, h.hora_inicio, h.hora_fin,
                                p.nombre AS nombre_profesor, p.apellidos, p.codigo_usuario,
                                (
                                    SELECT COUNT(*) 
                                    FROM matriculas m 
                                    WHERE m.curso_id = a.curso_id AND m.horario_id = a.horario_id
                                ) AS cantidad_alumnos
                                FROM asignaciones a
                                JOIN cursos c ON a.curso_id = c.id
                                JOIN horarios h ON a.horario_id = h.id
                                JOIN profesores p ON a.profesor_id = p.id
                                ORDER BY c.nombre_curso ASC, h.dia ASC";

                        $asignaciones = $conn->query($query);
                        $total_asignaciones = ($asignaciones && $asignaciones->num_rows > 0) ? $asignaciones->num_rows : 0;
                        ?>

                        <!-- Tarjeta resumen -->
                        <div class="card border-primary shadow mb-4 w-50 text-center">
                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-tasks fa-2x text-primary me-3"></i>
                                <div>
                                    <h5 class="mb-0">Total de Asignaciones</h5>
                                    <h3 class="mb-0"><?= $total_asignaciones ?></h3>
                                </div>
                            </div>
                        </div>

                        <?php if ($asignaciones && $asignaciones->num_rows > 0): ?>

                            <!-- FILTROS -->
                            <div class="row mb-3 g-2">
                                <!-- Buscador global -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por código asignación, código profesor o curso...">
                                    </div>
                                </div>

                                <!-- Filtro Curso solo de asignaciones -->
                                <div class="col-md-3">
                                    <select class="form-select" id="cursoFilter">
                                        <option value="">Todos los cursos</option>
                                        <?php
                                        $cursos = $conn->query("SELECT DISTINCT c.nombre_curso 
                                            FROM asignaciones a 
                                            JOIN cursos c ON a.curso_id = c.id 
                                            ORDER BY c.nombre_curso ASC");
                                        while ($c = $cursos->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($c['nombre_curso']) ?>">
                                                <?= htmlspecialchars($c['nombre_curso']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- ❌ Filtro Profesor eliminado por solicitud -->

                            </div>

                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center">Código de Asignación</th>
                                            <th class="text-center">Código de Docente</th>
                                            <th class="text-center">Curso</th>
                                            <th class="text-center">Horario</th>
                                            <th class="text-center">Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n = 1;
                                        while ($row = $asignaciones->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $n++ ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['codigo_asignacion']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['codigo_usuario']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars("{$row['dia']} {$row['hora_inicio']} - {$row['hora_fin']}") ?></td>
                                                <td class="text-center">
                                                    <a href="admin_detalle_asignaciones.php?asignacion_id=<?= $row['id'] ?>"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                            <div class="alert alert-warning text-center mb-0">No hay asignaciones registradas.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php
    include '../modals/toast_notificacion.php';
    include '../modals/modal_asignar_docente.php';
    include '../modals/modal_editar_asignacion.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const areaId = "<?= $area_id ?>";
            const cursoId = "<?= $curso_id ?>";
            const horarioId = "<?= $horario_id ?>";

            const modalArea = document.getElementById('modalArea');
            const modalCurso = document.getElementById('modalCurso');
            const modalHorario = document.getElementById('modalHorario');

            // Mostrar modal y precargar si vienen por GET
            if (areaId && cursoId && horarioId) {
                const modal = new bootstrap.Modal(document.getElementById('modalAsignarDocente'));
                modal.show();

                modalArea.value = areaId;
                cargarCursos(() => {
                    modalCurso.value = cursoId;
                    cargarHorarios(() => {
                        modalHorario.value = horarioId;
                    });
                });
            }

            modalArea?.addEventListener('change', () => cargarCursos());
            modalCurso?.addEventListener('change', () => cargarHorarios());

            function cargarCursos(callback) {
                const id = modalArea.value;
                modalCurso.disabled = true;
                modalHorario.disabled = true;
                modalCurso.innerHTML = '<option value="">Cargando cursos...</option>';
                modalHorario.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

                if (!id) return;

                fetch(`../../controllers/get_cursos_por_area.php?area_id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        modalCurso.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                        data.forEach(c => {
                            modalCurso.appendChild(new Option(c.nombre_curso, c.id));
                        });
                        modalCurso.disabled = false;
                        if (callback) callback();
                    });
            }

            function cargarHorarios(callback) {
                const id = modalCurso.value;
                modalHorario.disabled = true;
                modalHorario.innerHTML = '<option value="">Cargando horarios...</option>';

                if (!id) return;

                fetch(`../../controllers/get_horarios.php?curso_id=${id}`)
                    .then(res => res.json())
                    .then(data => {
                        modalHorario.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                        data.forEach(h => {
                            modalHorario.appendChild(new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id));
                        });
                        modalHorario.disabled = false;
                        if (callback) callback();
                    });
            }

            // Guardar Asignación (NUEVO)
            document.getElementById('formAsignarModal')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);

                fetch('../../controllers/insert_asignacion.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modalEl = document.getElementById('modalAsignarDocente');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            modalInstance.hide();

                            modalEl.addEventListener('hidden.bs.modal', () => {
                                showNotification('Asignación completada con éxito.', 'success', 'check');
                                setTimeout(() => {
                                    window.location.href = window.location.pathname;
                                }, 1500);
                            }, {
                                once: true
                            });
                        } else {
                            showNotification(data.message, 'warning', 'exclamation-triangle');
                        }
                    })
                    .catch(err => {
                        console.error("❌ Error al guardar:", err);
                        showNotification('Error de red al guardar asignación.', 'danger', 'times');
                    });
            });

            // Editar Asignación
            document.querySelectorAll('.btnEditarAsignacion').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const areaId = btn.dataset.areaId;
                    const cursoId = btn.dataset.cursoId;
                    const horarioId = btn.dataset.horarioId;
                    const profesorId = btn.dataset.profesorId;

                    document.getElementById('editarAsignacionId').value = id;

                    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarAsignacion'));
                    modalEditar.show();

                    fetch('../../controllers/get_areas.php')
                        .then(res => res.json())
                        .then(areas => {
                            const areaSel = document.getElementById('editarArea');
                            areaSel.innerHTML = '<option value="">Seleccione un área</option>';
                            areas.forEach(a => {
                                const opt = new Option(a.nombre_area, a.id);
                                if (a.id == areaId) opt.selected = true;
                                areaSel.appendChild(opt);
                            });

                            return fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`);
                        })
                        .then(res => res.json())
                        .then(cursos => {
                            const cursoSel = document.getElementById('editarCurso');
                            cursoSel.innerHTML = '<option value="">Seleccione un curso</option>';
                            cursos.forEach(c => {
                                const opt = new Option(c.nombre_curso, c.id);
                                if (c.id == cursoId) opt.selected = true;
                                cursoSel.appendChild(opt);
                            });

                            return fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`);
                        })
                        .then(res => res.json())
                        .then(horarios => {
                            const horarioSel = document.getElementById('editarHorario');
                            horarioSel.innerHTML = '<option value="">Seleccione un horario</option>';
                            horarios.forEach(h => {
                                const opt = new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id);
                                if (h.id == horarioId) opt.selected = true;
                                horarioSel.appendChild(opt);
                            });

                            return fetch(`../../controllers/get_profesores.php`);
                        })
                        .then(res => res.json())
                        .then(profesores => {
                            const profSel = document.getElementById('editarProfesor');
                            profSel.innerHTML = '<option value="">Seleccione un profesor</option>';
                            profesores.forEach(p => {
                                const opt = new Option(`${p.nombre} ${p.apellidos}`, p.id);
                                if (p.id == profesorId) opt.selected = true;
                                profSel.appendChild(opt);
                            });
                        });
                });
            });

            document.getElementById('editarArea').addEventListener('change', function() {
                const id = this.value;
                const cursoSel = document.getElementById('editarCurso');
                const horarioSel = document.getElementById('editarHorario');

                cursoSel.disabled = true;
                horarioSel.disabled = true;

                cursoSel.innerHTML = '<option value="">Cargando cursos...</option>';
                horarioSel.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

                fetch(`../../controllers/get_cursos_por_area.php?area_id=${id}`)
                    .then(res => res.json())
                    .then(cursos => {
                        cursoSel.innerHTML = '<option value="">Seleccione un curso</option>';
                        cursos.forEach(c => {
                            cursoSel.appendChild(new Option(c.nombre_curso, c.id));
                        });
                        cursoSel.disabled = false;
                    });
            });

            document.getElementById('editarCurso').addEventListener('change', function() {
                const id = this.value;
                const horarioSel = document.getElementById('editarHorario');

                horarioSel.disabled = true;
                horarioSel.innerHTML = '<option value="">Cargando horarios...</option>';

                fetch(`../../controllers/get_horarios.php?curso_id=${id}`)
                    .then(res => res.json())
                    .then(horarios => {
                        horarioSel.innerHTML = '<option value="">Seleccione un horario</option>';
                        horarios.forEach(h => {
                            horarioSel.appendChild(new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id));
                        });
                        horarioSel.disabled = false;
                    });
            });

            document.getElementById('formEditarAsignacion').addEventListener('submit', function(e) {
                e.preventDefault();
                const data = new FormData(this);

                fetch('../../controllers/update_asignacion.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarAsignacion'));
                            modal.hide();
                            showNotification('Asignación actualizada con éxito', 'success', 'check');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showNotification(data.message, 'danger', 'exclamation-triangle');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        showNotification('Error de red o del servidor', 'danger', 'server');
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






            // ------------------ FILTRO TABLA ------------------
            const searchInput = document.getElementById('searchInput');
            const cursoFilter = document.getElementById('cursoFilter');
            const tablaFilas = document.querySelectorAll('tbody tr');

            function filtrarTabla() {
                const texto = searchInput.value.toLowerCase();
                const curso = cursoFilter.value.toLowerCase();

                tablaFilas.forEach(fila => {
                    const columnas = fila.querySelectorAll('td');
                    const codAsignacion = columnas[1].innerText.toLowerCase();
                    const codProfesor = columnas[2].innerText.toLowerCase();
                    const cursoCol = columnas[3].innerText.toLowerCase();

                    const coincideTexto =
                        codAsignacion.includes(texto) ||
                        codProfesor.includes(texto) ||
                        cursoCol.includes(texto);

                    const coincideCurso = curso === '' || cursoCol.includes(curso);

                    fila.style.display = (coincideTexto && coincideCurso) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filtrarTabla);
            cursoFilter.addEventListener('change', filtrarTabla);










        });
    </script>



</body>

</html>