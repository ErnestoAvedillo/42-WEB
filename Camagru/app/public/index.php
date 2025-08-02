<?php
include 'class_session/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<header>
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

  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';


  include 'main.php';

  if ($session_active) {
    // Check if the session is already started
    var_dump($_SESSION);
  } else {
    // Start the session if it hasn't been started yet
    echo "la variable is_session is false, session not started";
  }

  include 'views/footer.php';
  ?>
</body>