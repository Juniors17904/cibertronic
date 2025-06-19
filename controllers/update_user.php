<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $rolNuevo = $_POST['rol'];
    $estado = $_POST['estado'];
    $contrasena = $_POST['contrasena'] ?? '';

    try {
        $conn->begin_transaction();

        // Obtener el rol anterior
        $stmtOld = $conn->prepare("SELECT rol FROM usuarios WHERE id = ?");
        $stmtOld->bind_param("i", $id);
        $stmtOld->execute();
        $stmtOld->bind_result($rolAnterior);
        $stmtOld->fetch();
        $stmtOld->close();

        // Actualizar tabla usuarios
        $stmt = $conn->prepare("UPDATE usuarios SET correo = ?, rol = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("sssi", $correo, $rolNuevo, $estado, $id);
        $stmt->execute();

        // Si hay nueva contraseña
        if (!empty($contrasena)) {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmtPwd = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmtPwd->bind_param("si", $hash, $id);
            $stmtPwd->execute();
        }

        // Si cambió el rol, eliminar de la tabla anterior y agregar a la nueva
        if ($rolAnterior !== $rolNuevo) {
            $tablas = [
                'administrador' => 'administrador',
                'profesor' => 'profesores',
                'alumno' => 'alumnos'
            ];

            $tablaAnterior = $tablas[$rolAnterior];
            $tablaNueva = $tablas[$rolNuevo];

            // Eliminar de la tabla anterior
            $stmtDel = $conn->prepare("DELETE FROM $tablaAnterior WHERE usuario_id = ?");
            $stmtDel->bind_param("i", $id);
            $stmtDel->execute();

            // Insertar en la nueva tabla si no existe
            $stmtCheck = $conn->prepare("SELECT id FROM $tablaNueva WHERE usuario_id = ?");
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows === 0) {
                $stmtInsert = $conn->prepare("INSERT INTO $tablaNueva (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
                $stmtInsert->bind_param("isss", $id, $nombre, $apellidos, $telefono);
                $stmtInsert->execute();
            }
        } else {
            // Si no cambió el rol, solo actualiza los datos básicos
            $tabla = match ($rolNuevo) {
                'administrador' => 'administrador',
                'profesor' => 'profesores',
                'alumno' => 'alumnos',
            };

            $stmtExtra = $conn->prepare("UPDATE $tabla SET nombre = ?, apellidos = ?, telefono = ? WHERE usuario_id = ?");
            $stmtExtra->bind_param("sssi", $nombre, $apellidos, $telefono, $id);
            $stmtExtra->execute();
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}
