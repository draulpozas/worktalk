<?php
header("Access-Control-Allow-Origin: localhost");
require_once __DIR__."/../../config/autoload.php";
$lang = View::getLang();

if (User::getFromUsername($_GET['username'])) {
    echo $lang['{{usernameunavailable}}']." :(";
} else {
    echo $lang['{{usernameavailable}}'] . " :)";
}