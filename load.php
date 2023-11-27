<?php

include 'connection.php';
include 'users.php';
include 'post.php';

global $pdo;

$loadFromUser = new User($pdo);
$loadFromPost = new Post($pdo);

define("BASE_URL", "http://localhost/");


?>
