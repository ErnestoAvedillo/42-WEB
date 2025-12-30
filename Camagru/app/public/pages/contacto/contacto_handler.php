<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();  
require_once __DIR__ . '/../../utils/send_mail.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));
  
    // Aquí puedes agregar la lógica para manejar el formulario, como enviar un correo electrónico
    $result=send_contact_notification($email, $name, $message);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contacto - Camagru</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <?php
  $pageTitle = "Contacto - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>
  <h1>Gracias por contactarnos, <?php echo $name; ?></h1>
  <?php
  if ($result) {
      echo "<p>Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo pronto.</p>";
  } else {
      echo "<p>Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.</p>";
  }
  ?>
  <p> Este es el mensaje que nos has enviado:</p>
  <p> <?php echo $message; ?></p>
  <?php include __DIR__ . '/../../pages/right_bar/right_bar.php'; ?>
  <?php include __DIR__ . '/../../pages/footer/footer.php'; ?>
</body>