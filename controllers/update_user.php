<?php
require_once '../config/config.php';

// Verificar si se recibieron los datos mínimos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $contrasena = $_POST['contrasena'] ?? '';

    try {
        // Inicia la transacción
        $conn->begin_transaction();

        // Actualiza la tabla usuarios
        $stmt = $conn->prepare("UPDATE usuarios SET correo = ?, rol = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("sssi", $correo, $rol, $estado, $id);
        $stmt->execute();

        // Si se proporciona una nueva contraseña, actualizarla (encriptada)
        if (!empty($contrasena)) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmtPwd = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmtPwd->bind_param("si", $hash, $id);
            $stmtPwd->execute();
        }

        // Determinar tabla adicional según el rol
        $tabla = match ($rol) {
            'administrador' => 'administrador',
            'profesor' => 'profesores',
            'alumno' => 'alumnos',
        };

        // Actualizar nombre, apellidos, teléfono en tabla correspondiente
        $stmtExtra = $conn->prepare("UPDATE $tabla SET nombre = ?, apellidos = ?, telefono = ? WHERE usuario_id = ?");
        $stmtExtra->bind_param("sssi", $nombre, $apellidos, $telefono, $id);
        $stmtExtra->execute();

        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
