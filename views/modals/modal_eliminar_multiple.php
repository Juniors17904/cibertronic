<!-- Modal de confirmación para múltiples eliminaciones -->
<div class="modal fade" id="modalEliminarSeleccionados" tabindex="-1" aria-labelledby="modalEliminarSeleccionadosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarSeleccionadosLabel">
                    <i class="fas fa-user-minus me-2"></i>Eliminar Usuarios Seleccionados
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="mensajeEliminarSeleccionados">¿Estás seguro que deseas eliminar los usuarios seleccionados? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarSeleccionados">Eliminar</button>
            </div>
        </div>
    </div>
</div>