<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}
require_once __DIR__ . '/../../database/mongo_db.php'; // Adjust path since we're in upload_handler.php
//$client = new DocumentDB();
//$client->connect();
//$collection = $client->getCollection();
//echo "<pre>";
//echo SessionManager::getSessionKey('uuid') . "<br>";
//var_dump($_SESSION);
//echo "</pre>";
// Check if a file was uploaded
$typeFile = $_GET['type'] ?? 'uploads'; // Default to 'uploads' if not specified
$file = $_FILES['file'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    if ($file['error'] === UPLOAD_ERR_OK) {
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
        $user_uuid = $_SESSION['uuid'] ?? null; // Replace with actual user UUID if needed
        if (!$user_uuid) {
            $_SESSION['error_messages'] = ["User not logged in or not found in this session."];
            header('Location: /pages/login/login.php');
        }
        $result = $documentDB->uploadFile($file, $user_uuid); // Pass the file path and user UUID
        $_SESSION['success_message'] = "File uploaded successfully. ID: " . $result;
        header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
    } else {
        $_SESSION['error_messages'] = ["File upload error: " . $file['error']];
        header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
    }
} else {
    $_SESSION['error_messages'] = ["No file uploaded. Please try again."];
    header('Location: /pages/upload/upload.php?type=' . urlencode($typeFile));
    //echo "No file uploaded.";
}
exit();
