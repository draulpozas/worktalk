<?php
header("Access-Control-Allow-Origin: localhost");
require_once __DIR__."/../../config/autoload.php";
session_start();

$html = ChatController::getHTMLList();
echo strtr($html, View::getLang());