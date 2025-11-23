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
    <link rel="stylesheet" href="/pages/logout/logout.css">
</head>

<body>
    <?php
    // Verificar si la sesión está activa antes de intentar destruirla
    if (SessionManager::isSessionActive()) {

        // Limpiar todas las variables de sesión
        $_SESSION = [];
        // Destruir la sesión completamente
        SessionManager::destroySession();
    }
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../pages/header/header.php';
    include __DIR__ . '/../../pages/left_bar/left_bar.php';
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
    include __DIR__ . '/../../pages/right_bar/right_bar.php';
    include __DIR__ . '/../../pages/footer/footer.php';
    ?>
</body>

</html>