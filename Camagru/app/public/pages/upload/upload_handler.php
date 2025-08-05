

<?php
require_once __DIR__ . '/../../database/mongo_db.php'; // Adjust path since we're in upload_handler.php


// MongoDB connection
$client = new PictureDB();
$collection = $client->getCollection();


// Check if a file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $fileData = file_get_contents($file['tmp_name']);
        $mimeType = mime_content_type($file['tmp_name']);

        // Store file in MongoDB as a document
        $result = $collection->insertOne([
            'filename' => $filename,
            'filedata' => new MongoDB\BSON\Binary($fileData, MongoDB\BSON\Binary::TYPE_GENERIC),
            'mimetype' => $mimeType,
            'uploaded_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        echo "File uploaded successfully. ID: " . $result->getInsertedId();
    } else {
        echo "File upload error: " . $file['error'];
    }
} else {
    echo "No file uploaded.";
}
?>