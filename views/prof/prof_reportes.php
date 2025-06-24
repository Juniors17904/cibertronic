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

            <!-- Contenido principal -->
            <main class="col-md-7 col-lg-8 px-5 py-4">
                <h4 class="mb-4 text-primary fw-bold">Panel de Reportes</h4>

                <div class="row g-4">
                    <!-- Tarjeta: Reporte de Asistencias -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-clipboard-check fs-1 text-success"></i>
                                </div>
                                <h5 class="card-title mb-2">Reporte de Asistencias</h5>
                                <p class="card-text small">Consulta y exporta el historial de asistencias por curso y alumno.</p>
                                <a href="prof_reportesAs.php" class="btn btn-outline-success mt-2">Ver Reporte</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Reporte de Notas -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow h-100">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-journal-text fs-1 text-primary"></i>
                                </div>
                                <h5 class="card-title mb-2">Reporte de Notas</h5>
                                <p class="card-text small">Consulta calificaciones, promedios y resultados finales por curso.</p>
                                <a href="prof_reportesNt.php" class="btn btn-outline-primary mt-2">Ver Reporte</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>