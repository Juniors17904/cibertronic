<?php
require_once '../config/config.php';

header('Content-Type: application/json');

// Validar datos recibidos
$id = $_POST['id'] ?? null;
$curso_id = $_POST['curso_id'] ?? null;
$horario_id = $_POST['horario_id'] ?? null;
$profesor_id = $_POST['profesor_id'] ?? null;

if (!$id || !$curso_id || !$horario_id || !$profesor_id) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

// Sanitizar y preparar datos
$id = intval($id);
$curso_id = intval($curso_id);
$horario_id = intval($horario_id);
$profesor_id = intval($profesor_id);

// Ejecutar actualización
$query = "UPDATE asignaciones SET curso_id = ?, horario_id = ?, profesor_id = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta"]);
    exit;
}

$stmt->bind_param("iiii", $curso_id, $horario_id, $profesor_id, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar asignación"]);
}

$stmt->close();
$conn->close();
