<?php
include_once '../../class_session/session.php';
require_once '../../database/User.php';
require_once '../../database/Profiles.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    header('Location: /pages/register/register.php');
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

    header('Location: /pages/register/register.php');
    exit();
}

// Intentar registrar al usuario
try {
    $user = new User();
    $result = $user->register($username, $email, $password, $firstName, $lastName);
    // ✅ DEBUG: Ver qué devuelve el registro
    error_log("Register result: " . json_encode($result));

    if ($result['success']) {
        // ✅ VERIFICAR QUE EXISTE 'uuid' ANTES DE USARLO
        $userUuid = $result['uuid'] ?? $result['user_uuid'] ?? $result['id'] ?? null;

        if (!$userUuid) {
            throw new Exception('No se pudo obtener el UUID del usuario registrado');
        }

        $profile = new Profiles();
        $profileData = [
            'user_uuid' => $userUuid,
            'date_of_birth' => '1900-01-01',
        ];
        $profileResult = $profile->registerUserProfile($profileData);
        //echo "<p>✓ Registro de perfil: " . $profileResult['message'] . "</p>";
        if (!$profileResult['success']) {
            throw new Exception('Error al registrar el perfil del usuario');
        }
        // ✅ DEBUG: Ver qué devuelve el perfil
        //echo "<p>✓ Perfil registrado exitosamente.</p>";


        if ($profileResult) {
            $_SESSION['success_message'] = $result['message'];
            $_SESSION['registered_user'] = $username;

            // Limpiar datos del formulario
            unset($_SESSION['register_errors']);
            unset($_SESSION['register_data']);

            // Redirigir al login
            header('Location: /pages/login/login.php');
            //echo "<p>✓ Registro exitoso. Redirigiendo al inicio de sesión...</p>";
            exit();
        } else {
            throw new Exception('Error al registrar el perfil del usuario: ' . $profileResult);
        }
    } else {
        $_SESSION['register_errors'] = [$result['message']];
        $_SESSION['register_data'] = [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];

        header('Location: ../../index.php?page=error_register_handler');
        //echo "<p>✖ Registro fallido: " . htmlspecialchars($result['message']) . "</p>";
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
    header('Location: /pages/register/register.php');
    //echo "<p>✖ Registro fallido: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
