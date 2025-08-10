<?php
require_once __DIR__ . '/../class_session/session.php';
SessionManager::getInstance();
?>
<div class="debug-info">
    <h2>Debugging Information</h2>
    <pre>
        <?php
        // Display session data
        print_r($_SESSION);
        print_r($_FILES);
        ?>
    </pre>
</div>