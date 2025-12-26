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
    <?php include 'cabecera.php'; ?>
    <div class="container-fluid mt-0">
        <div class="row">
            <?php include 'lateral.php'; ?>

            <!-- <main class="col-md-7 col-lg-8 px-5 py-4"> -->
            <main class="col-md-8 col-lg-9 px-2 px-md-5 py-4">

                <div class="card shadow-lg rounded mb-4">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0">Mi Perfil</h2>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-9">

                                <!-- Foto de perfil -->
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        <img id="fotoPerfil"
                                            src="../../assets/images/<?= htmlspecialchars(!empty(trim($prof['foto'])) ? $prof['foto'] : 'perfil.jpg') ?>"
                                            alt="Foto de perfil"
                                            class="img-thumbnail rounded-circle"
                                            style="width: 150px; height: 150px; object-fit: cover;">

                                        <button type="button"
                                            class="btn btn-light border rounded-circle p-2 position-absolute"
                                            style="bottom: 10px; right: 10px;"
                                            onclick="document.getElementById('fileInput').click();">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </div>


                                <form id="perfilForm">

                                    <input type="file" id="fileInput" name="foto" accept="image/*" style="display: none;">

                                    <div class="row gx-5 gy-4">

                                        <!-- Nombre -->
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre</label>
                                            <div class="input-group">
                                                <input name="nombre" id="nombre" type="text" class="form-control"
                                                    value="<?= htmlspecialchars($prof['nombre']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('nombre')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Apellidos -->
                                        <div class="col-md-6">
                                            <label class="form-label">Apellidos</label>
                                            <div class="input-group">
                                                <input name="apellidos" id="apellidos" type="text" class="form-control"
                                                    value="<?= htmlspecialchars($prof['apellidos']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('apellidos')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- DNI -->
                                        <div class="col-md-6">
                                            <label class="form-label">DNI</label>
                                            <div class="input-group">
                                                <input name="dni" id="dni" type="number" class="form-control"
                                                    value="<?= htmlspecialchars($prof['dni']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('dni')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Teléfono -->
                                        <div class="col-md-6">
                                            <label class="form-label">Teléfono</label>
                                            <div class="input-group">
                                                <input name="telefono" id="telefono" type="number" class="form-control"
                                                    value="<?= htmlspecialchars($prof['telefono']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('telefono')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Correo Personal -->
                                        <div class="col-md-6">
                                            <label class="form-label">Correo Personal</label>
                                            <div class="input-group">
                                                <input name="correo_personal" id="correo_personal" type="text" class="form-control"
                                                    value="<?= htmlspecialchars($prof['correo_personal']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('correo_personal')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Dirección -->
                                        <div class="col-md-6">
                                            <label class="form-label">Dirección</label>
                                            <div class="input-group">
                                                <input name="direccion" id="direccion" type="text" class="form-control"
                                                    value="<?= htmlspecialchars($prof['direccion']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('direccion')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Fecha de Nacimiento -->
                                        <div class="col-md-6">
                                            <label class="form-label">Fecha de Nacimiento</label>
                                            <div class="input-group">
                                                <input name="fecha_nacimiento" id="fecha_nacimiento" type="date" class="form-control"
                                                    value="<?= htmlspecialchars($prof['fecha_nacimiento']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('fecha_nacimiento')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Especialidad -->
                                        <div class="col-md-6">
                                            <label class="form-label">Especialidad</label>
                                            <div class="input-group">
                                                <input name="especialidad" id="especialidad" type="text" class="form-control"
                                                    value="<?= htmlspecialchars($prof['especialidad']) ?>" readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarCampo('especialidad')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Código de Profesor -->
                                        <div class="col-md-6">
                                            <label class="form-label">Código de Profesor</label>
                                            <input type="text" class="form-control-plaintext mb-0"
                                                value="<?= htmlspecialchars($prof['codigo_usuario']) ?>" readonly>
                                        </div>

                                        <!-- Correo Institucional -->
                                        <div class="col-md-6">
                                            <label class="form-label">Correo Institucional</label>
                                            <input type="text" class="form-control-plaintext mb-0"
                                                value="<?= htmlspecialchars($prof['correo_institucional']) ?>" readonly>
                                        </div>

                                        <!-- Fecha de Contrato -->
                                        <div class="col-md-6">
                                            <label class="form-label">Fecha de Contrato</label>
                                            <input type="text" class="form-control-plaintext mb-0"
                                                value="<?= htmlspecialchars($prof['fecha_contrato']) ?>" readonly>
                                        </div>

                                        <!-- Rol -->
                                        <div class="col-md-6">
                                            <label class="form-label">Rol</label>
                                            <input type="text" class="form-control-plaintext mb-0"
                                                value="<?= htmlspecialchars(ucfirst($prof['rol'])) ?>" readonly>
                                        </div>

                                        <!-- Estado -->
                                        <div class="col-md-6">
                                            <label class="form-label">Estado</label>
                                            <input type="text" class="form-control-plaintext mb-0"
                                                value="<?= htmlspecialchars(ucfirst($prof['estado'])) ?>" readonly>
                                        </div>

                                        <!-- Hidden -->
                                        <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($prof['usuario_id']) ?>">
                                        <input type="hidden" name="profesor_id" value="<?= htmlspecialchars($prof['id']) ?>">

                                        <!-- Botones -->
                                        <div class="col-12 text-center mt-4">
                                            <button id="guardarBtn" type="button" class="btn btn-success d-none">Guardar Cambios</button>
                                            <button id="cancelarBtn" type="button" class="btn btn-secondary d-none" onclick="cancelarEdicion()">Cancelar</button>
                                        </div>

                                    </div>
                                </form>




                                <!-- Bloque Toast -->
                                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                                    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header">
                                            <strong class="me-auto">Notificación</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function editarCampo(campo) {
            const input = document.getElementById(campo);
            input.removeAttribute('readonly');
            input.focus();
            document.getElementById('guardarBtn').classList.remove('d-none');
            document.getElementById('cancelarBtn').classList.remove('d-none');
        }

        function cancelarEdicion() {
            location.reload();
        }

        document.getElementById('guardarBtn').addEventListener('click', () => {
            const form = document.getElementById('perfilForm');
            const formData = new FormData(form);

            fetch('../../controllers/update_prof.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Datos actualizados correctamente.', 'success', 'check-circle');
                        document.getElementById('guardarBtn').classList.add('d-none');
                        document.getElementById('cancelarBtn').classList.add('d-none');
                        form.querySelectorAll('input').forEach(input => {
                            if (!input.readOnly && input.name) {
                                input.setAttribute('readonly', true);
                            }
                        });
                    } else {
                        showNotification('Error al guardar.', 'danger', 'times-circle');
                    }
                })
                .catch(err => {
                    console.error('Error en fetch:', err); // 👈 Muestra el error en consola del navegador
                    showNotification('Error de red: ' + err.message, 'danger', 'times-circle');
                });
        });

        document.getElementById('fileInput').addEventListener('change', function(event) {
            const archivo = event.target.files[0];
            if (archivo) {
                const lector = new FileReader();
                lector.onload = function(e) {
                    document.getElementById('fotoPerfil').src = e.target.result;
                };
                lector.readAsDataURL(archivo);


                // 👇 Activar botones al seleccionar imagen
                document.getElementById('guardarBtn').classList.remove('d-none');
                document.getElementById('cancelarBtn').classList.remove('d-none');
            }
        });

        function showNotification(message, type = 'success', icon = 'check-circle') {
            const toastEl = document.getElementById('liveToast');
            const toastHeader = toastEl.querySelector('.toast-header');
            const toastBody = toastEl.querySelector('.toast-body');

            toastHeader.className = 'toast-header';
            toastBody.className = 'toast-body';

            switch (type) {
                case 'success':
                    toastHeader.classList.add('bg-success', 'text-white');
                    break;
                case 'danger':
                    toastHeader.classList.add('bg-danger', 'text-white');
                    break;
                case 'warning':
                    toastHeader.classList.add('bg-warning', 'text-dark');
                    break;
                default:
                    toastHeader.classList.add('bg-primary', 'text-white');
            }

            toastBody.innerHTML = `<i class="fas fa-${icon} me-2"></i> ${message}`;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            setTimeout(() => toast.hide(), 5000);
        }


        function cancelarEdicion() {
            showNotification('Cambios descartados.', 'warning', 'exclamation-triangle');
            setTimeout(() => location.reload(), 1000);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>