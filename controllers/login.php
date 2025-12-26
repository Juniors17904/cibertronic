
<?php
require_once('AuthController.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $auth = new AuthController();
    $auth->login($email, $password);
} else {

    header('Location: ../login.php');
    exit();
}
