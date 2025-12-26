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
  asi.fecha,
  h.dia,
  h.hora_inicio,
  h.hora_fin,
  CONCAT(al.nombre, ' ', al.apellidos) AS alumno,
  al.codigo_usuario as codigo_usuario,
  u.id AS codigo_user,          -- ⚡ Aquí sacas el código de alumno
  asi.estado
FROM asignaciones a
JOIN cursos c ON a.curso_id = c.id
JOIN asistencia asi ON asi.curso_id = a.curso_id AND asi.horario_id = a.horario_id
JOIN alumnos al ON asi.alumno_id = al.id
JOIN usuarios u ON al.usuario_id = u.id   -- ⚡ Este JOIN es clave
JOIN horarios h ON asi.horario_id = h.id
WHERE a.codigo_asignacion = ?
ORDER BY asi.fecha DESC
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
  $rows[] = $row;
}

error_log("✅ [RESULTADOS] Filas encontradas: " . count($rows));

echo json_encode($rows);
