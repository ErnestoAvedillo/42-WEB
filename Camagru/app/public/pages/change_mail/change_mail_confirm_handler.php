<?php
include_once '../../class_session/session.php';
SessionManager::getInstance();
require_once '../../database/User.php';
require_once '../../database/Profiles.php';
require_once '../../database/pending_registration.php';
try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Please use the registration form.');
    }
    //Recuperar datos del formulario
    $validationToken = $_POST['validation_token'] ?? 'n/a';
    $confirmValidationToken = $_POST['confirm_validation_token'] ?? 'n/b';
    $old_email = $_POST['old_email'] ?? '';
    $new_email = $_POST['new_email'] ?? '';
    // Validar que los tokens coincidan
    if ($validationToken !== $confirmValidationToken) {
        throw new Exception('Token not match. Please try again.');
    }
    // Registrar al usuario
    $user = new User();
    if ($user->setUserEmailByOldEmail($old_email, $new_email)) {
        // Limpiar los datos de cambio de email de la sesión
        unset($_SESSION['change_mail_data']);
        $_SESSION['success_message'] = 'Email changed successfully!';
    } else {
        error_log("Register ==> change_mail_confirm_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Failed to update user email: " . json_encode($new_email) . "\n", FILE_APPEND);
        throw new Exception('Failed to update user email.');
    }
    header('Location: /index.php');
} catch (Exception $e) {
    // En caso de error, redirigir al formulario de confirmación con mensaje de error
    $_SESSION['success'] = false;
    $_SESSION['error_messages'] = ['Error: ' . $e->getMessage()];
    error_log("Exception during registration confirmation: " . $e->getMessage());
    header('Location: /pages/change_mail/change_mail_confirm.php');
    exit();
}
