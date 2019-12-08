<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
ChatController::removeUser($_GET['user_id'], $_GET['chat_id']);

 ?>