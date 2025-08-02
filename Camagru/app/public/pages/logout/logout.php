<?php
include '../../class_session/session.php';
$session->destroy();
header("Location: ../../index.php");
exit();
