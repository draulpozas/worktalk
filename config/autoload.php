<?php

require_once __DIR__."/../db/Database.php";
require_once __DIR__."/../model/Chat.php";
require_once __DIR__."/../model/Message.php";
require_once __DIR__."/../model/User.php";
require_once __DIR__."/../controller/ChatController.php";
require_once __DIR__."/../controller/UserController.php";
require_once __DIR__."/../view/View.php";
require_once __DIR__."/../app/Login.php";
require_once __DIR__."/../app/email/vendor/autoload.php";
require_once __DIR__."/../app/email/send.php";
define('APP_URL', __DIR__.'/../app');

 ?>