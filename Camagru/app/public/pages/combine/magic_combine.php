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

// CSRF token check
if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
  exit;
}
// check if data is valid
if (empty($data)) {
  echo json_encode(['success' => false, 'message' => 'No data received']);
  exit;
};

$images = $data['images'];
$prompt = $data['prompt'];

foreach ($images as $key => $image) {
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
    "Combine ==> magic_combine- image dimensions: " . imagesx($img) . "x" . imagesy($img) .
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
$pythonData = [
  'images' => [],
  'prompt' => $prompt
];
foreach ($images as $image) {
  $pythonData['images'][] = [
    'img' => $image['img']
  ];
}



file_put_contents($autofilling, "Sending data to Python: " . json_encode($pythonData) . "\n", FILE_APPEND);
// Call the Python script
$ch = curl_init('http://python:6000/magic_combine');
file_put_contents($autofilling, "Paso \n", FILE_APPEND);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pythonData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Content-Length: ' . strlen(json_encode($pythonData))
]);

file_put_contents($autofilling, "Paso 1\n", FILE_APPEND);

$response = curl_exec($ch);
file_put_contents($autofilling, "Paso 2\n", FILE_APPEND);
if (curl_errno($ch)) {
  $error_msg = curl_error($ch);
  file_put_contents($autofilling, "Curl error: " . $error_msg . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'Curl error: ' . $error_msg]);
  exit;
}
curl_close($ch);
file_put_contents($autofilling, "Response from Python: " . substr($response, 0, 500) . "\n", FILE_APPEND);

// The response is already JSON, just decode it
$responseData = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  $error_msg = json_last_error_msg();
  file_put_contents($autofilling, "JSON decode error: " . $error_msg . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'JSON decode error: ' . $error_msg]);
  exit;
}

// Return the decoded response directly (not double-encoded)
echo $response;
exit;
