<?php
require_once __DIR__ . '/../../../database/demandas.php';

$autofilling = '/tmp/demandas.log';
$data = $_POST;
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!isset($data['csrf_token']) || !$csrf_token || !hash_equals($csrf_token, $data['csrf_token'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit();
}
$user_uuid = $data['user_uuid'] ?? null;
$id = $data['id'] ?? null;

file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Enter in edit_demanda_handler" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
$demandasInstance = new Demandas();
file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Created Demandas instance" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
try {
    $result = $demandasInstance->updateDemanda($id, $user_uuid, $data);
    file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Update result: " . $result . "\n", FILE_APPEND);
    if ($result) {
        file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Update successful" . "\n", FILE_APPEND);
        echo json_encode(['success' => true, 'redirect' => '/pages/demandas/demandas.php']);
    } else {
        file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Update failed" . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Failed to update demanda']);
    }
} catch (Exception $e) {
    file_put_contents($autofilling, "Editdemanda_handler: " . date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}
exit();
