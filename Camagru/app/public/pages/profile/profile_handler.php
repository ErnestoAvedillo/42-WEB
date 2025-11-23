<?php

include __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/login/login.php');
    //echo "login_handler.php: Método no permitido";
    exit();
}

// Obtener datos del formulario
$data = $_POST;
// CSRF token check
if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
    $_SESSION['errors'] = 'Invalid CSRF token.';
    header('Location: /pages/profile/profile.php');
    exit();
}

if (!isset($_SESSION['uuid']) || empty($_SESSION['uuid'])) {
    header('Location: /pages/login/login.php');
    exit();
}

$username = trim($data['username'] ?? '');
$email = $data['email'] ?? '';
if (empty($username) || empty($email)) {
    $_SESSION['errors'] = 'Username and email are required.';
    header('Location: /pages/profile/profile.php');
    exit();
}
$userInstance = new User();
$user = $userInstance->getUserByUUID($_SESSION['uuid']);

if ($userInstance->isUsernameTaken($username, $user['uuid'])) {
    $_SESSION['errors'] = 'Username is already taken.';
    header('Location: /pages/profile/profile.php');
    exit();
}
if ($userInstance->isEmailTaken($email, $user['uuid'])) {
    $_SESSION['errors'] = 'Email is already taken.';
    header('Location: /pages/profile/profile.php');
    exit();
}
// Actualizar datos del perfil

$file = $_FILES['profile_picture'] ?? null;
if ($file != null && $file['error'] === UPLOAD_ERR_OK) {
    $documentDB = new DocumentDB('uploads');
    try {
        $result = $documentDB->uploadFile($file, $_SESSION['uuid']);
    } catch (Exception $e) {
        $_SESSION['errors'] = 'Error uploading photo: ' . $e->getMessage();
        header('Location: /pages/profile/profile.php');
        exit();
    }
} elseif ($file === null) {
    $result = null; // No file uploaded, proceed without updating photo
} elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
    $_SESSION['errors'] = 'Error uploading photo: ' . $file['error'];
    header('Location: /pages/profile/profile.php');
    exit();
}
$profileData = [
    'username' => $username,
    'email' => $email,
    'send_notifications' => isset($data['send_notifications']) ? true : false,
    'first_name' => $data['first_name'] ?? '',
    'last_name' => $data['last_name'] ?? '',
    'national_id_nr' => $data['national_id'] ?? '',
    'nationality' => $data['nationality'] ?? '',
    'date_of_birth' => $data['birth_date'] ?? '',
    'street' => $data['street'] ?? '',
    'city' => $data['city'] ?? '',
    'state' => $data['state'] ?? '',
    'zip_code' => $data['zip_code'] ?? '',
    'country' => $data['country'] ?? '',
    'phone_number' => $data['phone_number'] ?? '',
    'profile_uuid' => $result ?? ''
];
try {
    $userInstance->updateUserProfile($_SESSION['uuid'], $profileData);
    header('Location: /index.php');
} catch (Exception $e) {
    // Manejar errores
    $_SESSION['errors'] = 'Error al actualizar el perfil: ' . $e->getMessage();
    header('Location: /pages/login/login.php');
    exit();
}
