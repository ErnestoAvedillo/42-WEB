<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/mongo_db.php'; // Adjust path since we're in upload_handler.php
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
// MongoDB connection
$client = new PictureDB();
$client->connect();
$collection = $client->getCollection();
//echo "<pre>";
//echo SessionManager::getSessionKey('uuid') . "<br>";
//var_dump($_SESSION);
//echo "</pre>";
// Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $fileData = file_get_contents($file['tmp_name']);
        $mimeType = mime_content_type($file['tmp_name']);
        $user_uuid = $_SESSION['uuid'] ?? null; // Replace with actual user UUID if needed
        if (!$user_uuid) {
            $_SESSION['error_messages'] = ["User not logged in or not found in this session."];
            header('Location: /pages/login/login.php');
        }
        // Store file in MongoDB as a document
        $result = $collection->insertOne([
            'user_uuid' => $user_uuid, // Replace with actual user UUID if needed
            'filename' => $filename,
            'filedata' => new MongoDB\BSON\Binary($fileData, MongoDB\BSON\Binary::TYPE_GENERIC),
            'mimetype' => $mimeType,
            'uploaded_at' => new MongoDB\BSON\UTCDateTime()
        ]);
        $_SESSION['success_message'] = "File uploaded successfully. ID: " . $result->getInsertedId();
        header('Location: /pages/upload/upload.php');
    } else {
        $_SESSION['error_messages'] = ["File upload error: " . $file['error']];
        header('Location: /pages/upload/upload.php');
    }
} else {
    $_SESSION['error_messages'] = ["No file uploaded. Please try again."];
    header('Location: /pages/upload/upload.php');
    //echo "No file uploaded.";
}
exit();
