<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
$csrf_token = $_SESSION['csrf_token'] ?? null;
if (!$csrf_token) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION['csrf_token'] = $csrf_token;
}
if (!SessionManager::getSessionKey('uuid')) {
  // echo "<script>alert('You must be logged in to access this page.');</script>";
  header('Location: /pages/login/login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Document</title>
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/pages/combine/combine.css">
</head>

<body>
  <?php
  if (isset($_GET['secret'])) {
    $secret = $_GET['secret'];
  }
  $pageTitle = "Make your own collages - Camagru";
  include __DIR__ . '/../../pages/header/header.php';
  include __DIR__ . '/../../pages/left_bar/left_bar.php';
  ?>
  <div>
    <h1> Create your personalized pictures </h1>
    <div>
      <?php
      require_once __DIR__ . '/../../vendor/autoload.php';

      $website_name = 'Camagru';
      $user_email = SessionManager::getSessionKey('email');
      $username = SessionManager::getSessionKey('uuid');

      use OTPHP\TOTP;
      use Endroid\QrCode\QrCode;
      use Endroid\QrCode\Writer\PngWriter;

      if (!isset($secret)) {
        $totp = TOTP::create();
        $secret = $totp->getSecret();
      } else {
        $totp = TOTP::create($secret);
      }
      $totp->setLabel($user_email);
      $totp->setIssuer($website_name);
      // Generar la URI que Google Authenticator entiende
      $uri = $totp->getProvisioningUri();
      // Create QR code object
      $qr = new QrCode($uri); // <-- just pass the URI here
      // Create PNG writer
      $writer = new PngWriter();
      $result = $writer->write($qr);
      // Get Data URI for embedding in <img>
      $dataUri = $result->getDataUri();
      ?>
      <p>Scan this QR code with your authenticator app:</p>
      <img src="<?php echo $dataUri; ?>" alt="QR Code">
      <p>Or manually enter this secret key: <strong><?php echo $secret; ?></strong></p>
      <p>After scanning the QR code or entering the secret key, your authenticator app will generate a 6-digit code. Enter that code below to enable 2FA.</p>
      <form method="POST" action="/pages/2FA_config/2FA_config_handler.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="user_id" value="<?php echo $username; ?>">
        <input type="hidden" name="2fa_secret" value="<?php echo $secret; ?>">
        <label for="2fa_code">2FA Code:</label>
        <input type="text" id="2fa_code" name="2fa_code" required>
        <button type="submit">Enable 2FA</button>
      </form>
      <?php
      ?>
    </div>
  </div>
  <?php
  include __DIR__ . '/../../pages/right_bar/right_bar.php';
  include __DIR__ . '/../../pages/footer/footer.php';
  ?>
</body>

</html>