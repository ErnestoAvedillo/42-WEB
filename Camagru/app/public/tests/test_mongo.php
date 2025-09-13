<?php
require_once __DIR__ . '/../database/mongo_db.php'; // Adjust path since we're in database/ folder
use MongoDB\Client;
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;

$db = new DocumentDB('uploads');
$db->connect();
$db->uploadFile("/home/ernesto/Downloads/Ernesto.png");
