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
  $pageTitle = "Gallery - Camagru";
  include 'views/header.php';
  ?>

  <?php
  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>

  <main id="mainContent">
    <h1>Photo Gallery</h1>
    <p>Here you can view all the amazing photos shared by our community.</p>
    <div class="gallery-grid">
      <!-- Gallery content will go here -->
      <p>Gallery coming soon...</p>
    </div>
  </main>

  <?php include 'views/footer.php'; ?>
  <script src="js/hide_bar.js"></script>
</body>

</html>