<?php
// controllers/get_asistencia_fecha.php

require_once '../config/config.php';

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$curso_id = $_GET['curso_id'] ?? null;
$horario_id = $_GET['horario_id'] ?? null;
$fecha = $_GET['fecha'] ?? null;

if (!$curso_id || !$horario_id || !$fecha) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

// Obtener código de asignación
$sqlAsign = "SELECT codigo_asignacion FROM asignaciones WHERE curso_id = ? AND horario_id = ?";
$stmtAsign = $conn->prepare($sqlAsign);
$stmtAsign->bind_param("ii", $curso_id, $horario_id);
$stmtAsign->execute();
$resultAsign = $stmtAsign->get_result();
$codigo_asignacion = '';
if ($row = $resultAsign->fetch_assoc()) {
    $codigo_asignacion = $row['codigo_asignacion'];
}
$stmtAsign->close();

// Obtener alumnos matriculados
$sql = "
    SELECT a.id, CONCAT(a.nombre, ' ', a.apellidos) AS nombre
    FROM matriculas m
    INNER JOIN alumnos a ON a.id = m.alumno_id
    WHERE m.curso_id = ? AND m.horario_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $curso_id, $horario_id);
$stmt->execute();
$result = $stmt->get_result();

$alumnos = [];
while ($row = $result->fetch_assoc()) {
    $alumnos[$row['id']] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'estado' => null,
        'codigo_asignacion' => $codigo_asignacion
    ];
}
$stmt->close();

// Consultar asistencias registradas
if (!empty($alumnos)) {
    $ids = array_keys($alumnos);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $params = array_merge([$curso_id, $horario_id, $fecha], $ids);
    $bindTypes = "iis" . $types;

    $sqlAsist = "
        SELECT alumno_id, estado
        FROM asistencia
        WHERE curso_id = ? AND horario_id = ? AND fecha = ? AND alumno_id IN ($placeholders)
    ";

    $stmt2 = $conn->prepare($sqlAsist);

    $refs = [];
    $refs[] = &$bindTypes;
    foreach ($params as $k => $v) {
        $refs[] = &$params[$k];
    }

    call_user_func_array([$stmt2, 'bind_param'], $refs);
    $stmt2->execute();
    $resAsist = $stmt2->get_result();

    while ($row = $resAsist->fetch_assoc()) {
        $id = $row['alumno_id'];
        if (isset($alumnos[$id])) {
            $alumnos[$id]['estado'] = $row['estado'];
        }
    }

    $stmt2->close();
}

echo json_encode(array_values($alumnos));
