<?php
require_once __DIR__ . '/../../database/mongo_db.php'; // Adjust path since we're in upload_handler.php
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
// MongoDB connection
$client = new PictureDB();
$client->connect();
$collection = $client->getCollection();
//echo "<pre>";
//echo SessionManager::getSessionKey('uuid') . "<br>";
//var_dump($_FILES);
//echo "</pre>";
// Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $fileData = file_get_contents($file['tmp_name']);
        $mimeType = mime_content_type($file['tmp_name']);
        $user_uuid = $_POST['user_uuid'] ?? null; // Replace with actual user UUID if needed
        if (!$user_uuid) {
            //echo "<script>alert('User UUID not found in this session.');</script>";
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
        header('Location: /pages/gallery/gallery.php');
        //echo "File uploaded successfully. ID: " . $result->getInsertedId();
    } else {
        //echo "<script>alert('File upload error: " . $file['error'] . "');</script>";
        header('Location: /pages/gallery/gallery.php');
    }
} else {
    //echo "<script>alert('No file uploaded.');</script>";
    header('Location: /pages/upload/upload.php');
    //echo "No file uploaded.";
}
exit();
