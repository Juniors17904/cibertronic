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
            <main class="col-md-8 col-lg-9 px-5 py-4">

                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Reporte de Notas</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtros -->
                        <form id="formFiltros" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Curso</label>
                                    <select class="form-select" id="filtroCurso">
                                        <option value="">-- Todos --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alumno</label>
                                    <select class="form-select" id="filtroAlumno">
                                        <option value="">-- Todos --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label class="form-label">Resultado</label>
                                    <select class="form-select" id="filtroResultado">
                                        <option value="">-- Todos --</option>
                                        <option value="Aprobado">Aprobado</option>
                                        <option value="Desaprobado">Desaprobado</option>
                                        <option value="Pendiente">Pendiente</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-4 d-flex align-items-end">
                                    <button class="btn btn-outline-primary w-100" type="submit">Filtrar</button>
                                </div>
                                <div class="col-md-7 mt-2">
                                    <input type="text" class="form-control" id="buscador" placeholder="Buscar...">
                                </div>
                                <div class="col-md-5 mt-2">
                                    <button class="btn btn-outline-success w-100" id="btnExportar" type="button">Exportar a Excel</button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código Asignación</th>
                                        <th>Curso</th>
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
                                        <td colspan="8">Cargando datos...</td>
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
            const tabla = document.getElementById('tablaNotas');
            const cursoSelect = document.getElementById('filtroCurso');
            const alumnoSelect = document.getElementById('filtroAlumno');
            const resultadoSelect = document.getElementById('filtroResultado');
            const buscador = document.getElementById('buscador');
            const contadorRegistros = document.getElementById('contadorRegistros');
            const paginador = document.getElementById('paginadorTabla');
            let datos = [],
                paginaActual = 1,
                filasPorPagina = 10;

            // ✅ Formato de nota individual (puede tener 1 decimal)
            function formatearNota(nota) {
                if (nota === '' || isNaN(nota)) return '--';
                const val = parseFloat(nota);
                return val % 1 === 0 ? val.toFixed(0) : val.toFixed(1);
            }

            // ✅ Promedio redondeado sin decimales
            function formatearPromedio(n1, n2, n3) {
                const promedio = Math.round((n1 + n2 + n3) / 3);
                return isNaN(promedio) ? '--' : promedio.toString();
            }

            fetch(`../../controllers/get_historial_notas.php?profesor_id=<?= $prof['id'] ?>`)
                .then(res => res.json())
                .then(data => {
                    datos = data;
                    llenarFiltros(data);
                    aplicarFiltros();
                });

            function llenarFiltros(data) {
                const cursos = [...new Set(data.map(d => d.asignatura))];
                const alumnos = [...new Set(data.map(d => `${d.nombre_alumno} ${d.apellidos_alumno}`))];
                cursos.forEach(c => cursoSelect.innerHTML += `<option value="${c}">${c}</option>`);
                alumnos.forEach(a => alumnoSelect.innerHTML += `<option value="${a}">${a}</option>`);
            }

            function aplicarFiltros() {
                const curso = cursoSelect.value.toLowerCase().trim();
                const alumno = alumnoSelect.value.toLowerCase().trim();
                const texto = buscador.value.trim().toLowerCase();
                const resultadoFiltro = resultadoSelect.value.toLowerCase().trim();

                let filtrados = datos.filter(d => {
                    const nombreCompleto = `${d.nombre_alumno} ${d.apellidos_alumno}`.toLowerCase().trim();
                    const cursoOK = !curso || d.asignatura.toLowerCase().trim() === curso;
                    const alumnoOK = !alumno || nombreCompleto === alumno;
                    const textoOK = !texto || `${nombreCompleto} ${d.asignatura} ${d.codigo_asignacion}`.includes(texto);

                    const n1 = parseFloat(d.nota_01) || 0;
                    const n2 = parseFloat(d.nota_02) || 0;
                    const n3 = parseFloat(d.nota_03) || 0;
                    const promedio = Math.round((n1 + n2 + n3) / 3);
                    let resultado = '--';
                    if (promedio === 0) resultado = 'pendiente';
                    else resultado = promedio >= 11 ? 'aprobado' : 'Desaprobado';
                    const resultadoOK = !resultadoFiltro || resultado.toLowerCase() === resultadoFiltro;

                    return cursoOK && alumnoOK && textoOK && resultadoOK;
                });

                const totalPaginas = Math.ceil(filtrados.length / filasPorPagina);
                paginaActual = Math.min(paginaActual, totalPaginas || 1);
                const inicio = (paginaActual - 1) * filasPorPagina;
                const fin = inicio + filasPorPagina;
                const datosPagina = filtrados.slice(inicio, fin);

                tabla.innerHTML = '';
                if (datosPagina.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="8">Sin resultados.</td></tr>';
                    paginador.innerHTML = '';
                    contadorRegistros.textContent = '';
                    return;
                }

                datosPagina.forEach(d => {
                    const n1 = parseFloat(d.nota_01) || 0;
                    const n2 = parseFloat(d.nota_02) || 0;
                    const n3 = parseFloat(d.nota_03) || 0;
                    const promedio = Math.round((n1 + n2 + n3) / 3);
                    let resultado = '--',
                        color = 'secondary';
                    if (promedio === 0) {
                        resultado = 'Pendiente';
                        color = 'warning';
                    } else if (promedio >= 11) {
                        resultado = 'Aprobado';
                        color = 'success';
                    } else {
                        resultado = 'Desaprobado';
                        color = 'danger';
                    }

                    tabla.innerHTML += `
                <tr>
                    <td>${d.codigo_asignacion}</td>
                    <td>${d.asignatura}</td>
                    <td>${d.nombre_alumno} ${d.apellidos_alumno}</td>
                    <td>${formatearNota(d.nota_01)}</td>
                    <td>${formatearNota(d.nota_02)}</td>
                    <td>${formatearNota(d.nota_03)}</td>
                    <td>${promedio}</td>
                    <td><span class="badge bg-${color}">${resultado}</span></td>
                </tr>`;
                });

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

            cursoSelect.addEventListener('change', aplicarFiltros);
            alumnoSelect.addEventListener('change', aplicarFiltros);
            resultadoSelect.addEventListener('change', aplicarFiltros);
            buscador.addEventListener('input', aplicarFiltros);

            // Exportar a Excel
            document.getElementById('btnExportar').addEventListener('click', () => {
                const curso = cursoSelect.value.toLowerCase().trim();
                const alumno = alumnoSelect.value.toLowerCase().trim();
                const texto = buscador.value.trim().toLowerCase();
                const resultadoFiltro = resultadoSelect.value.toLowerCase().trim();

                let filtrados = datos.filter(d => {
                    const nombreCompleto = `${d.nombre_alumno} ${d.apellidos_alumno}`.toLowerCase().trim();
                    const cursoOK = !curso || d.asignatura.toLowerCase().trim() === curso;
                    const alumnoOK = !alumno || nombreCompleto === alumno;
                    const textoOK = !texto || `${nombreCompleto} ${d.asignatura} ${d.codigo_asignacion}`.includes(texto);

                    const n1 = parseFloat(d.nota_01) || 0;
                    const n2 = parseFloat(d.nota_02) || 0;
                    const n3 = parseFloat(d.nota_03) || 0;
                    const promedio = Math.round((n1 + n2 + n3) / 3);
                    let resultado = '--';
                    if (promedio === 0) resultado = 'Pendiente';
                    else if (promedio >= 11) resultado = 'Aprobado';
                    else resultado = 'Desaprobado';

                    return cursoOK && alumnoOK && textoOK && (!resultadoFiltro || resultado.toLowerCase() === resultadoFiltro);
                });

                if (!filtrados.length) {
                    alert("No hay datos para exportar.");
                    return;
                }

                const data = [
                    ["Código", "Curso", "Alumno", "Nota 1", "Nota 2", "Nota 3", "Promedio", "Resultado"]
                ];
                filtrados.forEach(d => {
                    const n1 = parseFloat(d.nota_01) || 0;
                    const n2 = parseFloat(d.nota_02) || 0;
                    const n3 = parseFloat(d.nota_03) || 0;
                    const promedio = Math.round((n1 + n2 + n3) / 3);
                    let resultado = promedio === 0 ? 'Pendiente' : (promedio >= 11 ? 'Aprobado' : 'Desaprobado');

                    data.push([
                        d.codigo_asignacion,
                        d.asignatura,
                        `${d.nombre_alumno} ${d.apellidos_alumno}`,
                        formatearNota(d.nota_01),
                        formatearNota(d.nota_02),
                        formatearNota(d.nota_03),
                        promedio,
                        resultado
                    ]);
                });

                const ws = XLSX.utils.aoa_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Notas");
                XLSX.writeFile(wb, "reporte_notas.xlsx");
            });
        });
    </script>



</body>

</html>