<?php
require_once '../config/config.php';

$profesor_id = $_GET['profesor_id'] ?? null;

if (!$profesor_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT 
            a.fecha,
            asig.codigo_asignacion,
            c.nombre_curso,
            h.dia,
            h.hora_inicio,
            h.hora_fin,
            al.nombre AS nombre_alumno,
            al.apellidos AS apellidos_alumno,
            a.estado
        FROM asistencia a
        JOIN cursos c ON a.curso_id = c.id
        JOIN horarios h ON a.horario_id = h.id
        JOIN alumnos al ON a.alumno_id = al.id
        JOIN asignaciones asig ON asig.curso_id = a.curso_id AND asig.horario_id = a.horario_id AND asig.profesor_id = a.profesor_id
        WHERE a.profesor_id = ?
        ORDER BY a.fecha DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $profesor_id);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);
