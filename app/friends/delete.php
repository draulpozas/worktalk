<?php

require_once __DIR__."/../config/autoload.php";
session_start();

$usr2 = new User($_SESSION['user_id']);
$usr2->deleteFriendship($_GET['user_id_1']);

header("location: ". APP_URL ."/friends.php");

 ?>