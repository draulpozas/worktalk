<?php

require_once __DIR__."/../config/autoload.php";
session_start();
UserController::checkSession();
UserController::edit($_SESSION['user_id']);

 ?>