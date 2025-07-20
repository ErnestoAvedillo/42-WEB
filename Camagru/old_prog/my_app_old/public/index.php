<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Camagru'; ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>

<body>
  <?php
  $pageTitle = "Home - Camagru";
  include 'views/header.php';
  ?>

  <?php
  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>

  <main id="mainContent">
    <h1>Welcome to Camagru</h1>
    <p>This is a simple web application for sharing photos.</p>
    <p>Make sure to run the Docker containers using the Makefile commands.</p>
    <p>For more information, check the documentation.</p>

    <?php
    // Example PHP code to display the current date and time
    date_default_timezone_set('UTC');
    echo "<p>Current date and time: " . date("Y-m-d H:i:s") . "</p>";
    ?>
  </main>
  <h2 id='result'></h2>
  <?php include 'views/footer.php'; ?>
  <script src="js/hide_bar.js"></script>
</body>

</html>