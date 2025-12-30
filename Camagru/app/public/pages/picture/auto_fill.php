<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contents = file_get_contents('php://input');
    $data = json_decode($contents, true);
    // CSRF token check
    if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }
    $comment = "";
    $picture = $data['picture'] ?? null;

    // Limpiar los datos de la imagen si vienen en formato data URL
    if ($picture && strpos($picture, 'data:') === 0) {
        // Extraer el MIME type del data URL
        $mimeType = substr($picture, 5, strpos($picture, ';') - 5);
        // Extraer solo los datos base64
        $picture = substr($picture, strpos($picture, ',') + 1);
    } else {
        $mimeType = 'image/jpeg'; // Valor por defecto
    }
    if ($picture) {
        $apiKey = getenv('GOOGLE_API_KEY');
        if (!$apiKey) {
            echo json_encode(['success' => false, 'caption' => 'API key not set', 'error' => 'API key not set']);
            exit;
        }
        $model = getenv('USE_MODEL');
        $url = "https://generativelanguage.googleapis.com/v1beta/" . $model . ":generateContent?key=" . $apiKey;
        // 3. Estructura del Payload para visión multimodal
        $payload = [
            "system_instruction" => [
                "parts" => [
                    ["text" => "Eres un generador de subtítulos estricto. Responde ÚNICAMENTE con el comentario solicitado. Prohibido usar introducciones como 'Claro que sí', 'Aquí tienes' o cualquier saludo."]
                ]
            ],
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Genera solamente un comentario creativo, jocoso y breve para la siguiente foto."],
                        [
                            "inline_data" => [
                                "mime_type" => $mimeType,
                                "data" => $picture
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // 4. Ejecutar la llamada con cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode !== 200) {
            echo json_encode(['success' => false, 'error' => "Error de API (Código $httpCode): " . $response]);
            exit;
        }
        $result = json_decode($response, true);
        // 5. Extraer y devolver el texto
        $caption = $result['candidates'][0]['content']['parts'][0]['text'] ?? FALSE;
        if ($caption === false) {
            $error = curl_error($ch);
            echo json_encode(['success' => false, 'caption' => 'Error in CURL request', 'error' => $error]);
            unset($ch);
            exit;
        }
        unset($ch);
        $returned_text = ['success' => true, 'caption' => $caption];
        echo json_encode($returned_text);
    } else {
        echo json_encode(['success' => false, 'caption' => 'picture data not detected', 'error' => 'No picture provided']);
    }
} else {
    echo json_encode(['success' => false, 'caption' => 'Invalid request method', 'error' => 'Invalid request method']);
}
