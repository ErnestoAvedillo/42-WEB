<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../EnvLoader.php';

use PHPMailer\PHPMailer\PHPMailer;

function send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $isHTML = false)
{
    $autofilling = '/tmp/send_mail.log';
    
    // Validar que los parámetros obligatorios no estén vacíos
    if (empty($email_recipient)) {
        file_put_contents($autofilling, "ERROR: Recipient email is empty or null at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        return false;
    }
    
    if (empty($email_sender)) {
        file_put_contents($autofilling, "ERROR: Sender email is empty or null at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        return false;
    }

    $mail = new PHPMailer(true);
    //$mail->SMTPDebug = 2; // Habilita la depuración para ver los errores
    file_put_contents($autofilling, "send_mail ==> Sending email to: " . $email_recipient . " at " . date('Y-m-d H:i:s')  . " Subject: " . $asunto . "\n", FILE_APPEND);
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
    $autofilling = '/tmp/send_mail.log';
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
    $autofilling = '/tmp/send_mail.log';
    $validationToken = random_int(100000, 999999); // Generar un token de validación aleatorio
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $asunto = "Confirma tu correo de registro en Camagru";
    $mensaje = "Has recibido este correo porque te has registrado en nuestro sitio web ";
    $mensaje .= "con el usuario " . htmlspecialchars($username) . ".\n \r<br>";
    $mensaje .= "Por favor confirma tu registro haciendo clic en el siguiente enlace: \n \r <br>";
    $mensaje .= "Validar mi cuenta haciendo click <a href='http://" . $ipAddress . ":" . $portAddress . "/pages/register/confirm_link_handler.php?username=" . urlencode($username) . "&token=" . $validationToken . "'>Aquí</a><br>";
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
    $autofilling = '/tmp/send_mail.log';
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $asunto = "Recuperación de contraseña en Camagru";
    $mensaje = "Hola " . htmlspecialchars($username) . ",\n \r<br>";
    $mensaje .= "Has solicitado restablecer tu contraseña.\n \r";
    $mensaje .= "Puedes acceder a la página de recuperación pinchando en el siguiente enlace:";
    $mensaje .= " <a href='http://" . $ipAddress . ":" . $portAddress . "/pages/forgot_password/forgot_password.php?username=" . urlencode($username) . "&token=" . $token . "'>Recuperar contraseña</a> \n \r<br>";
    $mensaje .= "Si no has solicitado esta recuperación, puedes ignorar este correo.";
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');
    $HTML = true;

    return send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $HTML);
}

function send_comment_notification($email_recipient, $username, $picture_uuid, $commenter)
{
    $autofilling = '/tmp/send_mail.log';
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $asunto = "Nueva notificación de comentario en Camagru";
    $mensaje = "Hola " . htmlspecialchars($username) . ",\n \r<br>";
    $mensaje .= "El usuario " . htmlspecialchars($commenter) . " ha comentado una de tus fotos.\n \r";
    $mensaje .= "Puedes ver el comentario pinchando en el siguiente enlace:";
    $mensaje .= " <a href='http://" . $ipAddress . ":" . $portAddress . "/pages/picture/picture.php?picture_uuid=" . urlencode($picture_uuid) . "'>Ver comentario</a> \n \r<br>";
    $mensaje .= "Si no deseas recibir más notificaciones de este tipo, puedes cambiar tus preferencias en la configuración de tu cuenta.";
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');
    $HTML = true;

    return send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $HTML);
}

function send_contact_notification($email_contact_requester, $name, $message)
{
    $autofilling = '/tmp/send_mail.log';
    file_put_contents($autofilling, "send_contact_notification ==> Sending contact notification from: " . $email_contact_requester . " Name: " . $name . " at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($autofilling, "Message: " . $message . "\n", FILE_APPEND);
    $ipAddress = EnvLoader::get('APP_ADDR');
    $portAddress = EnvLoader::get('APP_PORT');
    $email_sender = EnvLoader::get('NO_REPLY_EMAIL');
    $password_sender = EnvLoader::get('NO_REPLY_PASSWORD');
    $host = EnvLoader::get('SMTP_HOST');
    $port = EnvLoader::get('SMTP_PORT');
    $HTML = true;

    // Primero enviar notificación al administrador
    $asunto = "Nuevo mensaje de contacto en Camagru de un usuario";
    $mensaje = "Hola, " . htmlspecialchars($name) . " te ha enviado un mensaje,\n \r<br>";
    $mensaje .= "Email del remitente: " . htmlspecialchars($email_contact_requester) . "\n \r<br>";
    $mensaje .= "Has recibido un nuevo mensaje de contacto:\n \r";
    $mensaje .= "<blockquote>" . nl2br(htmlspecialchars($message)) . "</blockquote>";
    $email_recipient = EnvLoader::get('EMAIL_CONTACT');
    
    // Verificar que la dirección de contacto del admin no esté vacía
    if (empty($email_recipient)) {
        error_log("ERROR: EMAIL_CONTACT environment variable is not set or empty");
        return false;
    }

    $result1 = send_mail($email_sender, $password_sender, $email_recipient, $asunto, $mensaje, $host, $port, $HTML);

    // Luego enviar confirmación al usuario que envió el mensaje
    $asunto = "Nuevo mensaje de contacto al administrador de Camagru";
    $mensaje = "Gracias por contactar con el equipo de Camagru.\n \r";
    $mensaje .= "Este es tu mensaje:\n \r";
    $mensaje .= "<blockquote>" . nl2br(htmlspecialchars($message)) . "</blockquote>";
    $mensaje .= "\n \r<br>Nos pondremos en contacto contigo lo antes posible.";
    $mensaje .= "\n \r<br>Atentamente,";
    $mensaje .= "\n \r<br>El equipo de Camagru";
    
    // Verificar que el email del solicitante no esté vacío
    if (empty($email_contact_requester)) {
        error_log("ERROR: email_contact_requester is empty");
        return false; // Al menos devolver el resultado del primer email
    }

    $result2 = send_mail($email_sender, $password_sender, $email_contact_requester, $asunto, $mensaje, $host, $port, $HTML);
    
    return $result1 && $result2; // Ambos emails deben ser enviados exitosamente
}
