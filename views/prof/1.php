<?php include '../header.php'; ?>

<body>
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <main class="col-md-8 col-lg-9 px-md-5 py-4">

                <!-- Bienvenida -->
                <div class="mb-5">
                    <h2 class="mb-2 text-primary">Bienvenido</h2>
                    <p class="text-muted fs-5">Presiona el botón para ver el toast</p>
                </div>

                <button id="btnToast" class="btn btn-danger">NOTIFICACIÓN</button>

            </main>
        </div>
    </div>

    <!-- ✅ TOAST oculto -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Aquí va el mensaje.
            </div>
        </div>
    </div>

    <!-- ✅ Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="notificaciones.js"></script>

</body>

</html>