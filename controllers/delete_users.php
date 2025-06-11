<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔁 Obtener IDs desde user_ids (múltiple) o eliminar_individual
    if (isset($_POST['user_ids'])) {
        $ids = explode(',', $_POST['user_ids']);
    } elseif (isset($_POST['eliminar_individual'])) {
        $ids = [intval($_POST['eliminar_individual'])];
    } else {
        echo json_encode(['success' => false, 'message' => 'No se especificaron IDs']);
        exit;
    }

    $ids = array_map('intval', $ids);
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'Lista de IDs vacía']);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    // 🧹 Eliminar en orden: hijos → usuarios
    foreach (['administrador', 'profesores', 'alumnos'] as $tabla) {
        $sql = "DELETE FROM $tabla WHERE usuario_id IN ($placeholders)";
        error_log("🧹 $tabla: $sql");
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
        } else {
            error_log("❌ Error prepare $tabla: " . $conn->error);
        }
    }

    $sql = "DELETE FROM usuarios WHERE id IN ($placeholders)";
    error_log("🧨 usuarios: $sql");
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("❌ Error prepare usuarios: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error preparando eliminación']);
        exit;
    }

    $stmt->bind_param($types, ...$ids);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log("❌ Error ejecutando usuarios: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error ejecutando eliminación']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Petición inválida']);
