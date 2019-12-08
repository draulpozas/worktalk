<?php
require_once __DIR__."/../config/autoload.php";
require_once __DIR__."/email/send.php";

class Login{
    public static function main(){
        if ($_POST) {
            $_SESSION['user_id'] = User::checkLogin($_POST['username'], $_POST['passwd']);
            header("Location: ./index.php");
        }
        echo file_get_contents ("../view/templates/login.html");
    }

    public static function forgotten(){
        if ($_POST) {
            $email = $_POST['email'];
            $passwd = self::generateNewPasswd();
            $usr = User::getFromEmail($email);
            if ($usr) {
                $usr->passwd($passwd);
                $usr->save();
                sendEmail($email, "Password recovery", "Your new password for WorkTalk is $passwd");
                View::infoPage("Email was sent", "Follow the instructions from there to recover your password.");
            } else {
                echo "No user was found for that email. <a href=\"./\">Go back</a>";
            }
        } else {
            View::resetPasswd();
        }
    }

    public static function generateNewPasswd($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $passwd = '';
        for ($i = 0; $i < $length; $i++) {
            $passwd .= $characters[rand(0, $charactersLength - 1)];
        }
        return $passwd;
    }
}

 ?>