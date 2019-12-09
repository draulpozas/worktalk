<?php

require_once __DIR__."/../config/autoload.php";

class User{
    // Class properties
    private $id;
    private $username;
    private $passwd;
    private $email;
    private $lang;

    // Constructor
    public function __construct($id = null){
        if ($id) {
            $data = Database::selectUser("WHERE id = $id");
            if (!$data) {
                return false;
            }
            $params = $data[0];
            $this->id = $id;
            $this->username = $params['username'];
            $this->passwd = $params['passwd'];
            $this->email = $params['email'];
            $this->lang = $params['lang'];
        }
    }

    // get-set methods
    public function id(){
        return $this->id;
    }

    public function username($username = null){
        if ($username){
            $this->username = $username;
        }
        return $this->username;
    }

    public function passwd($passwd = null){
        if ($passwd){
            $this->passwd = password_hash($passwd, PASSWORD_DEFAULT);
        }
        return $this->passwd;
    }

    public function email($email = null){
        if ($email){
            $this->email = $email;
        }
        return $this->email;
    }

    public function lang($lang = null){
        if ($lang){
            $this->lang = $lang;
        }
        return $this->lang;
    }

    //basic methods
    /**
    * Save method. Collects the values of the object's properties, then it uses the Database class to save the information in the database.
    * It inserts or updates depending on whether or not an id has been set. This is because the id is only generated when the row is inserted in the database;
    * thus, if id is not defined, the object is considered a new row yet to be inserted in the database; if it has an id, it represents data loaded from a database record.
    */
    public function save(){
        $params = [
            'username' => $this->username(),
            'passwd' => $this->passwd(),
            'email' => $this->email(),
            'lang' => $this->lang(),
        ];

        if ($this->id()) {
            return Database::updateUser($params, $this->id());
        } else {
            return Database::insertUser($params);
        }
    }

    /**
    * Deletes the row from the database table "chat" where the id is equal to the id of the current object.
    */
    public function delete(){
        Database::deleteUser($this->id());
    }

    //other methods
    /**
     * Returns an array with all the chats the current user is a member of.
     */
    public function getChats(){
        $data = Database::selectMemberOf("WHERE user_id = ". $this->id());

        $chats = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($chats, (new Chat($data[$i]['chat_id'])));
        }

        return $chats;
    }

    /**
     * Returns an array with all the messages sent by the current user.
     */
    public function getMessages(){
        $data = Database::selectMessage("WHERE sender_id = ". $this->id());

        $msgs = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($msgs, (new Message($data[$i]['id'])));
        }

        return $msgs;
    }

    /**
     * Returns whether or not the current user belongs to a specified chat_id
     */
    public function belongsToChat($chat_id){
        $chats = Chat::getListByUser($this->id());

        for ($i=0; $i < count($chats); $i++) { 
            if ($chats[$i]->id() == $chat_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether or not the credentials given as parameters are valid for logging the current user in.
     */
    public function verify($username, $passwd){
        return (($username == $this->username() || $username == $this->email()) && password_verify($passwd, $this->passwd()));
    }

    //static methods
    /**
     * Returns an array with the full list of users registered in the app, ordered alphabetically.
     */
    public static function getAll(){
        $data = Database::selectUser("ORDER BY username ASC");
        $usrs = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($usrs, (new User($data[$i]['id'])));
        }

        return $usrs;
    }

    /**
     * Checks whether or not the credentials given as a parameter are valid for any existing user.
     * If they are, it returns that user's id. If they are not, it returns false.
     */
    public static function checkLogin($username, $passwd){
        $usr = User::getFromUsername($username);
        if (!$usr) {
            $usr = User::getFromEmail($username);
        }if (!$usr) {
            return false;
        }
        if ($usr->verify($username, $passwd)) {
            return $usr->id();
        }
    }
    
    /**
     * Returns a User object where the email address is equal to the one given as a parameter.
     * If no user is found for the email address, it returns false.
     */
    public static function getFromEmail($email){
        $data = Database::selectUser("WHERE email = '$email'");
        if ($data) {
            $usr = new User($data[0]['id']);
            return $usr;
        }
        return false;
    }

    /**
     * Returns a User object where the username is equal to the one given as a parameter.
     * If no user is found for the provided username, it returns false.
     */
    public static function getFromUsername($username){
        $data = Database::selectUser("WHERE username = '$username'");
        if ($data) {
            $usr = new User($data[0]['id']);
            return $usr;
        }
        return false;
    }

    # Friendships --------------------------------------------------
    /**
     * Sends a new friendship request, from the current user to the one with the id given as a parameter.
     */
    public static function sendFriendshipRequest($user_id_2){
        return Database::insertFriendship($_SESSION['user_id'], $user_id_2);
    }

    /**
     * Returns an array of User objects. Gets all users that have sent an unanswered friendship request to the current user.
     */
    public function getFriendshipRequests(){
        $data = Database::selectFriendship("WHERE user_id_2 = ". $this->id() ." AND accepted = 0");
        $rqts = [];
        foreach ($data as $row) {
            array_push($rqts, (new User($row['user_id_1'])));
        }

        return $rqts;
    }

    /**
     * Sets the "accepted" field of a friendship request to '1'.
     */
    public static function acceptFriendship($user_id_2){
        return Database::acceptFriendship($user_id_2, $_SESSION['user_id']);
    }

    /**
     * Deletes a row from the Friendship table.
     */
    public static function deleteFriendship($user_id_1){
        return (Database::deleteFriendship($user_id_1, $_SESSION['user_id']) || Database::deleteFriendship($_SESSION['user_id'], $user_id_1));
    }

    /**
     * Returns whether or not the current user is friends with the specified user_id.
     */
    public function isFriend($user_id){
        $friends = $this->getFriends();
        foreach ($friends as $usr) {
            if ($usr->id() == $user_id) {
                return true;
            }
        }
    }

    /**
     * Returns an array with all the users that are related to the current user via friendship, where the "accepted" field is '1'.
     */
    public function getFriends(){
        $data = Database::selectFriendship("WHERE (user_id_2 = ". $this->id() ." OR user_id_1 = ". $this->id() .") AND accepted = 1");
        $usrs = [];

        foreach ($data as $row) {
            if ($row['user_id_1'] != $this->id()) {
                array_push($usrs, (new User($row['user_id_1'])));
            } else {
                array_push($usrs, (new User($row['user_id_2'])));
            }
        }

        return $usrs;
    }

    /**
     * Returns an array with all the users that are related to the current usre via friendship, where the "accepted" field is '0'.
     */
    public function getCreatedRequests(){
        $data = Database::selectFriendship("WHERE (user_id_2 = ". $this->id() ." OR user_id_1 = ". $this->id() .") AND accepted = 0");
        $usrs = [];

        foreach ($data as $row) {
            if ($row['user_id_1'] != $this->id()) {
                array_push($usrs, (new User($row['user_id_1'])));
            } else {
                array_push($usrs, (new User($row['user_id_2'])));
            }
        }

        return $usrs;
    }

}

 ?>