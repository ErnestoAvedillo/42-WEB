<?php
include '../class_session/class_session.php';
$session = new SessionManager();
?>
<!DOCTYPE html>
<html lang="en">

<header>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diverse Test</title>
</header>

<body>
    <h1>Diverse Test Cases</h1>
    <?php
    // Test session management
    $session->printSessionData();
    $session->printCookies();
    ?>
    <ul>
        <li><a href="session_test.php">Session Management Test</a></li>
        <li><a href="cookie_test.php">Cookie Management Test</a></li>
    </ul>
</body>

</html>