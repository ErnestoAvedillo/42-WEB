<?php
include 'class_session.php';
if (!isset($session)) {
    $session = new SessionManager();
}
