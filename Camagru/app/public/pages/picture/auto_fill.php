<?php
$autofilllog = '/tmp/facturas.log';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contents = file_get_contents('php://input');
    $data = json_decode($contents, true);
    $comment = "";
    $picture = $data['picture'] ?? null;
    if ($picture) {
        $postData = json_encode(['picture' => $picture]);
        $ch = curl_init('http://python:6000/autofill');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ]);
        $comment = curl_exec($ch);
        file_put_contents($autofilllog, "Autofill: " . date('Y-m-d H:i:s') . " CURL error: " . $comment . "\n", FILE_APPEND);
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
