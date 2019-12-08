<?php
require_once __DIR__."/../config/autoload.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    Login::main();
} else {
    if (!$_SESSION['user_id']) {
        Login::main();
    } else {
        View::init();
    }
}

 ?>