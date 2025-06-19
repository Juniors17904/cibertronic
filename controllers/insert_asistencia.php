<?php
require_once '../config/config.php';
date_default_timezone_set('America/Lima');

// Mostrar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener el siguiente número de sesión
$sesion_registro = 1;
$res = $conn->query("SELECT MAX(sesion_registro) AS max_sesion FROM asistencia");
if ($fila = $res->fetch_assoc()) {
    $sesion_registro = intval($fila['max_sesion']) + 1;
}


// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    error_log("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Validar campos
if (!isset($_POST['fecha'], $_POST['curso_id'], $_POST['horario_id'], $_POST['asistencia'])) {
    http_response_code(400);
    error_log("Faltan datos POST: " . json_encode($_POST));
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

$fecha = $_POST['fecha'];
$asistencias = $_POST['asistencia'];
$curso_id = intval($_POST['curso_id']);
$horario_id = intval($_POST['horario_id']);
$profesor_id = null;

// Buscar profesor asignado
$query = "SELECT profesor_id FROM asignaciones WHERE curso_id = ? AND horario_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("Error en prepare (buscar prof): " . $conn->error);
    echo json_encode(['error' => 'Error interno']);
    exit;
}
$stmt->bind_param("ii", $curso_id, $horario_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $profesor_id = $row['profesor_id'];
} else {
    error_log("No se encontró profesor para curso_id=$curso_id y horario_id=$horario_id");
    echo json_encode(['error' => 'Profesor no encontrado']);
    exit;
}

// Insertar asistencias
$query = "INSERT INTO asistencia (alumno_id, curso_id, horario_id, profesor_id, fecha, estado, sesion_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    error_log("Error en prepare (insert asistencia): " . $conn->error);
    echo json_encode(['error' => 'Error al preparar inserción']);
    exit;
}

foreach ($asistencias as $alumno_id => $estado) {
    $estado = ucfirst(strtolower($estado));
    $stmt->bind_param("iiisssi", $alumno_id, $curso_id, $horario_id, $profesor_id, $fecha, $estado, $sesion_registro);
    if (!$stmt->execute()) {
        error_log("Error al insertar asistencia de alumno_id=$alumno_id: " . $stmt->error);
    }
}

echo json_encode(['success' => true]);
