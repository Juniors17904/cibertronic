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
            <main class="col-md-6 col-lg-7 px-5 py-4">

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>