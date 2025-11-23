<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}
$autofilling = '/tmp/camagru.log';
require_once __DIR__ . '/../../database/mongo_db.php'; // Adjust path since we're in upload_handler.php
//$client = new DocumentDB();
//$client->connect();
//$collection = $client->getCollection();
//echo "<pre>";
//echo SessionManager::getSessionKey('uuid') . "<br>";
//var_dump($_SESSION);
//echo "</pre>";
// Check if a file was uploaded
$user_uuid = $_SESSION['uuid'] ?? null; // Replace with actual user UUID if needed
if (!$user_uuid) {
    $_SESSION['error_messages'] = ["User not logged in or not found in this session."];
    header('Location: /pages/login/login.php');
}
$typeFile = $_GET['type'] ?? 'uploads'; // Default to 'uploads' if not specified
if ($typeFile === 'master') {
    $documentDB = new DocumentDB('masters');
} else if ($typeFile === 'uploads') {
    $documentDB = new DocumentDB('uploads');
} elseif ($typeFile === 'facturas') {
    $documentDB = new DocumentDB('facturas');
} elseif ($typeFile === 'contratos') {
    $documentDB = new DocumentDB('contratos');
} else {
    $documentDB = new DocumentDB('uploads');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_messages'] = ["Invalid CSRF token."];
        header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
        exit();
    }
    if (empty($_FILES['file']['name'][0])) {
        $_SESSION['error_messages'] = ["No file selected. Please choose a file to upload."];
        header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
        exit();
    }

    file_put_contents($autofilling, "Camagru: Upload Handler ==>: " .  print_r($_FILES, true) . "\n", FILE_APPEND);
    foreach ($_FILES['file']['name'] as $key => $filename) {
        $filedata = ['name' => $filename, 'tmp_name' => $_FILES['file']['tmp_name'][$key], 'type' => $_FILES['file']['type'][$key], 'error' => $_FILES['file']['error'][$key], 'size' => $_FILES['file']['size'][$key]];
        $result = $documentDB->uploadFile($filedata, $user_uuid); // Pass the file path and user UUID
        if ($_FILES['file']['error'][$key] !== UPLOAD_ERR_OK) {
            $_SESSION['error_messages'] = ["File upload error: " . $_FILES['file']['error'][$key]];
            header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
        }
    }
} else {
    $_SESSION['error_messages'] = ["No file uploaded. Please try again."];
    header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
    //echo "No file uploaded.";
}
$_SESSION['success_message'] = "File/s uploaded successfully.";
header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
exit();
