<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/posts.php';
require_once __DIR__ . '/../../database/mongo_db.php';
require_once __DIR__ . '/../../database/User.php';
require_once __DIR__ . '/../../utils/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userUuid = $_POST['user_uuid'] ?? null;
    $pictureUuid = $_POST['picture_uuid'] ?? null;
    $caption = $_POST['caption'] ?? null;
    error_log("Add post request: user_uuid=$userUuid, picture_uuid=$pictureUuid, caption=$caption");
    if (!$userUuid || !$pictureUuid || !$caption) {
        echo '<script> alert("Missing required fields."); </script>';
        exit();
    }
    $documentInstance = new DocumentDB("combines");
    try {
        $pictureOwner = $documentInstance->getPhotoOwner($pictureUuid);
    } catch (Exception $e) {
        echo '<script> alert("Error retrieving picture owner: ' . $e->getMessage() . '"); </script>';
        exit();
    }
    error_log("Picture owner UUID: " . print_r($pictureOwner, true));
    $userInstance = new User();
    $pictureOwnerData = $userInstance->getUserByUuid($pictureOwner);
    if ($userInstance->send_notification_enabled($pictureOwner)) {
        send_comment_notification($pictureOwnerData['email'], $pictureOwnerData['username'], $pictureUuid, SessionManager::getSessionKey('username'));
    }
    if ($userUuid && $pictureUuid && $caption) {
        $post = new Posts();
        if (!$post->addPost($userUuid, $pictureUuid, $caption)) {
            echo '<script> alert("Failed to add post."); </script>';
        }
        header('Location: /pages/login/login.php?forward=/pages/picture/picture.php?picture_uuid=' . urlencode($pictureUuid));
    }
}
