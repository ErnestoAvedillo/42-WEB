<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../models/User.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$user = User::find($_SESSION['user_id']);

if (!$user) {
    header('Location: /login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $user->update($_POST);
    } elseif (isset($_POST['change_password'])) {
        $user->changePassword($_POST);
    }
}
