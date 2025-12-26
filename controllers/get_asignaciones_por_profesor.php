<?php
require_once '.../config/config.php';

$id = $_GET['id'] ?? 0;

$sql = "SELECT COUNT(*) as total FROM asignaciones WHERE profesor_id = (SELECT id FROM profesores WHERE usuario_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
echo json_encode(['total' => $result['total'] ?? 0]);
