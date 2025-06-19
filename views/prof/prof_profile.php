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
                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0">Mi Perfil</h2>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="text-center mb-4">
                                    <img src="../../assets/images/perfil.jpg" alt="Foto de perfil" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($prof['nombre']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Apellidos</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($prof['apellidos']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($prof['telefono']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Correo electrónico</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($prof['correo']) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Rol</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($prof['rol'])) ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($prof['estado'])) ?>" readonly>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <!-- <a href="admin_profile_edit.php" class="btn btn-outline-primary">Editar Perfil</a> -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>