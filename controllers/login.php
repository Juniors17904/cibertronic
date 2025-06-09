
<?php
require_once('AuthController.php'); // Ajusta la ruta si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $auth = new AuthController();
    $auth->login($email, $password);
} else {
    // Si se accede directamente sin enviar datos POST, redirige al formulario
    header('Location: ../login.php');
    exit();
}
