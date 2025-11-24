<?php
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/session.php';
require_once __DIR__ . '/correct_orientation.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/login/login.php');
  exit();
}
header('Content-Type: application/json');
$autofilling = '/tmp/combine.log';
// save_image.php

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);
foreach ($data as $key => $value) {
  if (is_string($value)) {
    $logVal = substr($value, 0, 100);
  } else {
    $logVal = print_r($value, true);
  }
  file_put_contents($autofilling, "Save_image.php: " . date('Y-m-d H:i:s') . " Key: " . $key . " Value: " . $logVal . "\n", FILE_APPEND);
}

// CSRF token check
if (!isset($_SESSION['csrf_token'])) {
  echo json_encode(['success' => false, 'message' => 'No CSRF token in session']);
  exit;
}

if (!isset($data['csrf_token'])) {
  echo json_encode(['success' => false, 'message' => 'No CSRF token in data']);
  exit;
}
if (!hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
  exit;
}
if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
  exit;
}


// Check if images data is valid
if (!isset($data['images']) || empty($data['images']) || !is_array($data['images'])) {
  echo json_encode(['success' => false, 'message' => 'No images data received']);
  exit;
}
$images = $data['images'];

//I use the first image to get the dimensions of the canvas
$width = $images[0]['width'];
$height = $images[0]['height'];

//I create the canvas and fill it with a white background
$canvas = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $white);

foreach ($images as $image) {
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
    "Comnime ==> save_image- imagecopyresampled(canvas dimensions: " . imagesx($canvas) . "x" . imagesy($canvas) .
      ", img dimensions: " . imagesx($img) . "x" . imagesy($img) .
      ", left: " . $image['left'] . ", top: " . $image['top'] .
      ", width: " . $image['width'] . ", height: " . $image['height'] . ")\n",
    FILE_APPEND
  );
  if ($img !== false) {
    $img = correctImageOrientation($img, $decodedData); // Correct the orientation
    // Use the provided dimensions and positions
    $Top2Save = isset($image['top']) ? (int)$image['top'] : 0;
    $Left2Save = isset($image['left']) ? (int)$image['left'] : 0;
    $Wide2Save = isset($image['width']) ? (int)$image['width'] : imagesx($img);
    $Height2Save = isset($image['height']) ? (int)$image['height'] : imagesy($img);

    // Resize and copy the image onto the canvas
    imagecopyresampled(
      $canvas,
      $img,
      $Left2Save,
      $Top2Save, // Destination position
      0,
      0, // Source position
      $Wide2Save,
      $Height2Save, // Destination dimensions
      imagesx($img),
      imagesy($img) // Source dimensions
    );
    imagedestroy($img);
  }
}
$documentDB = new DocumentDB('combines');
$documentDB->connect();
$session_uuid = SessionManager::getSessionKey('uuid');
// 1. Prepare image data for MongoDB
// 2. Capture final image in memory (no file save)
ob_start();
imagejpeg($canvas, null, 90);
$imageData = ob_get_clean();
// 3. Insert into MongoDB
try {
  $documentDB->insertCombine($imageData, $session_uuid);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
  exit;
}

imagedestroy($canvas);

// Send a JSON response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Images saved successfully']);
