<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/User.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: main.php?page=login');
    exit();
}

// Obtener datos del formulario
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validaciones básicas
$errors = [];

if (empty($username)) {
    $errors[] = 'El nombre de usuario o email es requerido';
}

if (empty($password)) {
    $errors[] = 'La contraseña es requerida';
}

// Si hay errores, regresar al formulario
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_data'] = ['username' => $username];
    header('Location: main.php?page=login');
    exit();
}

// Intentar autenticar al usuario
try {
    $user = new User();
    $result = $user->login($username, $password);

    if ($result['success']) {
        // Guardar información del usuario en la sesión
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['username'] = $result['user']['username'];
        $_SESSION['email'] = $result['user']['email'];
        $_SESSION['first_name'] = $result['user']['first_name'];
        $_SESSION['last_name'] = $result['user']['last_name'];
        $_SESSION['logged_in'] = true;

        // Limpiar errores
        unset($_SESSION['login_errors']);
        unset($_SESSION['login_data']);

        // Mensaje de éxito
        $_SESSION['success_message'] = 'Login successful! Welcome back.';

        // Redirigir al home o página solicitada
        $redirect = $_SESSION['redirect_after_login'] ?? 'home';
        unset($_SESSION['redirect_after_login']);

        header('Location: main.php?page=' . $redirect);
        exit();
    } else {
        $_SESSION['login_errors'] = [$result['message']];
        $_SESSION['login_data'] = ['username' => $username];
        header('Location: main.php?page=login');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['login_errors'] = ['Error del servidor: ' . $e->getMessage()];
    $_SESSION['login_data'] = ['username' => $username];
    header('Location: main.php?page=login');
    exit();
}
