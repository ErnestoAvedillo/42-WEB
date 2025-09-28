<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../EnvLoader.php';

use PHPMailer\PHPMailer\PHPMailer;

function send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $isHTML = false)
{
    $mail = new PHPMailer(true);
    //$mail->SMTPDebug = 2; // Habilita la depuración para ver los errores
    $mail->setFrom($email_sender, 'Camagru');
    $mail->addAddress($email_recipient);
    $mail->Subject = $asunto;
    $mail->Body = $mensaje;
    $mail->isSMTP();
    if ($isHTML) {
        $mail->isHTML(true);
    }
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $email_sender;
    $mail->Password = $password_sender;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usar SSL
    $mail->CharSet = 'UTF-8';
    $mail->Port = $port; // Puerto SMTP de Gmail
    $result = $mail->send();
    return $result;
}

function send_validation_token($email_recipient, $username)
{

    $validationToken = random_int(100000, 999999); // Generar un token de validación aleatorio
    $asunto = "Confirma tu correo de registro en Camagru";
    $mensaje = "Has recibido este correo porque te has registrado en nuestro sitio web ";
    $mensaje .= "con el usuario " . htmlspecialchars($username) . ".\n \r";
    $mensaje .= "Por favor introduce el siguiente código de verificación en el navegador: \n \r";
    $mensaje .= "Código: " . $validationToken;
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');


    $result = send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port);
    if ($result) {
        return $validationToken;
    }
    return null;
}


function send_validation_link($email_recipient, $username)
{

    $validationToken = random_int(100000, 999999); // Generar un token de validación aleatorio
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $asunto = "Confirma tu correo de registro en Camagru";
    $mensaje = "Has recibido este correo porque te has registrado en nuestro sitio web ";
    $mensaje .= "con el usuario " . htmlspecialchars($username) . ".\n \r<br>";
    $mensaje .= "Por favor confirma tu registro haciendo clic en el siguiente enlace: \n \r <br>";
    $mensaje .= "Validar mi cuenta ha <a href='http://" . $ipAddress . ":" . $portAddress . "/pages/register/confirm_link_handler.php?username=" . urlencode($username) . "&token=" . $validationToken . "'>Aquí</a><br>";
    $mensaje .= "\n \r Si no has sido tú quien se ha registrado, puedes ignorar este correo.";
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');
    $HTML = true;

    $result = send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $HTML);
    if ($result) {
        return $validationToken;
    }
    return null;
}

function send_recovery_email($email_recipient, $username, $token)
{
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $asunto = "Recuperación de contraseña en Camagru";
    $mensaje = "Hola " . htmlspecialchars($username) . ",\n \r<br>";
    $mensaje .= "Has solicitado restablecer tu contraseña.\n \r";
    $mensaje .= "Puedes acceder a la página de recuperación pinchando en el siguiente enlace:";
    $mensaje .= " <a href='http://" . $ipAddress . ":" . $portAddress . "/pages/login/password_recover/create_new_password.php?username=" . urlencode($username) . "&token=" . $token . "'>Recuperar contraseña</a> \n \r<br>";
    $mensaje .= "Si no has solicitado esta recuperación, puedes ignorar este correo.";
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');
    $HTML = true;

    return send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $HTML);
}
