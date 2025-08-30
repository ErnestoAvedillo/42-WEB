<?php
require_once __DIR__ . '/../../../database/facturas.php';
require_once __DIR__ . '/../../../class_session/class_session.php';
SessionManager::getInstance();
$autofilling = '/tmp/debug_edit_factura.log';
if (file_exists($autofilling)) {
    unlink($autofilling);
}
header('Content-Type: application/json; charset=utf-8');
$data = $_POST;
file_put_contents($autofilling, "success receiving factura data" . time() . "\n", FILE_APPEND);
file_put_contents($autofilling, "data: " . json_encode($data) . "\n", FILE_APPEND);
$respond = json_encode(["success" => true, "redirect" => "/pages/facturas/factura.php"]);
file_put_contents($autofilling, "respond: " . $respond . "\n", FILE_APPEND);
echo $respond;
exit();
