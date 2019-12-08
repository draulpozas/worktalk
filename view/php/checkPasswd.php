<?php
header("Access-Control-Allow-Origin: localhost");
require_once __DIR__."/../../config/autoload.php";
$lang = View::getLang();

if ($_GET['passwd'] === $_GET['passwd2']) {
    echo $lang["{{passwdmatch}}"];
} else {
    echo $lang["{{passwdnotmatch}}"];
}