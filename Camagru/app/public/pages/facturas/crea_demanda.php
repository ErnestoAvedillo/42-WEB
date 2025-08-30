<?php
require_once __DIR__ . '/../../class_session/class_session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}
require_once __DIR__ . '/../../database/facturas.php';
require_once __DIR__ . '/../../database/demandas.php';

//tendremos que añadirlo cuando tengamos incluida la parte javascript
header('Content-Type: text/html; charset=utf-8');

$autofilling = '/tmp/debug_crea_demanda.log';
if (file_exists($autofilling)) {
    unlink($autofilling);
}
file_put_contents($autofilling, "Entro en crea demanda: \n", FILE_APPEND);
$facturasDb = new Facturas();
$demandasDb = new Demandas();
$_user_uuid = SessionManager::getSessionKey('uuid');
//añadir otros filtros en el futuro
$registros = $facturasDb->getAll($_user_uuid, "pending");
file_put_contents($autofilling, "Facturas pendientes: " . json_encode($registros) . "\n", FILE_APPEND);
if (count($registros) == 0) {
    echo json_encode(['success' => false, 'error' => 'No hay facturas pendientes para crear una demanda.']);
    exit();
}
$lista_facturas = [];
$campos_demandas = [];
$importe_total = 0;
foreach ($registros as $factura) {
    $factura_id = $factura['factura_numero'];
    $keys = array_keys($lista_facturas);
    if (in_array($factura_id, $keys)) {
        echo json_encode(['success' => false, 'error' => 'Factura ya procesada en este ciclo: $factura_id, no se añadirá a la demanda.']);
        exit();
        continue;
    }
    if (!$campos_demandas) {
        foreach ($factura as $key => $value) {
            if (strpos($key, 'acreedor') !== false || strpos($key, 'deudor') !== false) {
                file_put_contents($autofilling, "Factura campo: " . $key . " => " . $value . "\n", FILE_APPEND);
                $campos_demandas[$key] = $value;
            }
        }
    }
    $lista_facturas[$factura_id] = [
        'fecha' => $factura['factura_fecha'],
        'vencimiento' => $factura['factura_vencimiento'],
        'importe' => $factura['factura_importe_total'],
        'concepto' => $factura['concepto'],
        'document_uuid' => $factura['document_uuid']
    ];
    $importe_total += $factura['factura_importe_total'];
}    //marcar la factura como en demanda
$campos_demandas['user_uuid'] = $_user_uuid;
$campos_demandas['importe_total_deuda'] = $importe_total;
$campos_demandas['lista_facturas'] = json_encode($lista_facturas);
$campos_demandas['created_at'] = date('Y-m-d H:i:s');
file_put_contents($autofilling, "Campos demandas 4 " . json_encode($campos_demandas) . " como 'in_demand'\n", FILE_APPEND);
file_put_contents($autofilling, "Factura añadida a la demanda: " . json_encode($lista_facturas) . "\n", FILE_APPEND);
file_put_contents($autofilling, "Inicio crear Demanda. \n", FILE_APPEND);
$demandasDb->addDemand($campos_demandas);
file_put_contents($autofilling, "Demanda creada. \n", FILE_APPEND);
foreach ($registros as $factura) {
    file_put_contents($autofilling, "Factura " . $factura['id'] . " marcandose en demanda \n", FILE_APPEND);
    $success = $facturasDb->updateStatus($factura['id'], 'in_demand');
    file_put_contents($autofilling, "Factura " . $factura['id'] . " marcada como en demanda:" . $success . " \n", FILE_APPEND);
}
echo json_encode(['success' => true]);
exit();
