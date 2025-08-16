<?php
require_once __DIR__ . '/../../class_session/session.php';
SessionManager::getInstance();
require_once __DIR__ . '/../../database/posts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userUuid = $_POST['user_uuid'] ?? null;
    $pictureUuid = $_POST['picture_uuid'] ?? null;
    $caption = $_POST['caption'] ?? null;

    if ($userUuid && $pictureUuid && $caption) {
        $post = new Posts();
        if (!$post->addPost($userUuid, $pictureUuid, $caption)) {
            echo '<script> alert("Failed to add post."); </script>';
        }
        header('Location: /pages/picture/picture.php?picture_uuid=' . urlencode($pictureUuid));
    }
}
