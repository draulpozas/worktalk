<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
ChatController::members($_GET['chat_id']);

 ?>