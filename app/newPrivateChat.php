<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
ChatController::newPrivateChat($_SESSION['user_id'], $_GET['user_id']);

 ?>