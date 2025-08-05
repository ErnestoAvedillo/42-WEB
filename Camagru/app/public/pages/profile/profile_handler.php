<?php

include __DIR__ . '/../../class_session/session.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/login/login.php');
    //echo "login_handler.php: Método no permitido";
    exit();
}

// Obtener datos del formulario
$data = $_POST;

if (!isset($_SESSION['user_uuid']) || empty($_SESSION['user_uuid'])) {
    header('Location: /login');
    exit();
}

$username = trim($data['username'] ?? '');
$email = $data['email'] ?? '';

if (empty($username)) {
    $errors[] = 'El nombre de usuario o email es requerido';
}
$userInstance = new User();
$user = $userInstance->getUserByUUID($_SESSION['user_uuid']);
$username = $user ? $user->username : '';
$email = $user ? $user->email : '';
if ($username !== $user->username || $email !== $user->email) {
    $userInstance->updateUser($_SESSION['user_uuid'], $username, $email);
}

// Actualizar datos del perfil
$profileData = [
    'national_id_nr' => $data['national_id'] ?? '',
    'nationality' => $data['nationality'] ?? '',
    'date_of_birth' => $data['birth_date'] ?? '',
    'street' => $data['street'] ?? '',
    'city' => $data['city'] ?? '',
    'state' => $data['state'] ?? '',
    'zip_code' => $data['zip_code'] ?? '',
    'privacy' => $data['privacy'] ?? 'public'
];
try {
    $profile = new Profiles();
    $profile->updateUserProfile($_SESSION['user_id'], $profileData);
    header('Location: /pages/gallery/gallery.php');
} catch (Exception $e) {
    // Manejar errores
    $_SESSION['errors'] = 'Error al actualizar el perfil: ' . $e->getMessage();
    header('Location: /pages/login/login.php');
    exit();
}
