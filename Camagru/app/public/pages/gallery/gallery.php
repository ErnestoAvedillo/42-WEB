<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';
    ?>
    <div class="gallery-container">
        <h1>Photo Gallery</h1>
        <p>Here you can view all the amazing photos shared by our community.</p>
        <div class="gallery-grid">
            <!-- Gallery content will go here -->
            <p>Gallery coming soon...</p>
        </div>
    </div>
    <?php
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>