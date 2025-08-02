<?php
include_once '../../class_session/session.php';
require_once '../../database/User.php';
require_once '../../database/Profiles.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    header('Location: main.php?page=register');
    exit();
}

// Obtener datos del formulario
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');

// Validaciones básicas
$errors = [];

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

// Si hay errores, regresar al formulario
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_data'] = [
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName
    ];
    ob_end_clean();

    header('Location: main.php?page=register');
    exit();
}

// Intentar registrar al usuario
try {
    $user = new User();
    $result = $user->register($username, $email, $password, $firstName, $lastName);

    $profile = new Profiles();
    $profileData = [
        'user_uuid' => $result['uuid'],
        'national_id_nr' => '',
        'nationality' => '',
        'date_of_birth' => '',
        'street' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => '',
        'phone_number' => '',
        'profile_picture' => ''
    ];
    $profileResult = $profile->registerUserProfile($profileData);
    if ($result['success'] && $profileResult) {
        $_SESSION['success_message'] = $result['message'];
        $_SESSION['registered_user'] = $username;

        // Limpiar datos del formulario
        unset($_SESSION['register_errors']);
        unset($_SESSION['register_data']);

        // Redirigir al login
        header('Location: ../../index.php?page=login&registered=1');
        exit();
    } else {
        $_SESSION['register_errors'] = [$result['message']];
        $_SESSION['register_data'] = [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        header('Location: ../../index.php?page=error_register_handler');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['register_errors'] = ['Error del servidor: ' . $e->getMessage()];
    $_SESSION['register_data'] = [
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName
    ];
    header('Location: ../../index.php?page=register');
    exit();
}
