<?php
require_once __DIR__ . '/../../utils/convert_string2date.php';
require_once __DIR__ . '/../../database/facturas.php';
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/class_session.php';
SessionManager::getInstance();
$autofilling = '/tmp/facturas.log';
if (file_exists($autofilling)) {
    unlink($autofilling);
}
if (!SessionManager::getSessionKey('uuid')) {
    file_put_contents($autofilling,  "Factura_handler: " . date('Y-m-d H:i:s') . "User not logged in\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Not logged in', 'redirect' => '/pages/request_login/request_login.php']);
    exit();
}
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " Invalid CSRF token\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token', 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }
    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Invalid request method\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Invalid request method', 'redirect' => '/pages/facturas/factura.php']);
    exit();
}

if (!isset($_FILES['factura'])) {
    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "No file uploaded\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'No file uploaded', 'redirect' => '/pages/facturas/factura.php']);
    exit();
}
file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "File uploaded\n" . print_r($_FILES['factura'], true), FILE_APPEND);

foreach ($_FILES['factura']['name'] as $key => $filename) {
    $file = [
        'name' => $_FILES['factura']['name'][$key],
        'type' => $_FILES['factura']['type'][$key],
        'tmp_name' => $_FILES['factura']['tmp_name'][$key],
        'error' => $_FILES['factura']['error'][$key],
        'size' => $_FILES['factura']['size'][$key],
    ];
    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Processing file: " . print_r($file, true) . "\n", FILE_APPEND);
    // $file = $_FILES['factura'];
    // foreach ($file as $key => $value) {
    //     file_put_contents($autofilling, "File upload error: " . $key . " => " . $value . "\n", FILE_APPEND);
    // }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "File upload error: " . $file['error'] . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'File upload error: ' . $file['error'], 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }

    if ($file['size'] > $maxBytes) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "File size exceeds limit: " . $file['size'] . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'File size exceeds limit of 10MB.', 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }

    $tmpFilePath = $file['tmp_name'];
    $fileData = file_get_contents($tmpFilePath);
    $mimetype = mime_content_type($tmpFilePath);
    if (!in_array($mimetype, $allowedTypes)) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Invalid file type: " . $mimetype . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Invalid file type.', 'redirect' => '/pages/facturas/factura.php']);
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

    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Uploading file: " . $file['name'] . " size: " . $file['size'] . " mime: " . $mimetype . "\n", FILE_APPEND);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "cURL error: " . $err . "\n", FILE_APPEND);
        curl_close($ch);
        echo json_encode(['success' => false, 'error' => $err, 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }

    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Received response: " . substr($response, 0, 500) . "\n", FILE_APPEND);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Invalid JSON from Python: " . json_last_error_msg() . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON from Python: ' . json_last_error_msg(), 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }

    try {
        $fileInstance = new DocumentDB('facturas');
        $document_uuid = $fileInstance->uploadFile($file, $_SESSION['uuid']);
    } catch (Exception $e) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Failed to upload file to MongoDB: " . $e->getMessage() . " with user " . $_SESSION['uuid'] . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Failed to upload file to database', 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }
    try {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "add document uuid: " . $document_uuid . "\n", FILE_APPEND);
        $responseData['caption']['user_uuid'] = SessionManager::getSessionKey('uuid');
        $responseData['caption']['document_uuid'] = $document_uuid;
        $responseData['caption']['factura']['fecha'] = convertstring2date($responseData['caption']['factura']['fecha'], 'd/m/Y', 'Y/m/d');
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Converted fecha: " . $responseData['caption']['factura']['fecha'] . "\n", FILE_APPEND);
        $responseData['caption']['factura']['vencimiento'] = convertstring2date($responseData['caption']['factura']['vencimiento'], 'd/m/Y', 'Y/m/d');
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Converted vencimiento: " . $responseData['caption']['factura']['vencimiento'] . "\n", FILE_APPEND);
        $responseData['caption']['factura']['importe_total'] = convertStringToNumeric($responseData['caption']['factura']['importe_total']);
        $responseData['caption']['factura']['importe_iva'] = convertStringToNumeric($responseData['caption']['factura']['importe_iva']);
        $responseData['caption']['factura']['importe_base'] = convertStringToNumeric($responseData['caption']['factura']['importe_base']);
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "finish adding document uuid: " . $document_uuid . "\n", FILE_APPEND);
        $facturasInstance = new Facturas();
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "adding factura information: " . json_encode($responseData['caption']) . "\n", FILE_APPEND);
        $facturasInstance->addFactura($responseData['caption']);
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "finish adding factura information: " . json_encode($responseData['caption']) . "\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Failed to add factura: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Failed to add factura', 'redirect' => '/pages/facturas/factura.php']);
        exit();
    }
}
$response = json_encode(['success' => true, 'redirect' => '/pages/facturas/factura.php']);
file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . "Process completed successfully: " . $response . "\n", FILE_APPEND);
echo $response;
exit;
