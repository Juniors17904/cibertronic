<?php
require_once '../controllers/auth_check.php';
require_once '../controllers/get_admin_data.php';

$admin = getAdminData($conn, $_SESSION['user_id']);

if (!$admin) {
    die("Administrador no encontrado.");
}
include 'header.php';
?>

<body>
    <?php include 'cabecera.php' ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php' ?>

            <!-- Contenido principal -->
            <main class="col-md-7 col-lg-8 px-5">
                <div class="perfil-top-barra">
                    <h1 class="text-center mb-0 text-white">Mi Perfil</h1>
                </div>
                <div class="profile-info">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <img src="../assets/images/perfil.jpg" alt="Foto de perfil" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['nombre']) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['apellidos']) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['telefono']) ?>" readonly>
                                </div>
                                <!-- Nuevos campos añadidos -->
                                <div class="mb-3">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($admin['correo']) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($admin['rol'])) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars(ucfirst($admin['estado'])) ?>" readonly>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="perfil-bajo-barra text-center">
                    <!-- <a href="admin_profile_edit.php" class="btn btn-light">Editar Perfil</a> -->
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>