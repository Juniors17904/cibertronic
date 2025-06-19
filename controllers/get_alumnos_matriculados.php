<?php
// Conexión a la base de datos
require_once '../config/config.php'; // Ajusta la ruta si es diferente

// Encabezado de respuesta JSON
header('Content-Type: application/json');

// MOSTRAR ERRORES (durante desarrollo, quítalo en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar que lleguen los parámetros
if (!isset($_GET['curso_id']) || !isset($_GET['horario_id'])) {
    http_response_code(400);
    error_log("FALTAN PARÁMETROS: " . json_encode($_GET)); // log para debug
    echo json_encode(["error" => "Faltan parámetros"]);
    exit;
}

// Obtener y convertir parámetros
$curso_id = intval($_GET['curso_id']);
$horario_id = intval($_GET['horario_id']);

// Registrar log de entrada
error_log("→ Petición recibida: curso_id=$curso_id | horario_id=$horario_id");

// Consulta SQL para obtener alumnos matriculados
$query = "
    SELECT a.id, a.nombre, a.apellidos
    FROM matriculas m
    INNER JOIN alumnos a ON m.alumno_id = a.id
    WHERE m.curso_id = ? AND m.horario_id = ?
";

// Preparar la consulta
$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    error_log("❌ Error en prepare: " . $conn->error);
    echo json_encode(["error" => "Error en prepare"]);
    exit;
}

// Enlazar parámetros y ejecutar
$stmt->bind_param("ii", $curso_id, $horario_id);
$stmt->execute();

// Obtener resultado
$result = $stmt->get_result();
if (!$result) {
    http_response_code(500);
    error_log("❌ Error en get_result: " . $stmt->error);
    echo json_encode(["error" => "Error al obtener resultados"]);
    exit;
}

// Armar respuesta
$alumnos = [];
while ($row = $result->fetch_assoc()) {
    $alumnos[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'] . ' ' . $row['apellidos']
    ];
}

// Log cantidad de alumnos encontrados
error_log("✅ Alumnos encontrados: " . count($alumnos));

// Devolver respuesta
echo json_encode($alumnos);
