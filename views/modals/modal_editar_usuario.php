<!-- Modal: Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalEditarUsuarioLabel"><i class="fas fa-edit me-2"></i>Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="formEditarUsuario" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="editarUsuarioId" name="id">

                    <div class="mb-3">
                        <label for="editarCorreo" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="editarCorreo" name="correo" required>
                    </div>

                    <div class="mb-3">
                        <label for="editarNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editarNombre" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label for="editarApellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="editarApellidos" name="apellidos" required>
                    </div>

                    <div class="mb-3">
                        <label for="editarTelefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="editarTelefono" name="telefono">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editarRol" class="form-label">Rol</label>
                            <select class="form-select" id="editarRol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="profesor">Profesor</option>
                                <option value="alumno">Alumno</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="editarEstado" class="form-label">Estado</label>
                            <select class="form-select" id="editarEstado" name="estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editarContrasena" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="editarContrasena" name="contrasena" placeholder="Dejar vacío para mantener la actual">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>