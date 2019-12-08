<?php

require_once __DIR__."/../config/autoload.php";

class View{
    /**
     * Returns an assoc. array of strings for the language replacement at HTML printing. 
     */
    public static function getLang(){
        if (!isset($_SESSION)) {
            return json_decode(file_get_contents(__DIR__."/lang/_en_.json"), true);
        }
        $lang;
        $usr = new User($_SESSION['user_id']);
        switch ($usr->lang()) {
            case 'es':
                $lang = json_decode(file_get_contents(__DIR__."/lang/_es_.json"), true);
                break;
            case 'fr':
                $lang = json_decode(file_get_contents(__DIR__."/lang/_fr_.json"), true);
                break;
            case 'it':
                $lang = json_decode(file_get_contents(__DIR__."/lang/_it_.json"), true);
                break;
            default:
                $lang = json_decode(file_get_contents(__DIR__."/lang/_en_.json"), true);
                break;
        }

        return $lang;
    }

    /**
     * Initializes the app. Simply calls to the list of chats.
     */
    public static function init(){
        ChatController::list();
    }

    /**
     * Displaying of the chat list of the user.
     */
    public static function listChats($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/listChats.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of a specific chat.
     */
    public static function showChat($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/showChat.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the "new chat" page.
     */
    public static function newChat(){
        $lang = self::getLang();
        $html =  file_get_contents(__DIR__."/templates/newChat.html");
        $html = self::addHeader($html);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the profile page. 
     */
    public static function showProfile($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/showProfile.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the "edit profile" page. 
     */
    public static function editProfile($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/editProfile.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the "content unavailable" page. 
     */
    public static function unavailableContent(){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/unavailableContent.html");
        $html = self::addHeader($html);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the sing up page. 
     */
    public static function signUp(){
        echo file_get_contents(__DIR__."/templates/signUp.html");
    }

    /**
     * Displaying of the "join new chat" page. 
     */
    public static function joinChat(){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/joinChat.html");
        $html = self::addHeader($html);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the "forgotten password" page. 
     */
    public static function resetPasswd(){
        echo file_get_contents(__DIR__."/templates/resetPasswd.html");
    }

    /**
     * Displaying of the "rename chat" page. 
     */
    public static function renameChat($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/renameChat.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the "view chat members" page. 
     */
    public static function members($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/members.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of the friends main page. 
     */
    public static function friends($replace){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/addFriends.html");
        $html = self::addHeader($html);
        $html = strtr($html, $replace);
        echo strtr($html, $lang);
    }

    /**
     * Displaying of an error page. 
     * Displays a specified error message in an appropriate template. 
     */
    public static function errorPage($err_msg){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/error.html");
        $html = self::addHeader($html);
        $html = strtr($html, ['{{err_msg}}' => $err_msg]);
        echo strtr($html, $lang);
    }

    /**
     * Displays a specific information message, along with a specified title, in an apropriate template. 
     */
    public static function infoPage($info_title, $info_msg){
        $lang = self::getLang();
        $html = file_get_contents(__DIR__."/templates/info.html");
        $html = self::addHeader($html);
        $html = strtr($html, ['{{info_title}}' => $info_title, '{{info_msg}}' => $info_msg]);
        echo strtr($html, $lang);
    }

    /**
     * Adds the header menu to the specified HTML template and returns the result.
     * This way, the same HTML code appears on every page with no need to write it several times. 
     */
    private static function addHeader($html){
        return strtr($html, ['{{header}}' => file_get_contents(__DIR__."/templates/headerTmp.html")]);
    }

}

 ?>