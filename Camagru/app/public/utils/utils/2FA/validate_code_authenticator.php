<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;

// Este secreto lo recuperas de la BD del usuario
$secret = "EL_SECRETO_DEL_USUARIO";

$totp = TOTP::create($secret);

// Código ingresado por el usuario en el login
$userCode = $_POST['otp_code'];

if ($totp->verify($userCode)) {
    echo "✅ Acceso concedido";
} else {
    echo "❌ Código inválido";
}
