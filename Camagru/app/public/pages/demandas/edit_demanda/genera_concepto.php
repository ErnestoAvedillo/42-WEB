<?php
$autofilling = '/tmp/demandas.log';

file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " Enter in genera_concepto" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

$data = $_POST;
file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " Data received: " . json_encode($data) . "\n", FILE_APPEND);
$lista_facturas = $data['lista_facturas'] ?? null;
$acreedor = ["nombre" => $data['acreedor_nombre'], "cif" => $data['acreedor_cif'], "domicilio" => $data['acreedor_domicilio'], "telefono" => $data['acreedor_telefono'], "fax" => $data['acreedor_fax'], "email" => $data['acreedor_email'], "representante_legal" => $data['acreedor_representante_legal']];
$deudor = ["nombre" => $data['deudor_nombre'], "cif" => $data['deudor_cif'], "domicilio" => $data['deudor_domicilio'], "telefono" => $data['deudor_telefono'], "fax" => $data['deudor_fax'], "email" => $data['deudor_email'], "representante_legal" => $data['deudor_representante_legal']];
if ($lista_facturas && is_string($lista_facturas)) {
    file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " Convertir el string JSON en array PHP: " . $lista_facturas . "\n", FILE_APPEND);
    $lista_facturas = json_decode($lista_facturas, true);
}
file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " listado de facturas: " . json_encode($lista_facturas) . "\n", FILE_APPEND);
if (!$lista_facturas) {
    file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " No hay facturas para generar concepto." . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo "No hay facturas para generar concepto.";
    exit();
} else {
    $postFields = json_encode(['acreedor' => $acreedor, 'deudor' => $deudor, 'lista_facturas' => $lista_facturas]);
    file_put_contents($autofilling, "GeneraConcepto: " . date('Y-m-d H:i:s') . " Enviando facturas a servicio de concepto." . $postFields . "\n", FILE_APPEND);
    $ch = curl_init('http://python:6000/concepto');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // capture response instead of printing it
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    // Aquí puedes procesar la lista de facturas y generar el concepto
    echo ($response);
    exit();
}
