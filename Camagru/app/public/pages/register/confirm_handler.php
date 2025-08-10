<?php
include_once '../../class_session/session.php';
SessionManager::getInstance();
require_once '../../database/User.php';
require_once '../../database/Profiles.php';
// Intentar registrar al usuario
try {

    $register_data = $_SESSION['register_data'] ?? [];
    //echo "<p> Username: " . htmlspecialchars($register_data['username'] ?? '') . "</p>";
    //echo "<p> Email: " . htmlspecialchars($register_data['email'] ?? '') . "</p>";
    //echo "<p> Password: " . htmlspecialchars($register_data['password'] ?? '') . "</p>";
    //echo "<p> First Name: " . htmlspecialchars($register_data['first_name'] ?? '') . "</p>";
    //echo "<p> Last Name: " . htmlspecialchars($register_data['last_name'] ?? '') . "</p>";
    $validationToken = $_POST['validation_token'] ?? '';
    $confirmValidationToken = $_POST['confirm_validation_token'] ?? '';
    if (empty($validationToken) || empty($confirmValidationToken)) {
        throw new Exception('El token de validación es requerido');
        header('Location: /pages/register/confirm.php?error=validation_token_required');
    }
    if ($validationToken !== $confirmValidationToken) {
        throw new Exception('Los tokens de validación no coinciden');
        header('Location: /pages/register/confirm.php?error=validation_token_mismatch');
    }
    $user = new User();
    $result = $user->register($register_data['username'], $register_data['email'], $register_data['password'], $register_data['first_name'], $register_data['last_name']);
    // ✅ DEBUG: Ver qué devuelve el registro
    //error_log("Register result: " . json_encode($result));
    //echo "<p>Register result: " . json_encode($result) . "</p>";
    if (!$result['success']) {
        $_SESSION['error_messages'] = [$result['message']];
        $_SESSION['register_data'] = $register_data;
        header('Location: /pages/register/register.php');
        exit();
    }
    $_SESSION['success_message'] = ['Registro exitoso. Por favor, inicia sesión.'];
    header('Location: /pages/login/login.php');
} catch (Exception $e) {
    $_SESSION['error_messages'] = ['Error del servidor: ' . $e->getMessage()];
    $_SESSION['register_data'] = [
        'username' => $_SESSION['register_data']['username'] ?? '',
        'email' => $_SESSION['register_data']['email'] ?? '',
        'first_name' => $_SESSION['register_data']['first_name'] ?? '',
        'last_name' => $_SESSION['register_data']['last_name'] ?? ''
    ];
    header('Location: /pages/register/register.php');
    //echo "<p>✖ Registro fallido: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
