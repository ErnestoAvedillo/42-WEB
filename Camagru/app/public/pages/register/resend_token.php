<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../utils/send_mail.php';
require_once __DIR__ . '/../../database/pending_registration.php';
// Log the incoming GET parameters for debugging
$email = $_SESSION['register_data']['email'];
$username = $_SESSION['register_data']['username'];

$pendingRegistration = new pendingRegistration();
$newToken = send_validation_token($email, $username);
$pendingRegistration->resend_token($username, $email, $newToken);

$_SESSION['register_data']['token'] = $newToken;
unset($_SESSION['error_messages']);
header('Location: /pages/register/confirm.php');
$_SESSION['info_messages'] = 'Se ha enviado un nuevo código de validación a su correo electrónico. Por favor, revise su bandeja de entrada.';
exit();
