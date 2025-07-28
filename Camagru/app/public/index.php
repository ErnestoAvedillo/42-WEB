<!DOCTYPE html>
<html lang="en">
<header>
  <?php if (!isset($_SESSION)) {
    session_start();
  } ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Camagru'; ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</header>

<body>
  <?php
  $pageTitle = "Home - Camagru";
  include 'views/header.php';
  ?>
  <?php
  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>
  <?php include 'main.php'; ?>

  <?php include 'views/footer.php'; ?>
</body>