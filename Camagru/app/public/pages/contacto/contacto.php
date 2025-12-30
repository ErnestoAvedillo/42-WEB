<?php
  require_once __DIR__ . '/../../class_session/session.php';
  SessionManager::getInstance();
  if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
?>
<div class="terms-container">
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contacto</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
</head>
  
  <body>
    <?php
    $pageTitle = "Contacto - Camagru";
    include __DIR__ . '/../../pages/header/header.php';
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
    ?>
    <h1>Contacto</h1>
    <form action="/pages/contacto/contacto_handler.php" method="post">
      <label for="name">Nombre:</label><br>
      <input type="text" id="name" name="name" required><br><br>

      <label for="email">Correo Electrónico:</label><br>
      <input type="email" id="email" name="email" required><br><br>

      <label for="message">Mensaje:</label><br>
      <textarea id="message" name="message" rows="5" required></textarea><br><br>

      <input type="submit" value="Enviar">
    </form>
    <h1>También puedes contactarnos en:</h1>
    <p><strong>Teléfono:</strong> 📞 +34 674 318 517</p>
    <p><strong>Dirección:</strong> 📍 Carrer d' Albert Einstein, 11. Barcelona</p>
    <p><strong>Email:</strong> 📧 eavedillo@yahoo.es</p>

    <?php include __DIR__ . '/../../pages/right_bar/right_bar.php'; ?>
    <?php include __DIR__ . '/../../pages/footer/footer.php'; ?>


</div>
</body>

</html>