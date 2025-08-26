<?php
require_once __DIR__ . '/../../database/facturas.php';
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/class_session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}

$autofilling = '/tmp/debug_demand_handler.log';

function convertDateFormat($dateString)
{
    $date = DateTime::createFromFormat('d-m-Y', $dateString);
    if ($date) {
        return $date->format('d-m-Y');
    }
    return null;
}

function convertStringToNumeric($string)
{
    $string = preg_replace('/,/', '.', $string);
    $number = preg_replace('/[^0-9.]/', '', $string);
    return $number !== '' ? (float)$number : null;
}

header('Content-Type: application/json');
//echo '<script> alert("Paso por aqui");</script>';
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
$maxBytes = 10 * 1024 * 1024; // 10 MB
if (file_exists($autofilling)) {
    unlink($autofilling);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    file_put_contents($autofilling, "Invalid request method\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'Invalid request method']);
    exit();
}

if (!isset($_FILES['factura'])) {
    file_put_contents($autofilling, "No file uploaded\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'No file uploaded']);
    exit();
}

$file = $_FILES['factura'];
// foreach ($file as $key => $value) {
//     file_put_contents($autofilling, "File upload error: " . $key . " => " . $value . "\n", FILE_APPEND);
// }
if ($file['error'] !== UPLOAD_ERR_OK) {
    file_put_contents($autofilling, "File upload error: " . $file['error'] . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'File upload error: ' . $file['error']]);
    exit();
}

if ($file['size'] > $maxBytes) {
    file_put_contents($autofilling, "File size exceeds limit: " . $file['size'] . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'File size exceeds limit of 10MB.']);
    exit();
}

$tmpFilePath = $file['tmp_name'];
$fileData = file_get_contents($tmpFilePath);
$mimetype = mime_content_type($tmpFilePath);
if (!in_array($mimetype, $allowedTypes)) {
    file_put_contents($autofilling, "Invalid file type: " . $mimetype . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'Invalid file type.']);
    exit();
}

$postFields = [
    // Pass the temporary file path so PHP will send a proper multipart/form-data file upload
    'factura' => new CURLFile($tmpFilePath, $mimetype, $file['name'])
];

$ch = curl_init('http://python:6000/factura');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // capture response instead of printing it

file_put_contents($autofilling, "Uploading file: " . $file['name'] . " size: " . $file['size'] . " mime: " . $mimetype . "\n", FILE_APPEND);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    $err = curl_error($ch);
    file_put_contents($autofilling, "cURL error: " . $err . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => $err]);
    curl_close($ch);
    exit();
}

file_put_contents($autofilling, "Received response: " . substr($response, 0, 500) . "\n", FILE_APPEND);
curl_close($ch);

$responseData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    file_put_contents($autofilling, "Invalid JSON from Python: " . json_last_error_msg() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'Invalid JSON from Python: ' . json_last_error_msg()]);
    exit();
}

try {
    $fileInstance = new DocumentDB();
    $document_uuid = $fileInstance->uploadFile($file, $_SESSION['uuid']);
} catch (Exception $e) {
    file_put_contents($autofilling, "Failed to upload file to MongoDB: " . $e->getMessage() . " with user " . $_SESSION['uuid'] . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'Failed to upload file to database']);
    exit();
}
try {
    file_put_contents($autofilling, "add document uuid: " . $document_uuid . "\n", FILE_APPEND);
    $responseData['caption']['user_uuid'] = SessionManager::getSessionKey('uuid');
    $responseData['caption']['document_uuid'] = $document_uuid;
    $responseData['caption']['status'] = 'pending';
    $responseData['caption']['factura']['fecha'] = convertDateFormat($responseData['caption']['factura']['fecha']);
    $responseData['caption']['factura']['vencimiento'] = convertDateFormat($responseData['caption']['factura']['vencimiento']);
    $responseData['caption']['factura']['importe_total'] = convertStringToNumeric($responseData['caption']['factura']['importe_total']);
    $responseData['caption']['factura']['importe_iva'] = convertStringToNumeric($responseData['caption']['factura']['importe_iva']);
    $responseData['caption']['factura']['importe_base'] = convertStringToNumeric($responseData['caption']['factura']['importe_base']);
    file_put_contents($autofilling, "finish adding document uuid: " . $document_uuid . "\n", FILE_APPEND);
    $facturasInstance = new Facturas();
    file_put_contents($autofilling, "adding factura information: " . json_encode($responseData['caption']) . "\n", FILE_APPEND);
    $facturasInstance->addFactura($responseData['caption']);
    file_put_contents($autofilling, "finish adding factura information: " . json_encode($responseData['caption']) . "\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($autofilling, "Failed to add factura: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => '', 'error' => 'Failed to add factura']);
    exit();
}
header('Location: /pages/demand/demand.php');
