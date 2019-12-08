<?php
header("Access-Control-Allow-Origin: localhost");
require_once __DIR__."/../../config/autoload.php";
session_start();
$chat_id = intval($_GET['chat_id'], 10);
$chat = new Chat($chat_id);
if (!$chat->hasUser($_SESSION['user_id'])) {
    View::unavailableContent();
    return false;
}
$chat->updateLastConnection($_SESSION['user_id']);

$html = $chat->getHtml();
echo strtr($html, View::getLang());