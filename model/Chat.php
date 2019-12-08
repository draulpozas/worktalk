<?php

require_once __DIR__."/../config/autoload.php";

class Chat{
    //Class properties
    private $id;
    private $name;
    private $share_code;

    //Constructor
    public function __construct($id = null){
        if ($id) {
            $data = Database::selectChat("WHERE id = '$id'");
            if (!$data) {
                return false;
            }
            $params = $data[0];
            $this->id = $params['id'];
            $this->name = $params['name'];
            $this->share_code = $params['share_code'];
        }
    }

    //private methods
    /**
    * Generates a new share code randomly for the Chat object. Then, it returns the newly generated code.
    */
    private function generateShareCode(){
        $code;
        $repeated;
        do {
            $code = rand(1000000, 9999999);
            $repeated = false;
            $chats = self::getAll();
            for ($i=0; $i < count($chats); $i++) { 
                if ($code == $chats[$i]->share_code()) {
                    $repeated = true;
                }
            }
        } while ($repeated);

        $this->share_code = $code;
        return $this->share_code;
    }

    //get-set methods
    public function id(){
        return $this->id;
    }

    public function name($name = null){
        if ($name){
            $this->name = $name;
        }
        return $this->name;
    }

    public function share_code(){
        if (!isset($this->share_code)) {
            $this->generateShareCode();
        }
        return $this->share_code;
    }

    //basic methods
    /**
    * Save method. Collects the values of the object's properties, then it uses the Database class to save the information in the database.
    * It inserts or updates depending on whether or not an id has been set. This is because the id is only generated when the row is inserted in the database;
    * thus, if id is not defined, the object is considered a new row yet to be inserted in the database; if it has an id, it represents data loaded from a database record.
    */
    public function save(){
        $params = [
            'name' => $this->name(),
            'share_code' => $this->share_code(),
        ];

        if ($this->id) {
            Database::updateChat($params, $this->id);
        }
        else{
            Database::insertChat($params);
        }
    }

    /**
    * Deletes the row from the database table "chat" where the id is equal to the id of the current object.
    */
    public function delete(){
        Database::deleteChat($this->id);
    }

    //other methods
    /**
    * Returns an array of User objects, with all the users that are members of the current chat.
    */
    public function getUsers(){
        $data = Database::selectMemberOf("WHERE chat_id = ". $this->id() ." ORDER BY role DESC");

        $usrs = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($usrs, (new User($data[$i]['user_id'])));
        }

        return $usrs;
    }

    /**
    * Returns an array of Message objects, with all the messages that have been sent to the current chat.
    */
    public function getMessages(){
        $data = Database::selectMessage("WHERE chat_id = ".$this->id());

        $msgs = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($msgs, (new Message($data[$i]['id'])));
        }

        return $msgs;
    }

    /** 
    * Returns whether or not a specific user_id is a member of the current chat.
    */
    public function hasUser($user_id){
        $usrs = $this->getUsers();

        for ($i=0; $i < count($usrs); $i++) { 
            if ($usrs[$i]->id() == $user_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds a user_id as a member of the current chat
     */
    public function addUser($user_id){
        if ($this->hasUser($user_id)) {
            return false;
        }
        Database::insertMemberOf($this->id, $user_id);
    }

    /**
     * Removes a specific user_id from the current chat
     */
    public function removeUser($user_id){
        if ($this->hasUser($user_id)) {
            Database::deleteMemberOf($this->id(), $user_id);
        } else {
            return false;
        }
    }

    /**
     * Returns a string with the full list of messages sent to the current chat, represented as an HTML unordered list.
     */
    public function getHtml(){
        $msgs = $this->getMessages();
        $items_str = '<ul>';
        for ($i=0; $i < count($msgs); $i++) { 
            $msg = $msgs[$i];
            $items_str .= $msg->getHTML();
        }
        $items_str .= "</ul>";
        return $items_str;
    }

    /**
     * Returns an sql timestamp ('YYYY-MM-DD HH:mm:ss') string with the instant in which the most recent message of the current chat was sent.
     */
    public function getLastUpdate(){
        $chat_id = $this->id();
        $data = Database::selectMessage("WHERE chat_id = $chat_id ORDER BY id DESC LIMIT 1");
        $last_msg = new Message($data[0]['id']);
        return $last_msg->sent_timestamp();
    }

    /**
     * Sets a specific user_id as an admin for the current chat.
     */
    public function setAdmin($user_id){
        Database::setAdmin($user_id, $this->id());
    }

    /**
     * Sets a specific user_id as a regular user (revokes admin privileges) for the current chat.
     */
    public function setUser($user_id){
        Database::setUser($user_id, $this->id());
    }

    /**
     * Returns a string with the role of a specific user_id for the current chat.
     */
    public function getRole($user_id){
        $data = Database::selectMemberOf("WHERE chat_id = ".$this->id());
        for ($i=0; $i < count($data); $i++) { 
            if ($data[$i]['user_id'] == $user_id) {
                return $data[$i]['role'];
            }
        }
        return false;
    }

    /**
     * Updates the last connection of a specific user_id for the current chat.
     * The Database method automatically sets the current timestamp as the value for the last connection.
     */
    public function updateLastConnection($user_id){
        Database::updateLastConnection($user_id, $this->id());
    }

    /**
     * Returns a string with the sql timestamp ('YYYY-MM-DD HH:mm:ss') for the last connection of a specific user_id for the current chat.
     */
    public function getLastConnection($user_id){
        $data = Database::selectMemberOf("WHERE chat_id = ".$this->id());
        for ($i=0; $i < count($data); $i++) { 
            if ($data[$i]['user_id'] == $user_id) {
                return $data[$i]['last_connection'];
            }
        }
        return false;
    }

    //static methods
    /**
     * Returns an array with all the chats saved in the database.
     */
    public static function getAll(){
        $data = Database::selectChat();
        $chats = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($chats, (new Chat($data[$i]['id'])));
        }

        return $chats;
    }

    /**
     * Returns a Chat object given a specific share code. If none is found for the given code, it will return false.
     */
    public static function getFromShareCode($share_code){
        $data = Database::selectChat("WHERE share_code = $share_code");
        return new Chat($data[0]['id']);
    }

    /**
     * Returns an array with all the chats a specific user_id is a member of.
     */
    public static function getListByUser($user_id){
        $where = "WHERE user_id = $user_id";
        $data = Database::selectMemberOf($where);
        $chats = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($chats, (new Chat($data[$i]['chat_id'])));
        }

        return $chats;
    }

    /**
     * Returns the last Chat object that has been saved in the database.
     */
    public static function getLastInsertedElement(){
        $data = Database::selectChat("ORDER BY id DESC LIMIT 1");
        return new Chat($data[0]['id']);
    }
}

 ?>