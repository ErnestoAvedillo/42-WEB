<?php
require __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

// Por ejemplo, la URI que viene de OTPHP
$provisioningUri = "otpauth://totp/MiApp:usuario@miapp.com?secret=JBSWY3DPEHPK3PXP&issuer=MiApp";

// Crear QR
$qrCode = new QrCode(
    data: $provisioningUri,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: new ErrorCorrectionLevelLow(),
    size: 300,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    foregroundColor: new Color(0, 0, 0),
    backgroundColor: new Color(255, 255, 255)
);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Mostrar directamente como imagen en el navegador
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
