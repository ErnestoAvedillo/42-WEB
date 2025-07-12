
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
    <header>
        <button id="toggleSidebarBtn">☰</button>
        <h1>Camagru</h1>
        <nav>
            <ul>
                <li><a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="gallery.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'class="active"' : ''; ?>>Gallery</a></li>
                <li><a href="login.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="active"' : ''; ?>>Login</a></li>
                <li><a href="register.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'class="active"' : ''; ?>>Register</a></li>
            </ul>
        </nav>
    </header>
    <aside id="sidebar">
        <h2>Sidebar</h2>
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="help.php">Help</a></li>
        </ul>
    </aside>
