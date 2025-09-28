<?php
require_once __DIR__ . '/../../database/pending_registration.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../EnvLoader.php';
require_once __DIR__ . '/../../utils/send_mail.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();

// use PHPMailer\PHPMailer\Exception;

$autofilling = '/tmp/Camagru.log';
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
$pendingReg = new pendingRegistration();
file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " username: " . json_encode($username) . " email: " . json_encode($email) . "\n", FILE_APPEND);

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

if ($pendingReg->usernameMailExists($username, $email)) {
  $pendingReg->deletePendingRegistration($username, $email);
}
if ($pendingReg->emailExists($email)) {
  $errors[] = 'Ya existe un registro pendiente con este email. Por favor, revisa tu correo para completar el registro o utiliza otro email.';
}
if ($pendingReg->usernameExists($username)) {
  $errors[] = 'Ya existe un registro pendiente con este nombre de usuario. Por favor, utiliza otro nombre de usuario.';
}
// Si hay errores, regresar al formulario
file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Errors: " . json_encode($errors) . "\n", FILE_APPEND);
if (!empty($errors)) {
  $_SESSION['error_messages'] = $errors;
  $_SESSION['register_data'] = [
    'username' => $username,
    'email' => $email,
    'first_name' => $firstName,
    'last_name' => $lastName
  ];
  // header('Location: /pages/register/register.php');
  header('Content-Type: application/json'); // Indicar error en la respuesta
  echo json_encode(['success' => false]);
  exit();
}

// Enviar correo de validación
try {
  require_once __DIR__ . '/../../EnvLoader.php';
  $ipAddress = EnvLoader::get('APP_ADDR');
  $portAddress = EnvLoader::get('APP_PORT');
  file_put_contents($autofilling, "Register ==> register_handler.php - fromRegister: " . date('Y-m-d H:i:s') . " Sending validation email to: " . json_encode($email) . " from: http://" . $ipAddress . ":" . $portAddress . "\n", FILE_APPEND);
  // Enviar correo con link o token según el botón presionado
  if (isset($_GET['secondaryBtn'])) {
    // Enviar correo con link de validación
    $validationToken = send_validation_link($email, $username);
  } else {
    // Enviar correo con token de validación
    $validationToken = send_validation_token($email, $username);
  }
  if ($validationToken === null) {
    //Si ha fallado el envío del correo generar una excepcion
    file_put_contents($autofilling, "Failed to send validation email.\n", FILE_APPEND);
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
  // header('Location: /pages/register/register.php');
  header('Content-Type: application/json'); // Indicar error en la respuesta
  echo json_encode(['success' => false]);
  exit();
}
// Guardar registro pendiente
$pendingReg = new pendingRegistration();
if (!$pendingReg->createPendingRegistration($username, $email, $password, $firstName, $lastName, $validationToken)) {
  // Si hay error al guardar, regresar al formulario
  $_SESSION['error_messages'] = ['Error al guardar el registro pendiente. Por favor, inténtalo de nuevo más tarde.'];
  $_SESSION['register_data'] = [
    'username' => $username,
    'email' => $email,
    'first_name' => $firstName,
    'last_name' => $lastName
  ];
  // header('Location: /pages/register/register.php');
  header('Content-Type: application/json'); // Indicar error en la respuesta
  echo json_encode(['success' => false]);
  exit();
}
// Si se presionó el botón secundario (enviar link), responder con JSON para evitar redirección
if (isset($_GET['secondaryBtn'])) {
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
  exit();
}
// Redirigir a la página de confirmación en el caso de que reuiera confirmacion por token
header('Location: /pages/register/confirm.php?username=' . urlencode($username) . '&validation_token=' . urlencode($validationToken));
