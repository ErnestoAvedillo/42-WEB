<?php
require_once __DIR__ . '/../../../database/facturas.php';

$autofilling = '/tmp/facturas.log';

$data = $_POST;
$user_uuid = $data['user_uuid'] ?? null;
$id = $data['id'] ?? null;

$facturas = new Facturas();
file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " success receiving factura data\n", FILE_APPEND);
file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " data received : " . json_encode($data) . "\n", FILE_APPEND);
try {
    $result = $facturas->updateFactura($id, $user_uuid, $data);
    file_put_contents($autofilling, "result of updateFactura: " . $result . "\n", FILE_APPEND);
    if ($result) {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " success receiving factura data\n", FILE_APPEND);
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " data received : " . json_encode($data) . "\n", FILE_APPEND);

        $respond = ["success" => true, "redirect" => "/pages/facturas/factura.php"];
    } else {
        file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " error receiving factura data\n", FILE_APPEND);
        $respond = ["success" => false, "error" => "Failed to update factura"];
    }
} catch (Exception $e) {
    file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " error receiving factura data: " . $e->getMessage() . "\n", FILE_APPEND);
    $respond = ["success" => false, "error" => "Failed to update factura"];
}
file_put_contents($autofilling, "Factura_handler: " . date('Y-m-d H:i:s') . " respond: " . json_encode($respond) . "\n", FILE_APPEND);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($respond);
exit();
