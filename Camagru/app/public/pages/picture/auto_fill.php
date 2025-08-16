<?php
$autofilllog = '/tmp/debug_auto_fill.log';
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
        curl_close($ch);
        file_put_contents($autofilllog, "Received data: " . print_r($comment, true) . "\n", FILE_APPEND);
        $commentData = json_decode($comment, true);
        $caption =  $commentData['caption'] ?? '';
        echo json_encode(['success' => true, 'caption' => $caption]);
    } else {
        file_put_contents($autofilllog, "Raw output shell_exec: " . print_r($comment, true) . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'caption' => 'picture data not detected', 'error' => 'No picture provided']);
    }
} else {
    echo json_encode(['success' => false, 'caption' => 'Invalid request method', 'error' => 'Invalid request method']);
}
