<?php
ob_start();
session_start();
require_once __DIR__ . '/../config/config.php';

// Verificar permisos de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header("HTTP/1.1 403 Forbidden");
    die("Acceso denegado. Solo administradores pueden crear usuarios.");
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    header("Location: ../views/admin_profile3.php?modal_close=0");
    exit;
}

// Sanitizar datos
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? '';
$estado = $_POST['estado'] ?? 'activo';
$nombre = htmlspecialchars($_POST['nombre'] ?? '');
$apellidos = htmlspecialchars($_POST['apellidos'] ?? '');
$telefono = htmlspecialchars($_POST['telefono'] ?? '');

// Validaciones
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Correo electrónico no válido.";
    header("Location: ../views/admin_profile3.php?modal_close=0");
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
    header("Location: ../views/admin_profile3.php?modal_close=0");
    exit;
}

if (!in_array($rol, ['administrador', 'profesor', 'alumno'])) {
    $_SESSION['error'] = "Rol no válido.";
    header("Location: ../views/admin_profile3.php?modal_close=0");
    exit;
}

// Verificar correo existente
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "El correo electrónico ya está registrado.";
    header("Location: ../views/admin_profile3.php?modal_close=0");
    exit;
}
$stmt->close();

// Hash de contraseña
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Transacción
$conn->begin_transaction();

try {
    // Insertar en usuarios
    $stmt = $conn->prepare("INSERT INTO usuarios (correo, password, rol, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $passwordHash, $rol, $estado);
    $stmt->execute();
    $user_id = $conn->insert_id;
    $stmt->close();

    // Insertar en tabla específica
    switch ($rol) {
        case 'administrador':
            $stmt = $conn->prepare("INSERT INTO administrador (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            break;
        case 'profesor':
            $stmt = $conn->prepare("INSERT INTO profesores (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            break;
        case 'alumno':
            $stmt = $conn->prepare("INSERT INTO alumnos (usuario_id, nombre, apellidos, telefono) VALUES (?, ?, ?, ?)");
            break;
    }
    $stmt->bind_param("isss", $user_id, $nombre, $apellidos, $telefono);
    $stmt->execute();
    $stmt->close();

    $conn->commit();


    // Si es exitoso:
    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => "Usuario creado exitosamente. ID: " . $user_id,
        'icon' => 'check-circle'
    ];
    header("Location: ../views/admin_profile3.php"); // Sin parámetros GET
    exit;
} catch (Exception $e) {

    $conn->rollback();


    // Si hay error:
    $_SESSION['notification'] = [
        'type' => 'danger',
        'message' => "Error al crear usuario: " . $error_message,
        'icon' => 'exclamation-triangle'
    ];
    header("Location: ../views/admin_profile3.php");
    exit;
}

ob_end_flush();
