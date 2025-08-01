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
  $pageTitle = "Login - Camagru";
  include 'views/header.php';
  ?>

  <?php
  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>

  <main id="mainContent">
    <h1>Login</h1>
    <form class="login-form-text">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Don't have an account? Register here</a></p>
  </main>

  <?php include 'views/footer.php'; ?>
  <script src="js/hide_bar.js"></script>
</body>

</html>