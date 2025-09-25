<?php
include_once '../../class_session/session.php';
SessionManager::getInstance();
require_once '../../database/User.php';
require_once '../../database/Profiles.php';
require_once '../../database/pending_registration.php';
$autofilling = '/tmp/Camagru.log';
// Intentar registrar al usuario
try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Please use the registration form.');
    }
    //Recuperar datos del formulario
    $pendingReg = new pendingRegistration();
    $validationToken = $_POST['validation_token'] ?? 'n/a';
    $register_data = $pendingReg->getPendingRegistrationByToken($validationToken);
    $confirmValidationToken = $_POST['confirm_validation_token'] ?? 'n/b';
    // Validar los datos obtenidos del formulario
    if (!$register_data) {
        file_put_contents($autofilling, "Register ==> confirm_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Registro no encontrado\n", FILE_APPEND);
        throw new Exception('Invalid token. Please use the registration form.');
    }
    // Validar que los tokens coincidan
    if ($validationToken !== $confirmValidationToken) {
        throw new Exception('Token not match. Please try again.');
    }
    // Registrar al usuario
    $user = new User();
    $result = $user->copyRegisterFromPending($register_data['username']);
    // Verificar el resultado del registro en caso de que falle volver a registro
    if (!$result) {
        $_SESSION['success'] = false;
        $_SESSION['error_messages'] = "Error al copiar el registro pendiente. Por favor, intenta registrarte de nuevo.";
        $_SESSION['register_data'] = $register_data;
        header('Location: /pages/register/register.php');
        exit();
    }
    // Eliminar el registro pendiente y redirigir al login
    $pendingReg->deletePendingRegistration($register_data['username']);
    $_SESSION['success'] = false;
    $_SESSION['success_messages'] = ['Registro exitoso. Por favor, inicia sesión.'];
    header('Location: /pages/login/login.php');
} catch (Exception $e) {
    // En caso de error, redirigir al formulario de confirmación con mensaje de error
    $_SESSION['success'] = false;
    $_SESSION['error_messages'] = ['Error: ' . $e->getMessage()];
    error_log("Exception during registration confirmation: " . $e->getMessage());
    header('Location: /pages/register/confirm.php?error=validation_token_mismatch&validation_token=' . urlencode($validationToken));
    exit();
}
