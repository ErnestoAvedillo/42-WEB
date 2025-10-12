<?php 
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/login/login.php');
  exit();
}
header('Content-Type: application/json');
$autofilling = '/tmp/combine.log';

// get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);
// check if data is valid
if (empty($data)) {
  echo json_encode(['success' => false, 'message' => 'No data received']);
  exit;
};

foreach ($data as $key => $image) {
  preg_match('/^data:(.*?);base64,(.*)$/', $image['img'], $matches);
  $mime = $matches[1];
  $base64Data = $matches[2];
  if (!$base64Data) {
    continue; // Skip if no data
  }
  // Decode the base64 data and create an image resource
  $decodedData = base64_decode($base64Data);
  $img = imagecreatefromstring($decodedData);
  file_put_contents(
    $autofilling,
    "Comnime ==> magic_combine- image dimensions: " . imagesx($img) . "x" . imagesy($img) .
      ", left: " . $image['left'] . ", top: " . $image['top'] .
      ", width: " . $image['width'] . ", height: " . $image['height'] . ")\n",
    FILE_APPEND
  );
  if ($img === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid image data']);
    exit;
  }
}
// Prepare data for Python script
$pythonData = [];
foreach ($data as $image) {
  $pythonData[] = [
    'img' => $image['img']
  ];
}

// Call the Python script
$ch = curl_init('http://python:6000/magic_combine');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,$pythonData);

$response = curl_exec($ch);
if (curl_errno($ch)) {
  $error_msg = curl_error($ch);
  file_put_contents($autofilling, "Curl error: " . $error_msg . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'Curl error: ' . $error_msg]);
  exit;
}
curl_close($ch);
file_put_contents($autofilling, "Response from Python: " . substr($response, 0, 500) . "\n", FILE_APPEND);
$responseData = json_decode(['success' => true, 'image' => $response]);
if (json_last_error() !== JSON_ERROR_NONE) {
  $error_msg = json_last_error_msg();
  file_put_contents($autofilling, "JSON decode error: " . $error_msg . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'JSON decode error: ' . $error_msg]);
  exit;
}
echo json_encode($responseData);
exit;
?>

