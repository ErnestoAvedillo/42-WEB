<?php
require_once __DIR__ . '/../../../database/demandas.php';
require_once __DIR__ . '/../../../database/facturas.php';
require_once __DIR__ . '/../../../database/mongo_db.php';
require_once __DIR__ . '/../../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    // echo "<script>alert('You must be logged in to access this page.');</script>";
    header('Location: /pages/request_login/request_login.php');
    exit();
}
$autofilling = '/tmp/debug_delete_demandas.log';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check the cookie value in PHP
    if ($_COOKIE['deleteConfirmed'] ?? false) {
        $demandaId = $_GET['id'] ?? null;
        if ($demandaId) {
            $demandasInstance = new Demandas();
            $facturasInstance = new Facturas();
            $documentosInstance = new DocumentDB('demandas');
            //**** Seleccionar facturas para borrar****
            $listaFacturas = $demandasInstance->getFacturasByDemandaId($demandaId);
            foreach ($listaFacturas as $facturaId) {
                $documento_uuid = $facturasInstance->getDocumentoByFacturaId($facturaId);
                $result = $documentosInstance->delete($documento_uuid);
                if ($result) {
                    $facturasInstance->delete($facturaId);
                } else {
                    echo "<script>alert('Failed to delete document from MongoDB.');</script>";
                }
                header('Location: /pages/facturas/factura.php');
                exit();
            }
        } else {
            header('Location: /pages/facturas/factura.php');
            exit();
        }
    } else {
        header('Location: /pages/facturas/factura.php');
        exit();
    }
}
