<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
ChatController::rename($_GET['chat_id']);

 ?>