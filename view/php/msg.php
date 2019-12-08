<?php
header("Access-Control-Allow-Origin: localhost");
require_once __DIR__."/../../config/autoload.php";
session_start();
$chat_id = intval($_GET['chat_id'], 10);
if (!Message::sendNewMessage($chat_id, $_SESSION['user_id'], $_GET['msg'])){
    View::unavailableContent();
    return false;
}

$html = (new Chat($chat_id))->getHtml();
echo strtr($html, View::getLang());