<?php

require_once __DIR__."/../../config/autoload.php";
session_start();
$usr = new User($_SESSION['user_id']);
$usr->sendFriendshipRequest($_GET['user_id']);
header("location: ". APP_URL ."/friends.php");

 ?>