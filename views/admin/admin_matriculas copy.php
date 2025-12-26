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

            <main class="col-md-8 col-lg-9 px-5 py-4">



                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Alumnos Matriculados</h4>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaMatricula">
                            <i class="fas fa-plus-circle me-1"></i> Nueva Matrícula
                        </button>
                    </div>

                    <div class="card-body">


                        <?php
                        $sql = "SELECT
                            m.id AS matricula_id,
                            m.codigo_matricula AS codigo_matricula,
                            a.codigo_usuario AS alumno_id,
                            a.nombre,
                            a.apellidos,
                            c.nombre_curso,
                            m.fecha_matricula
                            FROM matriculas m
                            JOIN alumnos a ON m.alumno_id = a.id
                            JOIN cursos c ON m.curso_id = c.id
                            ORDER BY a.apellidos ASC
                        ";
                        $matriculas = $conn->query($sql);

                        // Contar TOTAL
                        $total_matriculas = ($matriculas) ? $matriculas->num_rows : 0;


                        ?>


                        <!-- Tarjeta resumen -->
                        <div class="card border-primary shadow mb-4 w-50 text-center">

                            <div class="card-body d-flex align-items-center">
                                <i class="fas fa-users fa-2x text-primary me-3"></i>
                                <div>
                                    <h5 class="mb-0">Total de Matrículas Registradas</h5>
                                    <h3 class="mb-0"><?= $total_matriculas ?></h3>
                                </div>
                            </div>
                        </div>



                        <?php if ($matriculas && $matriculas->num_rows > 0): ?>

                            <!-- FILTROS -->
                            <div class="row mb-3 g-2">
                                <!-- Buscador global -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="searchMatricula" class="form-control" placeholder="Buscar alumno, área, curso o codigo de asignación ...">
                                    </div>
                                </div>

                                <!-- Filtro Área -->
                                <div class="col-md-3">
                                    <select id="areaFilter" class="form-select">
                                        <option value="">Todas las áreas</option>
                                        <?php
                                        $areas = $conn->query("SELECT DISTINCT nombre_area FROM areas ORDER BY nombre_area ASC");
                                        while ($ar = $areas->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($ar['nombre_area']) ?>"><?= htmlspecialchars($ar['nombre_area']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Filtro Curso -->
                                <div class="col-md-3">
                                    <select id="cursoFilter" class="form-select">
                                        <option value="">Todos los cursos</option>
                                        <?php
                                        $cursos = $conn->query("SELECT DISTINCT nombre_curso FROM cursos ORDER BY nombre_curso ASC");
                                        while ($c = $cursos->fetch_assoc()):
                                        ?>
                                            <option value="<?= htmlspecialchars($c['nombre_curso']) ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- TABLA -->
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">


                                <table class="table table-striped table-hover text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Codigo de Matricula</th>
                                            <th>Código Alumno</th>
                                            <th>Nombres y Apellidos</th>
                                            <th>Curso</th>
                                            <th>Fecha Matrícula</th>
                                            <th>Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n = 1;
                                        while ($row = $matriculas->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $n++ ?></td>
                                                <td><?= htmlspecialchars($row['codigo_matricula']) ?></td>
                                                <td><?= htmlspecialchars($row['alumno_id']) ?></td>
                                                <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']) ?></td>
                                                <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                                <td><?= htmlspecialchars($row['fecha_matricula']) ?></td>
                                                <td>
                                                    <a href="admin_matriculas_detalles.php?matricula_id=<?= $row['matricula_id'] ?>" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>




                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>



                            </div>

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
        // ✅ Cuando cambia el área
        document.getElementById('area')?.addEventListener('change', function() {
            const areaId = this.value;
            const curso = document.getElementById('curso');
            const horario = document.getElementById('horario');

            console.log('🔵 Área seleccionada:', areaId); // 👀

            curso.disabled = true;
            horario.disabled = true;
            curso.innerHTML = '<option value="">Cargando cursos...</option>';
            horario.innerHTML = '<option value="">-- Seleccione un curso primero --</option>';

            if (!areaId) return;

            fetch(`../../controllers/get_cursos_por_area.php?area_id=${areaId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('✅ Cursos recibidos del área:', data); // 👀

                    curso.innerHTML = '<option value="">-- Seleccione un curso --</option>';
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.nombre_curso;
                        curso.appendChild(opt);
                    });
                    console.log('📌 Opciones finales en <select id="curso">:', curso.innerHTML); // 👀
                    curso.disabled = false;
                });
        });

        // ✅ Cuando cambia el curso
        document.getElementById('curso')?.addEventListener('change', function() {
            const cursoId = this.value;
            const horario = document.getElementById('horario');

            console.log('🔵 Curso seleccionado:', cursoId); // 👀

            horario.disabled = true;
            horario.innerHTML = '<option value="">Cargando horarios...</option>';

            if (!cursoId) return;

            fetch(`../../controllers/get_horarios.php?curso_id=${cursoId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('✅ Horarios recibidos:', data); // 👀

                    horario.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                    data.forEach(h => {
                        const opt = document.createElement('option');
                        opt.value = h.id;
                        opt.textContent = `${h.dia} ${h.hora_inicio} - ${h.hora_fin}`;
                        horario.appendChild(opt);
                    });
                    console.log('📌 Opciones finales en <select id="horario">:', horario.innerHTML); // 👀
                    horario.disabled = false;
                });
        });

        // ✅ Envío del formulario
        document.getElementById('formMatriculaModal')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const data = new FormData(form);

            console.log('🚀 Datos del formulario a enviar:', Object.fromEntries(data.entries())); // 👀

            fetch('../../controllers/insert_matricula.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(data => {
                    console.log('✅ Respuesta al guardar matrícula:', data); // 👀

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

        const searchMatricula = document.getElementById('searchMatricula');
        const areaFilter = document.getElementById('areaFilter');
        const cursoFilter = document.getElementById('cursoFilter');
        const filas = document.querySelectorAll('tbody tr');



        searchMatricula.addEventListener('input', filtrarMatriculas);
        areaFilter.addEventListener('change', filtrarMatriculas);
        cursoFilter.addEventListener('change', filtrarMatriculas);




        // ✅ Cuando cambia el ÁREA del header: traer SOLO cursos de esa área
        areaFilter.addEventListener('change', () => {
            const nombreArea = areaFilter.value;
            console.log('📌 [Área seleccionada]:', nombreArea);

            if (nombreArea === '') {
                // 🔵 Si el usuario vuelve a "Todas las áreas", restaurar todos los cursos
                cursoFilter.innerHTML = `<option value="">Todos los cursos</option>`;
                <?php
                $cursos = $conn->query("SELECT DISTINCT nombre_curso FROM cursos ORDER BY nombre_curso ASC");
                while ($c = $cursos->fetch_assoc()):
                ?>
                    cursoFilter.innerHTML += `<option value="<?= htmlspecialchars($c['nombre_curso']) ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>`;
                <?php endwhile; ?>
                console.log('✅ [Cursos restaurados]: Todos los cursos disponibles.');
            } else {
                // 🔵 Si elige un área específica → llamar al controlador PHP que devuelve cursos de esa área
                fetch(`../../controllers/get_cursos_por_area_nombre.php?nombre_area=${encodeURIComponent(nombreArea)}`)
                    .then(res => res.json())
                    .then(data => {
                        cursoFilter.innerHTML = `<option value="">Todos los cursos</option>`;
                        data.forEach(curso => {
                            cursoFilter.innerHTML += `<option value="${curso.nombre_curso}">${curso.nombre_curso}</option>`;
                        });
                        console.log('✅ [Cursos filtrados]:', data);
                    })
                    .catch(err => console.error('❌ Error al obtener cursos:', err));
            }

            filtrarMatriculas(); // 🔄 Filtrar tabla cada vez que cambia
        });

        // ✅ Cuando cambia el filtro de curso: solo filtra tabla
        cursoFilter.addEventListener('change', filtrarMatriculas);
        searchMatricula.addEventListener('input', filtrarMatriculas);

        // ✅ Filtra las filas de la tabla por texto, área y curso

        function filtrarMatriculas() {
            const texto = searchMatricula.value.toLowerCase();
            const area = areaFilter.value.toLowerCase();
            const curso = cursoFilter.value.toLowerCase();

            filas.forEach(fila => {
                const columnas = fila.querySelectorAll('td');
                const alumno = columnas[1].innerText.toLowerCase();
                const areaCol = columnas[2].innerText.toLowerCase();
                const cursoCol = columnas[3].innerText.toLowerCase();
                const codigoAsignacion = columnas[4].innerText.toLowerCase(); // ✅

                const coincideTexto =
                    alumno.includes(texto) ||
                    areaCol.includes(texto) ||
                    cursoCol.includes(texto) ||
                    codigoAsignacion.includes(texto); // ✅

                const coincideArea = area === '' || areaCol.includes(area);
                const coincideCurso = curso === '' || cursoCol.includes(curso);

                fila.style.display = coincideTexto && coincideArea && coincideCurso ? '' : 'none';
            });
        }
    </script>



</body>

</html>