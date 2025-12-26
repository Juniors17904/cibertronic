<!-- Modal para Nuevo Usuario -->
<div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="userForm" action="../../controllers/create_user.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="email" required
                                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="passwordField" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="rol" required>
                                <option value="">Seleccionar...</option>
                                <option value="administrador" <?= (isset($_POST['rol']) && $_POST['rol'] === 'administrador') ? 'selected' : '' ?>>Administrador</option>
                                <option value="profesor" <?= (isset($_POST['rol']) && $_POST['rol'] === 'profesor') ? 'selected' : '' ?>>Profesor</option>
                                <option value="alumno" <?= (isset($_POST['rol']) && $_POST['rol'] === 'alumno') ? 'selected' : '' ?>>Alumno</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado" required>
                                <option value="activo" <?= (isset($_POST['estado']) && $_POST['estado'] === 'activo') ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= (isset($_POST['estado']) && $_POST['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono"
                                value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required
                                value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" required
                                value="<?= isset($_POST['apellidos']) ? htmlspecialchars($_POST['apellidos']) : '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DNI</label>
                            <input type="text" class="form-control" name="dni"
                                value="<?= isset($_POST['dni']) ? htmlspecialchars($_POST['dni']) : '' ?>" maxlength="15">
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="userForm" class="btn btn-primary">Guardar Usuario</button>
            </div>
        </div>
    </div>
</div>