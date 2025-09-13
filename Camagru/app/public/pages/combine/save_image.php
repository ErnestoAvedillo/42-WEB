<?php
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
  echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /Camagru/app/public/pages/login/login.php');
  exit();
}
header('Content-Type: application/json');
// save_image.php

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is valid
if (empty($data)) {
  echo json_encode(['success' => false, 'message' => 'No data received']);
  exit;
}

//I use the first image to get the dimensions of the canvas
$width = $data[0]['width'];
$height = $data[0]['height'];
$top = $data[0]['top'];
$left = $data[0]['left'];

//I create the canvas and fill it with a white background
$canvas = imagecreatetruecolor($width, $height);
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $white);

foreach ($data as $image) {
  preg_match('/^data:(.*?);base64,(.*)$/', $image['img'], $matches);
  $mime = $matches[1];
  $base64Data = $matches[2];
  if (!$base64Data) {
    continue; // Skip if no data
  }
  // Decode the base64 data and create an image resource
  $decodedData = base64_decode($base64Data);
  $img = imagecreatefromstring($decodedData);
  if ($img !== false) {
    // Copy the resized image onto the canvas at the specified position
    $Top2Save = max(0, -$image['top']);
    $Left2Save = max(0, -$image['left']);
    $Wide2Save = min($width, imagesx($img) - $Left2Save);
    $Height2Save = min($height, imagesy($img) - $Top2Save);
    imagecopy($canvas, $img, $Top2Save, $Left2Save, 0, 0, $Wide2Save, $Height2Save);
    imagedestroy($img);
  }
  imagedestroy($img);
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
