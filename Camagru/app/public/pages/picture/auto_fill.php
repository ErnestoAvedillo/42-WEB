<?php
$autofilllog = '/tmp/facturas.log';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contents = file_get_contents('php://input');
    $data = json_decode($contents, true);
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
    
    foreach ($data as $key => $value) {
        file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " Key: " . $key . " Value: " . (is_string($value) ? substr($value, 0, 100) : print_r($value, true)) . "\n", FILE_APPEND);
    }
    file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " Extracted mime type: " . $mimeType . "\n", FILE_APPEND);
    file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " Clean base64 data: " . substr($picture, 0, 100) . "\n", FILE_APPEND);
    if ($picture) {
        $apiKey = getenv('GOOGLE_API_KEY');
        if (!$apiKey) {
            file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " JSON decode error: " . json_last_error_msg() . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'caption' => 'API key not set', 'error' => 'API key not set']);
            exit;
        }
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;
        // 3. Estructura del Payload para visión multimodal
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Describe esta foto de manera creativa, jocosa y breve."],
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
            file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " API error (Código $httpCode): " . $response . "\n", FILE_APPEND);
            return "Error de API (Código $httpCode): " . $response;
        }
    
        $result = json_decode($response, true);
        file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " API response: " . print_r($result, true) . "\n", FILE_APPEND);

        // 5. Extraer y devolver el texto
        $comment = $result['candidates'][0]['content']['parts'][0]['text'] ?? FALSE;
        file_put_contents($autofilllog, "Autofill comentario: " . date('Y-m-d H:i:s') . " CURL error: " . $comment . "\n", FILE_APPEND);
        if ($comment === false) {
            $error = curl_error($ch);
            file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " CURL error: " . $error . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'caption' => 'Error in CURL request', 'error' => $error]);
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        $commentData = json_decode($comment, true);
        if ($commentData === null) {
            file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " JSON decode error: " . json_last_error_msg() . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'caption' => 'Error decoding JSON', 'error' => json_last_error_msg()]);
            exit;
        }

        file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " Received data: " . print_r($commentData, true) . "\n", FILE_APPEND);
        $caption =  $commentData['caption'] ?? '';
        echo json_encode(['success' => true, 'caption' => $caption]);
    } else {
        file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . "Raw output shell_exec: " . print_r($comment, true) . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'caption' => 'picture data not detected', 'error' => 'No picture provided']);
    }
} else {
    file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . "Invalid request method\n", FILE_APPEND);
    echo json_encode(['success' => false, 'caption' => 'Invalid request method', 'error' => 'Invalid request method']);
}
