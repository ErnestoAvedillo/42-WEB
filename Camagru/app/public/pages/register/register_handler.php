<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../EnvLoader.php';
require_once __DIR__ . '/../../utils/send_mail.php';
// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $_SESSION['error_messages'] = ['Método no permitido. Por favor, utiliza el formulario de registro.'];
    $_SESSION['register_data'] = [
        'username' => $_GET['username'] ?? '',
        'email' => $_GET['email'] ?? '',
        'first_name' => $_GET['first_name'] ?? '',
        'last_name' => $_GET['last_name'] ?? ''
    ];
    header('Location: /pages/register/register.php');
    exit();
}

// Obtener datos del formulario
$username = trim($_GET['username'] ?? '');
$email = trim($_GET['email'] ?? '');
$password = $_GET['password'] ?? '';
$firstName = trim($_GET['first_name'] ?? '');
$lastName = trim($_GET['last_name'] ?? '');
$confirmPassword = $_GET['confirm_password'] ?? '';

// Validaciones básicas
$errors = [];

$Users = new User();
if ($Users->isUsernameTaken($username)) {
    $errors[] = 'El nombre de usuario ya está en uso';
}
if ($Users->isEmailTaken($email)) {
    $errors[] = 'El email ya está en uso';
}

if (empty($username)) {
    $errors[] = 'El nombre de usuario es requerido';
}

if (empty($email)) {
    $errors[] = 'El email es requerido';
}

if (empty($password)) {
    $errors[] = 'La contraseña es requerida';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Las contraseñas no coinciden';
}

$email = filter_var($email, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El email proporcionado no es válido';
}

// Si hay errores, regresar al formulario
if (!empty($errors)) {
    $_SESSION['error_messages'] = $errors;
    $_SESSION['register_data'] = [
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName
    ];
    header('Location: /pages/register/register.php');
    exit();
}

try {
    $validationToken = send_validation_token($email, $username);
    if ($validationToken === null) {
        throw new Exception('Error al generar o enviar el correo electrónico de validación. Por favor, inténtalo de nuevo más tarde.');
    }
} catch (Exception $e) {
    $_SESSION['error_messages'] = ['Error al enviar el correo de confirmación: ' . $e->getMessage()];
    $_SESSION['register_data'] = [
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName
    ];
    header('Location: /pages/register/register.php');
    exit();
}
$_SESSION['register_data'] = [
    'username' => $username,
    'email' => $email,
    'first_name' => $_GET['first_name'] ?? '',
    'last_name' => $_GET['last_name'] ?? '',
    'password' => $password
];
// Guardar el token de validación en la sesión en vez de enviarlo por GET
$_SESSION['validation_token'] = $validationToken;
header('Location: /pages/register/confirm.php');
