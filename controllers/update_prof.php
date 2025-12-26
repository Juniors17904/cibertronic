<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $usuario_id = $_POST['usuario_id'];
    $profesor_id = $_POST['profesor_id'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $correo_personal = $_POST['correo_personal'];
    $direccion = $_POST['direccion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $especialidad = $_POST['especialidad'];


    // 📷 Imagen
    // 📷 Procesar imagen si fue enviada correctamente
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $stmtFoto = $conn->prepare("SELECT foto FROM profesores WHERE id = ?");
        $stmtFoto->bind_param("i",  $profesor_id);
        $stmtFoto->execute();
        $resFoto = $stmtFoto->get_result();
        $rowFoto = $resFoto->fetch_assoc();
        $fotoActual = $rowFoto['foto'];

        $nombreTmp = $_FILES['foto']['tmp_name'];
        $nombreArchivo = uniqid() . '_' . basename($_FILES['foto']['name']);
        $carpetaDestino = realpath(__DIR__ . '/../assets/images');
        $destino = $carpetaDestino . DIRECTORY_SEPARATOR . $nombreArchivo;

        if (move_uploaded_file($nombreTmp, $destino)) {
            // ✅ Eliminar la anterior si no es la predeterminada
            $fotoAnteriorPath = $carpetaDestino . DIRECTORY_SEPARATOR . $fotoActual;
            if (!empty($fotoActual) && $fotoActual !== 'perfil.jpg' && file_exists($fotoAnteriorPath)) {
                unlink($fotoAnteriorPath);
            }

            // Actualizar nuevo nombre en base de datos
            $stmt = $conn->prepare("UPDATE profesores SET foto = ? WHERE id = ?");
            $stmt->bind_param("si", $nombreArchivo,  $profesor_id);
            $stmt->execute();
        } else {
            error_log("❌ Error moviendo imagen a destino: $destino");
        }
    }

    $sql = "UPDATE profesores SET 
            nombre = ?, 
            apellidos = ?, 
            dni = ?, 
            telefono = ?, 
            correo_personal = ?, 
            direccion = ?, 
            fecha_nacimiento = ?, 
            especialidad = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssi",
        $nombre,
        $apellidos,
        $dni,
        $telefono,
        $correo_personal,
        $direccion,
        $fecha_nacimiento,
        $especialidad,
        $profesor_id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit();
}
