<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin_id = $_POST['admin_id'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'] ?? null;
    $correo_personal = $_POST['correo_personal'];
    $direccion = $_POST['direccion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    // 🔄 Actualizar tabla administrador
    $sql = "UPDATE administrador SET
                nombre = ?, 
                apellidos = ?,
                dni = ?, 
                telefono = ?, 
                correo_personal = ?, 
                direccion = ?, 
                fecha_nacimiento = ?
                WHERE id = ?";

    error_log("📝 SQL Admin: $sql");
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("❌ Error preparando SQL admin: " . $conn->error);
    }
    $stmt->bind_param("sssssssi", $nombre, $apellidos, $dni, $telefono, $correo_personal, $direccion, $fecha_nacimiento, $admin_id);
    $success = $stmt->execute();
    if (!$success) {
        error_log("❌ Error ejecutando UPDATE admin: " . $stmt->error);
    }

    // 📷 Imagen
    // 📷 Procesar imagen si fue enviada correctamente
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $stmtFoto = $conn->prepare("SELECT foto FROM administrador WHERE id = ?");
        $stmtFoto->bind_param("i", $admin_id);
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
            $stmt = $conn->prepare("UPDATE administrador SET foto = ? WHERE id = ?");
            $stmt->bind_param("si", $nombreArchivo, $admin_id);
            $stmt->execute();
        } else {
            error_log("❌ Error moviendo imagen a destino: $destino");
        }
    }



    // Usuario vinculado
    $stmt2 = $conn->prepare("SELECT usuario_id FROM administrador WHERE id = ?");
    $stmt2->bind_param("i", $admin_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $usuario_id = $row['usuario_id'] ?? null;
    if (!$usuario_id) {
        error_log("❌ No se encontró usuario_id para admin_id $admin_id");
    }

    // Correo institucional
    if ($correo) {
        $stmt3 = $conn->prepare("UPDATE usuarios SET correo = ? WHERE id = ?");
        if (!$stmt3) {
            error_log("❌ Error preparando UPDATE usuarios: " . $conn->error);
        }
        $stmt3->bind_param("si", $correo, $usuario_id);
        $success2 = $stmt3->execute();
        if (!$success2) {
            error_log("❌ Error ejecutando UPDATE usuarios: " . $stmt3->error);
        }
    } else {
        $success2 = true;
    }

    // Respuesta
    if ($success && $success2) {
        echo json_encode(['success' => true]);
    } else {
        error_log("❌ Error general al guardar perfil.");
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit();
}
