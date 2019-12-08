<?php

require_once __DIR__."/../config/autoload.php";

class UserController{
    public static function show($user_id){
        if ($user_id == $_SESSION['user_id']) {
            $usr = new User($user_id);
            $replace = [
                '{{username}}' => $usr->username(),
                '{{email}}' => $usr->email(),
            ];
            View::showProfile($replace);
        } else {
            View::unavailableContent();
        }
    }

    public static function edit($user_id){
        $usr = new User($user_id);
        if ($_POST){
            if (isset($_POST['username'])) {
                $usr->username($_POST['username']);
            }
            if ($_POST['passwd'] === $_POST['passwd2'] && isset($_POST['passwd'])) {
                $usr->passwd($_POST['passwd']);
            }
            $usr->lang($_POST['lang']);

            if (isset($_POST['email'])) {
                $e = User::getFromEmail($_POST['email']);
                if ($e && $e->id() != $user_id) {
                    View::errorPage("{{emailtaken}}");
                    return false;
                }
            } else {
                $usr->email($_POST['email']);
            }

            $u = User::getFromUsername($_POST['username']); 
            if ($u) {
                if ($u->id() != $user_id) {
                View::errorPage("{{usernametaken}}");
                return false;
                }
            }

            $usr->save();
            self::show($user_id);
        } else {
            if ($user_id == $_SESSION['user_id']) {
                $langs_str = '';
                foreach (glob('../view/lang/*.json') as $langfile) {
                    $lang = explode('_', $langfile)[1];
                    $langs_str .= "<option value=\"". $lang . "\"";
                    if ((new User($_SESSION['user_id']))->lang() == $lang) {
                        $langs_str .= " selected ";
                    }
                    $langs_str .= ">". $lang ."</option>";
                }

                $replace = [
                    '{{username}}' => $usr->username(),
                    '{{email}}' => $usr->email(),
                    '{{langoptions}}' => $langs_str,
                ];
                View::editProfile($replace);
            } else {
                View::unavailableContent();
            }
        }
    }

    public static function signUp(){
        if ($_POST) {
            $usr = new User();
            if (User::getFromUsername($_POST['username'])) {
                View::errorPage("{{usernametaken}}");
                return false;
            }
            if (User::getFromEmail($_POST['email'])) {
                View::errorPage("{{emailtaken}}");
                return false;
            }
            $usr->username($_POST['username']);
            $passwd = Login::generateNewPasswd(32);
            $usr->passwd($passwd);
            $usr->email($_POST['email']);
            $usr->save();
            sendEmail($usr->email(), "Account register", "This email address has been associated with a WorkTalk account.<br>Username: ". $usr->username() .".<br>Your password is: $passwd<br> Follow <a href=\"". APP_URL ."\">this link</a> to log in. Then you can change your password in your profile settings");
            View::infoPage("Email was sent", "Follow the instructions from there to complete your registration.");
        } else {
            View::signUp();
        }
    }

    public static function delete(){
        $usr = new User($_SESSION['user_id']);
        $chats = $usr->getChats();
        $msgs = $usr->getMessages();
        $frns = $usr->getFriends();
        $rqts = $usr->getCreatedRequests();

        foreach ($msgs as $msg) {
            $msg->delete();
        }

        foreach ($chats as $chat) {
            $chat->removeUser($usr->id());
        }

        foreach ($frns as $friend) {
            User::deleteFriendship($friend->id());
        }

        foreach ($rqts as $request) {
            User::deleteFriendship($request->id());
        }

        $usr->delete();
        sendEmail($usr->email(), "Deleted account", "We are sorry to see you go. We hope you come back to use our application. Have a great day!");
        session_destroy();
        View::infoPage("Account deleted", "Thank you for using WorkTalk.");
    }

    public static function joinChat(){
        if ($_POST) {
            $chats = Chat::getAll();
            $joined = false;
            for ($i=0; $i < count($chats); $i++) { 
                if (($chats[$i]->share_code() == $_POST['share_code']) && !$chats[$i]->hasUser($_SESSION['user_id'])) {
                    $chats[$i]->addUser($_SESSION['user_id']);
                    $chat_id = $chats[$i]->id();
                    Message::sendNewMessage($chat_id, $_SESSION['user_id'], "Joined the chat.");
                    ChatController::show($chat_id);
                    $joined = true;
                }
            }
            if (!$joined) {
                View::errorPage("{{chatnotfound}}.");
            }
        } else {
            View::joinChat();
        }
    }

    public static function leaveChat($chat_id){
        $chat = new Chat($chat_id);
        Message::sendNewMessage($chat_id, $_SESSION['user_id'], "Left the chat.");
        $chat->removeUser($_SESSION['user_id']);
        header("location: ". APP_URL);
    }

    public static function checkSession(){
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            header("location: ". APP_URL);
        }
    }

    public static function friends(){
        if ($_POST) {
            $usr2 = User::getFromUsername($_POST['username']);
            if (!$usr2) {
                $usr2 = User::getFromEmail($_POST['username']);
            }
            if ($usr2 && !$usr2->isFriend($_SESSION['user_id'])) {
                User::sendFriendshipRequest($usr2->id());
            }
            header("location: ". APP_URL ."/friends.php");
        } else {
            $user = new User($_SESSION['user_id']);
            $requests = $user->getFriendshipRequests();
            $friends = $user->getFriends();

            $requests_str = '';
            foreach ($requests as $usr) {
                $requests_str .= "<li>" . $usr->username() . " - <a href=\"friends/accept.php?user_id_1=". $usr->id() ."\">{{acceptfriendship}}</a>&nbsp;|&nbsp;<a href=\"friends/delete.php?user_id_1=". $usr->id() ."\">{{rejectfriendship}}</a></li>";
            }

            $friends_str = '';
            foreach ($friends as $usr) {
                $friends_str .= "<li>" . $usr->username() . " - <a href =\"newPrivateChat.php?user_id=". $usr->id() ."\">{{privatechat}}</a></li>";
            }

            View::friends(['{{requestslist}}' => $requests_str, '{{friendslist}}' => $friends_str]);
        }
    }
}
 ?>