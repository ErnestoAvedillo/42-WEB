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

$parts = [
  ['text' => $data['prompt']]
];
$images = $data['images'];
// Prepare data for llm
foreach ($images as $key => $image) {
  preg_match('/^data:(.*?);base64,(.*)$/', $image['img'], $matches);
  $mime = $matches[1];
  $base64Data = $matches[2];
  if (!$base64Data) {
    continue; // Skip if no data
  }
  $parts[] = [
    'inline_data' => [
      'data' => $base64Data,
      'mime_type' => $mime,
    ]
  ];
}
// Call the Python script
$model = getenv('IMAGE_MODEL');
$apiKey = getenv('GOOGLE_API_KEY');
if (!$apiKey) {
  file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " JSON decode error: " . json_last_error_msg() . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'caption' => 'API key not set', 'error' => 'API key not set']);
  exit;
}
$url = "https://generativelanguage.googleapis.com/v1beta/" . $model . ":generateContent?key=" . $apiKey;
$payload = [
  "system_instruction" => [
    "parts" => [
      ["text" => "Actúa como un generador de contenido visual puro. Tu respuesta debe contener única y exclusivamente una imagen. Está terminantemente prohibido incluir saludos, explicaciones, comentarios, etiquetas de Markdown (como ```) o cualquier texto adicional."]
    ]
  ],
  "contents" => [
    [
      "parts" => $parts
    ]
  ]
];
file_put_contents($autofilling, "Paso \n", FILE_APPEND);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Content-Length: ' . strlen(json_encode($payload))
]);

file_put_contents($autofilling, "Paso 1\n", FILE_APPEND);

$response = curl_exec($ch);
file_put_contents($autofilling, "CURL response: " . substr($response, 0, 500) . "\n", FILE_APPEND);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode !== 200) {
  file_put_contents($autofilling, "API error (Código $httpCode): " . $response . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => "API error (Código $httpCode): " . $response]);
  unset($ch);
  exit;
}
file_put_contents($autofilling, "Paso 2\n", FILE_APPEND);
unset($ch);
file_put_contents($autofilling, "Response from LLM: " . substr($response, 0, 500) . "\n", FILE_APPEND);

// The response is already JSON, just decode it
$result = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  $error_msg = json_last_error_msg();
  file_put_contents($autofilling, "JSON decode error: " . $error_msg . "\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'JSON decode error: ' . $error_msg]);
  exit;
}

$image = null;
$mimetype = null;
$response_parts = $result['candidates'][0]['content']['parts'] ?? [];
foreach ($response_parts as $part) {
    if (isset($part['inlineData'])) {
        $image = $part['inlineData']['data'] ?? null;
        $mimetype = $part['inlineData']['mimeType'] ?? null;
        break; // Salimos del bucle una vez que encontramos la imagen
    }
}
if (!$image) {
  file_put_contents($autofilling, "No images found in response\n", FILE_APPEND);
  echo json_encode(['success' => false, 'message' => 'No images found in response']);
  exit;
}
// Return the decoded response directly (not double-encoded)
$imageData = [
  'img' => 'data:' . $mimetype . ';base64,' . $image,
];
$response = json_encode(['success' => true, 'images' => [$imageData]]);
echo $response;
exit;
