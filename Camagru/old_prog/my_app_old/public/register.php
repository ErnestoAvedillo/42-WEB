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
  $pageTitle = "Register - Camagru";
  include 'views/header.php';
  ?>

  <?php
  $pageTitle = "sidebar - Camagru";
  include 'views/side_bar.php';
  ?>

  <main id="mainContent">
    <h1>Register</h1>

    <?php
    require_once '/database/User.php';

    $message = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = trim($_POST['username']);
      $email = trim($_POST['email']);
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm_password'];

      // Create database connection
      $database = new Database();
      $manager = $database->connect();
      $user = new User($manager, $database->getDatabase());

      // Validate input
      if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
      } elseif (!$user->validateUsername($username)) {
        $error = "Username must be 3-20 characters long and contain only letters, numbers, and underscores.";
      } elseif (!$user->validateEmail($email)) {
        $error = "Please enter a valid email address.";
      } elseif (!$user->validatePassword($password)) {
        $error = "Password must be at least 8 characters long.";
      } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
      } else {
        // Try to create user
        if ($user->create($username, $password, $email)) {
          $message = "User created successfully! You can now <a href='login.php'>login</a>.";
        } else {
          $error = "Username or email already exists.";
        }
      }
    }
    ?>

    <?php if ($error): ?>
      <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="success-message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form class="login-form-text" method="POST" action="">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit">Register</button>
    </form>
    <p><a href="login.php">Already have an account? Login here</a></p>
  </main>

  <?php include 'views/footer.php'; ?>
  <script src="js/hide_bar.js"></script>
</body>

</html>