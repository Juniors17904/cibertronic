<?php ob_start();
session_start();
require_once __DIR__ . '/../config/config.php';

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header("HTTP/1.1 403 Forbidden");
    die("Acceso denegado. Solo administradores pueden crear usuarios.");
}

// Validar datos del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    die("Método no permitido.");
}

// Obtener y sanitizar datos
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? '';
$estado = $_POST['estado'] ?? 'activo';
$nombre = htmlspecialchars($_POST['nombre'] ?? '');
$apellidos = htmlspecialchars($_POST['apellidos'] ?? '');
$telefono = htmlspecialchars($_POST['telefono'] ?? '');

// Validaciones básicas
// 1. Valida formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Correo electrónico no válido.");
}

// 2. Valida longitud mínima de la contraseña (8 caracteres)
if (strlen($password) < 8) {
    die("La contraseña debe tener al menos 8 caracteres.");
}

// 3. Valida que el rol sea uno de los permitidos
if (!in_array($rol, ['administrador', 'profesor', 'alumno'])) {
    die("Rol no válido.");
}

// 4. Valida que el estado sea "activo" o "inactivo"
if (!in_array($estado, ['activo', 'inactivo'])) {
    die("Estado no válido.");
}

// Verificar si el correo ya existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    die("El correo electrónico ya está registrado.");
}
$stmt->close();

// Hash de la contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Iniciar transacción para asegurar la integridad de los datos
$conn->begin_transaction();

try {
    // 1. Insertar en la tabla usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (correo, password, rol, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $passwordHash, $rol, $estado);
    $stmt->execute();
    $user_id = $conn->insert_id;
    $stmt->close();

    // 2. Insertar en la tabla específica según el rol
    switch ($rol) {
        case 'administrador':
            $stmt = $conn->prepare("INSERT INTO administrador (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $nombre, $apellidos, $telefono);
            $stmt->execute();
            $stmt->close();
            break;

        case 'profesor':
            $stmt = $conn->prepare("INSERT INTO profesores (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $nombre, $apellidos, $telefono);
            $stmt->execute();
            $stmt->close();
            break;

        case 'alumno':
            $stmt = $conn->prepare("INSERT INTO alumnos (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $nombre, $apellidos, $telefono);
            $stmt->execute();
            $stmt->close();
            break;
    }

    // Confirmar la transacción
    $conn->commit();

    // Respuesta JSON única (sin mensajes DEBUG)
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado exitosamente. ID: ' . $user_id,
        'user_id' => $user_id
    ]);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error al crear el usuario: ' . $e->getMessage()
    ]);
    exit;
}
ob_end_flush(); // Limpia todo y solo muestra el JSON  
