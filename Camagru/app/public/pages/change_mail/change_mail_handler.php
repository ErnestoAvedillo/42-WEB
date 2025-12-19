<?php
require_once __DIR__ . '/../../database/pending_registration.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../EnvLoader.php';
require_once __DIR__ . '/../../utils/send_mail.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== ($_GET['csrf_token'] ?? '')) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
  exit();
}
// use PHPMailer\PHPMailer\Exception;

$autofilling = '/tmp/change_mail_handler.log';
// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  $_SESSION['error_messages'] = ['Método no permitido. Por favor, utiliza el formulario de registro.'];
  $_SESSION['register_data'] = [
    'username' => $_GET['username'] ?? '',
    'email' => $_GET['email'] ?? '',
    'first_name' => $_GET['first_name'] ?? '',
    'last_name' => $_GET['last_name'] ?? ''
  ];

  // header('Location: /pages/register/register.php');
  header('Content-Type: application/json'); // Indicar error en la respuesta
  echo json_encode(['success' => false]);
  exit();
}
file_put_contents($autofilling, "Register ==> change_mail_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Full GET data: " . print_r($_GET, true) . "\n", FILE_APPEND);
// Obtener datos del formulario
$new_email = trim($_GET['email'] ?? '');
$old_email = trim($_GET['current_email'] ?? '');
$username = $_SESSION['username'] ?? '';
// Validaciones básicas
$errors = [];

$Users = new User();
$pendingReg = new pendingRegistration();
file_put_contents($autofilling, "Register ==> change_mail_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " make checks 1\n", FILE_APPEND);
if ($Users->isEmailTaken($new_email)) {
  $errors[] = 'El email ya está en uso';
}

if (empty($new_email)) {
  $errors[] = 'El email es requerido';
}

$new_email = filter_var($new_email, FILTER_SANITIZE_EMAIL);
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'El email proporcionado no es válido';
}

// Si hay errores, regresar al formulario
file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Errors: " . json_encode($errors) . "\n", FILE_APPEND);
if (!empty($errors)) {
  $_SESSION['error_messages'] = $errors;
  $_SESSION['new_email'] = $new_email;
  // header('Location: /pages/register/register.php');
  header('location: /pages/change_mail/change_mail.php');
  exit();
}

// Enviar correo de validación
try {
  require_once __DIR__ . '/../../EnvLoader.php';
  $ipAddress = EnvLoader::get('APP_ADDR');
  $portAddress = EnvLoader::get('APP_PORT');
  // Enviar correo con link o token según el botón presionado
  file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Sending code email to: " . json_encode($new_email) . " from: http://" . $ipAddress . ":" . $portAddress . "\n", FILE_APPEND);
  // Enviar correo con token de validación
  $validationToken = send_validation_token($new_email, $username);
  if ($validationToken === null) {
    //Si ha fallado el envío del correo generar una excepcion
    file_put_contents($autofilling, "Failed to send validation email.\n", FILE_APPEND);
    header('location: /pages/change_mail/change_mail.php');
    exit();
  }
} catch (Exception $e) {
  $_SESSION['error_messages'] = ['Error al enviar el correo de confirmación: ' . $e->getMessage()];
  $_SESSION['register_data'] =  $new_email;
  // header('Location: /pages/register/register.php');
  header('location: /pages/change_mail/change_mail.php');
  exit();
}

// Si se presionó el botón secundario (enviar link), responder con JSON para evitar redirección
if (isset($_GET['send_link'])) {
  file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Registration pending link created successfully for user: " . json_encode($new_email) . "\n", FILE_APPEND);
  header('location: /pages/change_mail/change_mail.php');
  exit();
}
file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Registration pending created successfully for user: " . json_encode($new_email) . "\n", FILE_APPEND);
// Almacenar datos en la sesión en lugar de pasarlos por URL
$_SESSION['change_mail_data'] = [
    'old_email' => $old_email,
    'new_email' => $new_email,
    'validation_token' => $validationToken
];
// Redirigir a la página de confirmación sin exponer datos sensibles en la URL
header('Location: /pages/change_mail/change_mail_confirm.php');
exit();
