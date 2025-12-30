<?php
require_once __DIR__ . '/../../database/pending_registration.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../EnvLoader.php';
require_once __DIR__ . '/../../utils/send_mail.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();

// use PHPMailer\PHPMailer\Exception;

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  if (isset($_GET['send_link'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Por favor, utiliza el formulario de registro.']);
    exit();
  }
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
$pendingReg = new pendingRegistration();

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
if (!empty($errors)) {
  if (isset($_GET['send_link'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit();
  }
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

// Enviar correo de validación
try {
  require_once __DIR__ . '/../../EnvLoader.php';
  $ipAddress = EnvLoader::get('APP_ADDR');
  $portAddress = EnvLoader::get('APP_PORT');
  // Enviar correo con link o token según el botón presionado
  if (isset($_GET['send_link'])) {
    // Enviar correo con link de validación
    $validationToken = send_validation_link($email, $username);
  } else {
    // Enviar correo con token de validación
    $validationToken = send_validation_token($email, $username);
  }
  if ($validationToken === null) {
    //Si ha fallado el envío del correo generar una excepcion
    throw new Exception('Error al generar o enviar el correo electrónico de validación. Por favor, inténtalo de nuevo más tarde.');
  }
} catch (Exception $e) {
  if (isset($_GET['send_link'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo de confirmación: ' . $e->getMessage()]);
    exit();
  }
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
// Guardar registro pendiente
$pendingReg = new pendingRegistration();
if (!$pendingReg->createPendingRegistration($username, $email, $password, $firstName, $lastName, $validationToken)) {
  // Si hay error al guardar, regresar al formulario
  if (isset($_GET['send_link'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al guardar el registro pendiente. Por favor, inténtalo de nuevo más tarde.']);
    exit();
  }
  $_SESSION['error_messages'] = ['Error al guardar el registro pendiente. Por favor, inténtalo de nuevo más tarde.'];
  $_SESSION['register_data'] = [
    'username' => $username,
    'email' => $email,
    'first_name' => $firstName,
    'last_name' => $lastName
  ];
  header('Location: /pages/register/register.php');
  exit();
}
// Si se presionó el botón secundario (enviar link), responder con JSON para evitar redirección
if (isset($_GET['send_link'])) {
  header('Content-Type: application/json');
  echo json_encode(['success' => true]);
  exit();
}
// Redirigir a la página de confirmación en el caso de que reuiera confirmacion por token
$_SESSION['register_data'] = [
  'username' => $username,
  'email' => $email,
  'first_name' => $firstName,
  'last_name' => $lastName,
  'token' => $validationToken
];
header('Location: /pages/register/confirm.php');
exit();
