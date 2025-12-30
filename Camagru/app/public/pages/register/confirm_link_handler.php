<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
require_once __DIR__ . '/../../database/pending_registration.php';
include_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
try {
    $pendingReg = new pendingRegistration();
    // Support both GET (from email link) and POST (from form) methods
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener datos del enlace
        if (!isset($_GET['username']) || !isset($_GET['token'])) {
            error_log("Missing parameters: " . json_encode($_GET));
            throw new Exception('Invalid parameters. Please use the registration form.');
        }
        // Obtener datos del formulario
        $confirmValidationToken = $_GET['token'];
        $register_data = $pendingReg->getPendingRegistrationByToken($confirmValidationToken);
        $username = $_GET['username'];
        if (!$register_data || $register_data['username'] !== $username) {
            error_log("Parameter mismatch or registration not found: " . json_encode($_GET) . ", found: " . json_encode($register_data));
            throw new Exception('Parameters do not match with the pending registration. Please use the registration form.');
        }
        $validationToken = $register_data['validation_token'] ?? 'n/a';
    } else {
        error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Invalid request method. Please use the registration form.');
    }
    // Validar que los tokens coincidan
    if ($validationToken !== $confirmValidationToken) {
        error_log("Token mismatch: validationToken=" . json_encode($validationToken) . ", confirmValidationToken=" . json_encode($confirmValidationToken));
        throw new Exception('Token not match. Please use the link provided in the email.');
    }
    // Registrar al usuario
    $user = new User();
    $result = $user->copyRegisterFromPending($register_data['username']);
    // Verificar el resultado del registro en caso de que falle volver a registro
    if (!$result) {
        $_SESSION['success'] = false;
        $_SESSION['error_messages'] = "Error al copiar el registro pendiente. Por favor, intenta registrarte de nuevo.";
        $_SESSION['register_data'] = $register_data;
        $_SESSION['fromRegister'] = true;
        header('Location: /pages/register/register.php');
        exit();
    }
    // Eliminar el registro pendiente y redirigir al login
    $pendingReg->deletePendingRegistration($register_data['username'], $register_data['email']);
    $_SESSION['success'] = true;
    $_SESSION['success_messages'] = ['Registro exitoso. Por favor, inicia sesión.'];
    header('Location: /pages/register/welcome.html');
} catch (Exception $e) {
    // En caso de error, redirigir al formulario de confirmación con mensaje de error
    error_log("Exception during registration confirmation: " . $e->getMessage());
    $_SESSION['success'] = false;
    $_SESSION['error_messages'] = ['Error: ' . $e->getMessage()];
    header('Location: /pages/register/register.php');
    exit();
}
