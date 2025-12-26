<!-- Pie de página -->
<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        <p class="mb-2 small">&copy; <?php echo date("Y"); ?> Cibertronic S.R.L. Todos los derechos reservados.</p>
        <p class="mb-0 small">
            <a href="#" class="text-white text-decoration-none me-2">Política de Privacidad</a>
            <span class="d-none d-sm-inline">|</span>
            <br class="d-sm-none">
            <a href="#" class="text-white text-decoration-none ms-sm-2">Términos de Servicio</a>
        </p>
        <?php if (defined('IS_LOCAL') && IS_LOCAL): ?>
            <p class="mb-0 mt-2"><small class="badge bg-warning text-dark">🔄 Auto-refresh activado (3s)</small></p>
        <?php endif; ?>
    </div>
</footer>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (defined('IS_LOCAL') && IS_LOCAL): ?>
<!-- Auto-refresh solo en desarrollo local -->
<script>
    setTimeout(function() {
        location.reload();
    }, 3000); // Recarga cada 3 segundos
</script>
<?php endif; ?>