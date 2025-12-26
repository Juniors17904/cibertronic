<?php
require_once '../config/config.php';

$asignacion_id = $_GET['asignacion_id'] ?? '';

error_log("✅ [DEBUG] asignacion_id recibido: " . $asignacion_id);

if (!$asignacion_id) {
    error_log("❌ [ERROR] No se recibió asignacion_id");
    echo json_encode([]);
    exit;
}

$query = "
SELECT 
  a.codigo_asignacion,
  c.nombre_curso AS curso,
  CONCAT(al.nombre, ' ', al.apellidos) AS alumno,
  al.codigo_usuario AS codigo_alumno,   -- ✅ Aquí tu código de alumno real
  u.id AS codigo_usuario,
  n.nota_01,
  n.nota_02,
  n.nota_03
FROM asignaciones a
JOIN cursos c ON a.curso_id = c.id
JOIN matriculas m ON m.curso_id = c.id AND m.horario_id = a.horario_id
JOIN alumnos al ON m.alumno_id = al.id
JOIN usuarios u ON al.usuario_id = u.id
LEFT JOIN notas n ON n.alumno_id = al.id AND n.asignatura = c.nombre_curso
WHERE a.codigo_asignacion = ?
";

error_log("📌 [QUERY] " . $query);

$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("❌ [ERROR] prepare failed: " . $conn->error);
    echo json_encode([]);
    exit;
}

$stmt->bind_param("s", $asignacion_id);

if (!$stmt->execute()) {
    error_log("❌ [ERROR] execute failed: " . $stmt->error);
    echo json_encode([]);
    exit;
}

$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $row['nota_01'] = $row['nota_01'] ?? 0;
    $row['nota_02'] = $row['nota_02'] ?? 0;
    $row['nota_03'] = $row['nota_03'] ?? 0;
    $rows[] = $row;
}

error_log("✅ [RESULTADOS] Filas encontradas: " . count($rows));
echo json_encode($rows);
