<?php
require_once __DIR__ . '/../EnvLoader.php';
require_once __DIR__ . '/../utils/send_mail.php';

// Verificar que la petición sea POST
$mensaje = "Esto es una prueba de envío de correo electrónico.";
$email = EnvLoader::get('NO_REPLY_EMAIL');
$asunto = "Prueba de envío de correo";
$host = EnvLoader::get('SMTP_HOST');
$port = EnvLoader::get('SMTP_PORT');
$password = EnvLoader::get('NO_REPLY_PASSWORD');
$email_recipient = "eavedillo@yahoo.es";

$result = send_mail($email, $password, $email_recipient, $asunto, $mensaje, $host, $port);

if ($result) {
    echo "Correo enviado con éxito.";
} else {
    echo "Error al enviar el correo.";
}

$result = send_validation_token($email_recipient, "Prueba de envío");

if ($result !== null) {
    echo "Token de validación enviado con éxito. Tocken generado: " . htmlspecialchars($result);
} else {
    echo "Error al enviar el token de validación.";
}
