<?php
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../database/Profiles.php';
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
if (!SessionManager::getSessionKey('uuid')) {
    header('Location: /pages/request_login/request_login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/upload.css">
</head>

<body>
    <?php
    //    include __DIR__ . '/../../views/debugger.php';
    $pageTitle = "Home - Camagru";
    include __DIR__ . '/../../views/header.php';
    $pageTitle = "sidebar - Camagru";
    include __DIR__ . '/../../views/side_bar.php';
    $user = new User();
    $profile = new Profiles();
    $user_data = $user->getUserData(SessionManager::getSessionKey('id') ?? null);
    $profile_data = $profile->getProfileData(SessionManager::getSessionKey('uuid') ?? null);
    // Obtener errores y datos previos si existen
    $successMessage = $_SESSION['success_message'] ?? '';
    $errors = $_SESSION['error_messages'] ?? [];

    // Verificar si viene del registro
    $fromRegister = isset($_GET['registered']) && $_GET['registered'] == '1';
    $registeredUser = $_SESSION['registered_user'] ?? '';

    // Limpiar mensajes después de mostrarlos
    unset($_SESSION['error_messages']);
    unset($_SESSION['login_data']);
    unset($_SESSION['success_message']);
    unset($_SESSION['registered_user']);
    ?>
    <div class="uopload-container">
        <?php
        if ($successMessage != '') {
            echo "<div class='alert-success'>" . htmlspecialchars($successMessage) . "</div>";
        }
        if (isset($errors) && !empty($errors)) {
            // Mostrar errores
            foreach ($errors as $error) {
                echo "<div class='alert-error'>" . htmlspecialchars($error) . "</div>";
            }
        }
        ?>
        <h1>Upload Your Photos</h1>
        <p>Share your creativity with the world by uploading your photos.</p>
        <form action="upload_handler.php" method="post" enctype="multipart/form-data">
            <input type="file" name="photo" accept=".jpg,.jpeg,.png,.gif,.pdf,.mp4,.zip,.docx" required>
            <input type="hidden" name="user_uuid" value="<?php echo htmlspecialchars(SessionManager::getSessionKey('uuid')); ?>">
            <button type="submit" class="btn btn-primary">Upload Photo</button>
            <a href="/pages/gallery/gallery.php" class="btn btn-secondary">View Gallery</a>
        </form>
        <p>Supported formats: JPG, PNG, GIF, PDF, MP4, ZIP, DOCX</p>
    </div>
    <?php
    //    echo "<pre>";
    //    var_dump($_SESSION);
    //    echo "</pre>";
    include __DIR__ . '/../../views/footer.php';
    ?>
</body>

</html>