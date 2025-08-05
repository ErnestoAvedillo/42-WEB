<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
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
    <link rel="stylesheet" href="/css/logout.css">
</head>

<body>

    <?php
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';

    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';

    require_once __DIR__ . '/../../class_session/session.php';
    // Verificar si la sesión está activa antes de intentar destruirla
    if (SessionManager::isSessionActive()) {

        // Limpiar todas las variables de sesión
        $_SESSION = [];
        // Destruir la sesión completamente
        SessionManager::destroySession();
    }
    ?>
    <div class="logout-container">
        <h1>You exited successfully from Camagru</h1>
        <p>Please login again to continue enjoying our services.</p>
        <div class="logout-grid">
            <!-- Gallery content will go here -->
            <p>Have a nice day!!!!</p>
            <button onclick="location.href='/index.php'" class="btn">Home</button>
        </div>
    </div>

    <?php
    $pageTitle = "footer - Camagru";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>