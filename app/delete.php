<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
ChatController::delete($_GET['chat_id']);

 ?>