<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = $_POST['curso_id'] ?? null;
    $horario_id = $_POST['horario_id'] ?? null;
    $profesor_id = $_POST['profesor_id'] ?? null;

    error_log("📥 POST recibido: curso_id=$curso_id, horario_id=$horario_id, profesor_id=$profesor_id");

    if (!$curso_id || !$horario_id || !$profesor_id) {
        error_log("❌ Faltan datos requeridos.");
        echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO asignaciones (curso_id, horario_id, profesor_id) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("❌ Error al preparar consulta: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error al preparar consulta.']);
        exit;
    }

    $stmt->bind_param("iii", $curso_id, $horario_id, $profesor_id);

    if ($stmt->execute()) {
        error_log("✅ Asignación guardada correctamente.");
        echo json_encode(['success' => true]);
    } else {
        error_log("❌ Error al ejecutar consulta: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
    }
} else {
    error_log("⚠️ Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Petición inválida.']);
}
