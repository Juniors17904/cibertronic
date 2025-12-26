<?php
require_once '../../controllers/auth_check.php';
require_once '../../controllers/get_prof_data.php';

$prof = getProfData($conn, $_SESSION['user_id']);
if (!$prof) {
    die("Profesor no encontrado.");
}
include '../header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>

            <!-- <main class="col-md-7 col-lg-8 px-5 py-4"> -->
            <main class="col-md-8 col-lg-9 px-2 px-md-5 py-4">

                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Reporte de Asistencias</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form id="formFiltros" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Curso</label>
                                    <select class="form-select" id="filtroCurso">
                                        <option value="">-- Todos --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alumno</label>
                                    <select class="form-select" id="filtroAlumno">
                                        <option value="">-- Todos los Alumnos --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rango de Fechas</label>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="date" class="form-control" id="fechaInicio">
                                        </div>
                                        <div class="col">
                                            <input type="date" class="form-control" id="fechaFin">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3 mb-3">
                                <div class="col-md-3">
                                    <button class="btn btn-outline-primary w-100" type="submit">Filtrar</button>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="buscador" placeholder="Buscar...">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-outline-success w-100" id="btnExportar" type="button">Exportar a Excel</button>
                                </div>
                            </div>
                        </form>

                        <!-- Resumen -->
                        <div class="row mb-3" id="resumenEstados"></div>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Curso</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Alumno</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>

                                <tbody id="tablaAsistencias">
                                    <tr>
                                        <td colspan="5">Cargando datos...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small ps-2" id="contadorRegistros">Mostrando 0 de 0 registros</div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 me-2" id="paginadorTabla"></ul>
                            </nav>
                        </div>

                    </div>
                </div>
            </main>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabla = document.getElementById('tablaAsistencias');
            const cursoSelect = document.getElementById('filtroCurso');
            const alumnoSelect = document.getElementById('filtroAlumno');
            const resumenDiv = document.getElementById('resumenEstados');
            const buscador = document.getElementById('buscador');
            const fechaInicio = document.getElementById('fechaInicio');
            const fechaFin = document.getElementById('fechaFin');
            const contadorRegistros = document.getElementById('contadorRegistros');
            const paginador = document.getElementById('paginadorTabla');
            let datos = [],
                paginaActual = 1,
                filasPorPagina = 10;

            fetch(`../../controllers/get_historial_asistencias.php?profesor_id=<?= $prof['id'] ?>`)
                .then(res => res.json())
                .then(data => {
                    datos = data;
                    llenarFiltros(data);
                    aplicarFiltros();
                });

            function llenarFiltros(data) {
                const cursos = [...new Set(data.map(d => d.nombre_curso))];
                const alumnos = [...new Set(data.map(d => `${d.nombre_alumno} ${d.apellidos_alumno}`))];
                cursos.forEach(c => cursoSelect.innerHTML += `<option value="${c}">${c}</option>`);
                alumnos.forEach(a => alumnoSelect.innerHTML += `<option value="${a}">${a}</option>`);
            }

            function aplicarFiltros() {
                const curso = cursoSelect.value.toLowerCase().trim();
                const alumno = alumnoSelect.value.toLowerCase().trim();
                const texto = buscador.value.trim().toLowerCase();
                const fInicio = fechaInicio.value;
                const fFin = fechaFin.value;

                let resumen = {
                    Presente: 0,
                    Ausente: 0,
                    Justificado: 0
                };

                let filtrados = datos.filter(d => {
                    const nombreCompleto = `${d.nombre_alumno} ${d.apellidos_alumno}`.toLowerCase().trim();
                    const fechaOK = (!fInicio || d.fecha >= fInicio) && (!fFin || d.fecha <= fFin);
                    const cursoOK = !curso || d.nombre_curso.toLowerCase().trim() === curso;
                    const alumnoOK = !alumno || nombreCompleto === alumno;
                    const textoOK = !texto || `${nombreCompleto} ${d.nombre_curso} ${d.fecha} ${d.codigo_asignacion}`.includes(texto);
                    return fechaOK && cursoOK && alumnoOK && textoOK;
                });

                // Calcular resumen ANTES de paginar
                filtrados.forEach(d => {
                    if (resumen[d.estado] !== undefined) resumen[d.estado]++;
                });

                const totalPaginas = Math.ceil(filtrados.length / filasPorPagina);
                paginaActual = Math.min(paginaActual, totalPaginas || 1);
                const inicio = (paginaActual - 1) * filasPorPagina;
                const fin = inicio + filasPorPagina;
                const datosPagina = filtrados.slice(inicio, fin);

                tabla.innerHTML = '';
                if (datosPagina.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="6">Sin resultados.</td></tr>';
                    resumenDiv.innerHTML = '';
                    paginador.innerHTML = '';
                    contadorRegistros.textContent = '';
                    return;
                }

                datosPagina.forEach(d => {
                    const estadoColor = d.estado === 'Presente' ? 'success' : d.estado === 'Ausente' ? 'danger' : 'warning';
                    tabla.innerHTML += `
                        <tr>
                            <td>${d.codigo_asignacion}</td>
                            <td>${d.nombre_curso}</td>
                            <td>${d.fecha}</td>
                            <td>${d.dia} ${d.hora_inicio} - ${d.hora_fin}</td>
                            <td>${d.nombre_alumno} ${d.apellidos_alumno}</td>
                            <td><span class="badge bg-${estadoColor}">${d.estado}</span></td>
                        </tr>`;
                });

                resumenDiv.innerHTML = `
                    <div class="col"><div class="alert alert-success p-2">Presentes: ${resumen.Presente}</div></div>
                    <div class="col"><div class="alert alert-danger p-2">Ausentes: ${resumen.Ausente}</div></div>
                    <div class="col"><div class="alert alert-warning p-2">Justificados: ${resumen.Justificado}</div></div>`;

                contadorRegistros.textContent = `Mostrando ${inicio + 1} a ${Math.min(fin, filtrados.length)} de ${filtrados.length} registros`;
                renderizarPaginador(totalPaginas);
            }

            function renderizarPaginador(totalPaginas) {
                paginador.innerHTML = '';
                const crearItem = (texto, activo, deshabilitado, onclick) => {
                    const li = document.createElement('li');
                    li.className = `page-item${activo ? ' active' : ''}${deshabilitado ? ' disabled' : ''}`;
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.innerText = texto;
                    a.onclick = (e) => {
                        e.preventDefault();
                        if (onclick) onclick();
                    };
                    li.appendChild(a);
                    return li;
                };
                paginador.appendChild(crearItem('‹', false, paginaActual === 1, () => {
                    paginaActual--;
                    aplicarFiltros();
                }));
                paginador.appendChild(crearItem('›', false, paginaActual === totalPaginas, () => {
                    paginaActual++;
                    aplicarFiltros();
                }));
            }

            document.getElementById('formFiltros').addEventListener('submit', e => {
                e.preventDefault();
                paginaActual = 1;
                aplicarFiltros();
            });
            cursoSelect.addEventListener('change', () => {
                paginaActual = 1;
                aplicarFiltros();
            });
            alumnoSelect.addEventListener('change', () => {
                paginaActual = 1;
                aplicarFiltros();
            });
            fechaInicio.addEventListener('change', () => {
                paginaActual = 1;
                aplicarFiltros();
            });
            fechaFin.addEventListener('change', () => {
                paginaActual = 1;
                aplicarFiltros();
            });
            buscador.addEventListener('input', () => {
                paginaActual = 1;
                aplicarFiltros();
            });

            document.getElementById('btnExportar').addEventListener('click', () => {
                const filas = document.querySelectorAll('#tablaAsistencias tr');
                if (filas.length === 0 || filas[0].children.length !== 6) {
                    alert("No hay datos para exportar.");
                    return;
                }

                const data = [
                    ["Código", "Curso", "Fecha", "Horario", "Alumno", "Estado"]

                ];
                filas.forEach(tr => {
                    const tds = tr.querySelectorAll('td');
                    if (tds.length === 6) {
                        data.push([
                            tds[0].innerText,
                            tds[1].innerText,
                            tds[2].innerText,
                            tds[3].innerText,
                            tds[4].innerText,
                            tds[5].innerText
                        ]);
                    }
                });

                const ws = XLSX.utils.aoa_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Asistencias");
                XLSX.writeFile(wb, "reporte_asistencias.xlsx");
            });
        });
    </script>






</body>

</html>