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
            <!-- <main class="col-md-7 col-lg-8 px-5"> -->

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
                                p.nombre AS nombre_profesor, p.apellidos,
                                (
                                    SELECT COUNT(*) 
                                    FROM matriculas m 
                                    WHERE m.curso_id = a.curso_id AND m.horario_id = a.horario_id
                                ) AS cantidad_alumnos
                                FROM asignaciones a
                                JOIN cursos c ON a.curso_id = c.id
                                JOIN horarios h ON a.horario_id = h.id
                                JOIN profesores p ON a.profesor_id = p.id
                                ORDER BY c.nombre_curso ASC, h.dia ASC
                                ";


                        $asignaciones = $conn->query($query);
                        // Calcula total
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
                                <!-- Buscar por curso o profesor -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar por curso,codigo o profesor...">
                                    </div>
                                </div>

                                <!-- Filtro Curso -->
                                <div class="col-md-3">
                                    <select class="form-select" id="cursoFilter">
                                        <option value="">Todos los cursos</option>
                                        <?php
                                        // Opcional: rellena con cursos únicos de tu consulta
                                        $cursos = $conn->query("SELECT DISTINCT nombre_curso FROM cursos ORDER BY nombre_curso ASC");
                                        while ($c = $cursos->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($c['nombre_curso']) ?>">
                                                <?= htmlspecialchars($c['nombre_curso']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Filtro Profesor -->
                                <div class="col-md-3">
                                    <select class="form-select" id="profesorFilter">
                                        <option value="">Todos los profesores</option>
                                        <?php
                                        // Opcional: rellena con profesores únicos
                                        $profes = $conn->query("SELECT DISTINCT nombre FROM profesores ORDER BY nombre ASC");
                                        while ($p = $profes->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($p['nombre']) ?>">
                                                <?= htmlspecialchars($p['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                            </div>




                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">


                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-center">Código de Asignación</th>
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
                                                <td class="text-center"><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars("{$row['dia']} {$row['hora_inicio']} - {$row['hora_fin']}") ?></td>


                                                <td class="text-center">
                                                    <a href="admin_detalle_asignaciones.php?asignacion_id=<?= $row['id'] ?>"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>


                                                    <!-- <button class="btn btn-outline-primary btn-sm btnEditarAsignacion "
                                                        data-id="<?= htmlspecialchars($row['id']) ?>"
                                                        data-area-id="<?= htmlspecialchars($row['area_id'] ?? '') ?>"
                                                        data-curso-id="<?= htmlspecialchars($row['curso_id']) ?>"
                                                        data-horario-id="<?= htmlspecialchars($row['horario_id']) ?>"
                                                        data-profesor-id="<?= htmlspecialchars($row['profesor_id']) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <button class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirmDelete(<?= $row['id'] ?>)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button> -->

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

            // ------------------ MODAL NUEVA ASIGNACIÓN ------------------

            document.getElementById('modalArea')?.addEventListener('change', function() {
                const areaId = this.value;
                const cursoSelect = document.getElementById('modalCurso');
                const horarioSelect = document.getElementById('modalHorario');

                cursoSelect.disabled = true;
                horarioSelect.disabled = true;
                cursoSelect.innerHTML = '<option value="">Cargando cursos...</option>';
                horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

                if (!areaId) return;

                fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                    .then(res => res.json())
                    .then(data => {
                        cursoSelect.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                        data.forEach(curso => {
                            const opt = document.createElement('option');
                            opt.value = curso.id;
                            opt.textContent = curso.nombre_curso;
                            cursoSelect.appendChild(opt);
                        });
                        cursoSelect.disabled = false;
                    });
            });

            document.getElementById('modalCurso')?.addEventListener('change', function() {
                const cursoId = this.value;
                const horarioSelect = document.getElementById('modalHorario');
                horarioSelect.disabled = true;
                horarioSelect.innerHTML = '<option value="">Cargando horarios...</option>';

                if (!cursoId) return;

                fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                    .then(res => res.json())
                    .then(data => {
                        horarioSelect.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                        data.forEach(h => {
                            const opt = document.createElement('option');
                            opt.value = h.id;
                            opt.textContent = `${h.dia} ${h.hora_inicio} - ${h.hora_fin}`;
                            horarioSelect.appendChild(opt);
                        });
                        horarioSelect.disabled = false;
                    });
            });

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
                                document.querySelector('.modal-backdrop')?.remove();
                                showNotification('Asignación completada con éxito.', 'success', 'check');
                                setTimeout(() => location.reload(), 2000);
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

            // ------------------ MODAL EDITAR ASIGNACIÓN ------------------

            document.querySelectorAll('.btnEditarAsignacion').forEach(boton => {
                boton.addEventListener('click', () => {
                    const id = boton.dataset.id;
                    const areaId = boton.dataset.areaId;
                    const cursoId = boton.dataset.cursoId;
                    const horarioId = boton.dataset.horarioId;
                    const profesorId = boton.dataset.profesorId;

                    document.getElementById('editarAsignacionId').value = id;

                    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarAsignacion'));
                    modalEditar.show();

                    fetch('../../controllers/get_areas.php')
                        .then(res => res.json())
                        .then(areas => {
                            const selectArea = document.getElementById('editarArea');
                            selectArea.innerHTML = '<option value="">Seleccione un área</option>';
                            areas.forEach(a => {
                                const opt = new Option(a.nombre_area, a.id);
                                if (a.id == areaId) opt.selected = true;
                                selectArea.appendChild(opt);
                            });
                            return fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`);
                        })
                        .then(res => res.json())
                        .then(cursos => {
                            const selectCurso = document.getElementById('editarCurso');
                            selectCurso.innerHTML = '<option value="">Seleccione un curso</option>';
                            cursos.forEach(c => {
                                const opt = new Option(c.nombre_curso, c.id);
                                if (c.id == cursoId) opt.selected = true;
                                selectCurso.appendChild(opt);
                            });
                            return fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`);
                        })
                        .then(res => res.json())
                        .then(horarios => {
                            const selectHorario = document.getElementById('editarHorario');
                            selectHorario.innerHTML = '<option value="">Seleccione un horario</option>';
                            horarios.forEach(h => {
                                const opt = new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id);
                                if (h.id == horarioId) opt.selected = true;
                                selectHorario.appendChild(opt);
                            });
                            return fetch(`../../controllers/get_profesores.php`);
                        })
                        .then(res => res.json())
                        .then(profesores => {
                            const selectProfesor = document.getElementById('editarProfesor');
                            selectProfesor.innerHTML = '<option value="">Seleccione un profesor</option>';
                            profesores.forEach(p => {
                                const opt = new Option(`${p.nombre} ${p.apellidos}`, p.id);
                                if (p.id == profesorId) opt.selected = true;
                                selectProfesor.appendChild(opt);
                            });
                        });
                });
            });

            document.getElementById('editarArea').addEventListener('change', function() {
                const areaId = this.value;
                const cursoSelect = document.getElementById('editarCurso');
                const horarioSelect = document.getElementById('editarHorario');

                cursoSelect.disabled = true;
                horarioSelect.disabled = true;
                cursoSelect.innerHTML = '<option value="">Cargando cursos...</option>';
                horarioSelect.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

                fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                    .then(res => res.json())
                    .then(cursos => {
                        cursoSelect.innerHTML = '<option value="">Seleccione un curso</option>';
                        cursos.forEach(curso => {
                            const opt = new Option(curso.nombre_curso, curso.id);
                            cursoSelect.appendChild(opt);
                        });
                        cursoSelect.disabled = false;
                    });
            });

            document.getElementById('editarCurso').addEventListener('change', function() {
                const cursoId = this.value;
                const horarioSelect = document.getElementById('editarHorario');
                horarioSelect.disabled = true;
                horarioSelect.innerHTML = '<option value="">Cargando horarios...</option>';

                fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                    .then(res => res.json())
                    .then(horarios => {
                        horarioSelect.innerHTML = '<option value="">Seleccione un horario</option>';
                        horarios.forEach(h => {
                            const opt = new Option(`${h.dia} ${h.hora_inicio} - ${h.hora_fin}`, h.id);
                            horarioSelect.appendChild(opt);
                        });
                        horarioSelect.disabled = false;
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

            document.getElementById('modalAsignarDocente')?.addEventListener('hidden.bs.modal', () => {
                const toast = bootstrap.Toast.getInstance(document.getElementById('liveToast'));
                toast?.hide();
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

            // ------------------ FILTROS DE TABLA ------------------

            const searchInput = document.getElementById('searchInput');
            const cursoFilter = document.getElementById('cursoFilter');
            const profesorFilter = document.getElementById('profesorFilter');
            const tablaFilas = document.querySelectorAll('tbody tr');

            function filtrarTabla() {
                const texto = searchInput.value.toLowerCase();
                const curso = cursoFilter.value.toLowerCase();
                const profesor = profesorFilter.value.toLowerCase();

                tablaFilas.forEach(fila => {
                    const columnas = fila.querySelectorAll('td');
                    const codigo = columnas[1].innerText.toLowerCase();
                    const cursoCol = columnas[2].innerText.toLowerCase();
                    const horario = columnas[3].innerText.toLowerCase();
                    const profesorCol = columnas[4].innerText.toLowerCase();

                    const coincideTexto =
                        codigo.includes(texto) ||
                        cursoCol.includes(texto) ||
                        horario.includes(texto) ||
                        profesorCol.includes(texto);

                    const coincideCurso = curso === '' || cursoCol.includes(curso);
                    const coincideProfesor = profesor === '' || profesorCol.includes(profesor);

                    if (coincideTexto && coincideCurso && coincideProfesor) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filtrarTabla);
            cursoFilter.addEventListener('change', filtrarTabla);
            profesorFilter.addEventListener('change', filtrarTabla);

        });
    </script>



</body>

</html>