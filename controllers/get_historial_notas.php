<?php
require_once '../config/config.php';

$profesor_id = isset($_GET['profesor_id']) ? intval($_GET['profesor_id']) : 0;

$sql = "
    SELECT 
        a.codigo_asignacion,
        c.nombre_curso AS asignatura,
        al.nombre AS nombre_alumno,
        al.apellidos AS apellidos_alumno,
        n.nota_01,
        n.nota_02,
        n.nota_03
    FROM asignaciones a
    JOIN cursos c ON c.id = a.curso_id
    JOIN horarios h ON h.id = a.horario_id
    JOIN matriculas m ON m.curso_id = c.id AND m.horario_id = h.id
    JOIN alumnos al ON al.id = m.alumno_id
    LEFT JOIN notas n ON n.alumno_id = al.id AND n.asignatura = c.nombre_curso
    WHERE a.profesor_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profesor_id);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($row = $result->fetch_assoc()) {
    $row['nota_01'] = $row['nota_01'] ?? '0.00';
    $row['nota_02'] = $row['nota_02'] ?? '0.00';
    $row['nota_03'] = $row['nota_03'] ?? '0.00';
    $datos[] = $row;
}

echo json_encode($datos);
