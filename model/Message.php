<?php

require_once __DIR__."/../config/autoload.php";

class Message{
    // Class properties
    private $id;
    private $chat_id;
    private $sender_id;
    private $content;
    private $sent;

    // Constructor
    public function __construct($id = null){
        if ($id) {
            $data = Database::selectMessage("WHERE id = $id");
            if (!$data) {
                return false;
            }
            $params = $data[0];
            $this->id = $params['id'];
            $this->chat_id = $params['chat_id'];
            $this->sender_id = $params['sender_id'];
            $this->content = $params['content'];
            $this->sent = $params['sent'];
        }
    }

    // get-set methods
    public function id(){
        return $this->id;
    }

    public function chat_id($chat_id = null){
        if ($chat_id){
            $this->chat_id = $chat_id;
        }
        return $this->chat_id;
    }

    public function sender_id($sender_id = null){
        if ($sender_id){
            $this->sender_id = $sender_id;
        }
        return $this->sender_id;
    }

    public function content($content = null){
        if ($content){
            $this->content = $content;
        }
        return $this->content;
    }

    public function sent_timestamp($timestamp = null){
        if ($timestamp){
            $this->sent = $timestamp;
        }
        return $this->sent;
    }

    public function sent_time(){
        $time = strtotime($this->sent_timestamp());
        return date("H:i", $time);
    }

    public function sent_date(){
        $date = strtotime($this->sent_timestamp());
        return date("d/m/Y", $date);
    }

    //basic methods
    /**
    * Save method. Collects the values of the object's properties, then it uses the Database class to save the information in the database.
    * It inserts or updates depending on whether or not an id has been set. This is because the id is only generated when the row is inserted in the database;
    * thus, if id is not defined, the object is considered a new row yet to be inserted in the database; if it has an id, it represents data loaded from a database record.
    */
    public function save(){
        $params = [
            'chat_id' => $this->chat_id(),
            'sender_id' => $this->sender_id(),
            'content' => $this->content(),
        ];

        if ($this->id) {
            Database::updateMessage($params, $this->id());
        }
        else{
            Database::insertMessage($params);
        }
    }

    /**
    * Deletes the row from the database table "chat" where the id is equal to the id of the current object.
    */
    public function delete(){
        Database::deleteMessage($this->id());
    }

    //other methods
    /**
     * Returns a string with the information of the current message represented as an HTML div object.
     * The HTML code changes depending on who sent it:
     * - Messages sent by the current user are assigned the css class "ownmsg".
     * - Messages sent by a user not present in the chat are added an appropriate message.
     * - If the current user is an admin and the sender is a regular user present in the chat, it will show a link for kicking the user off the chat.
     */
    public function getHTML(){
        $usr = new User($this->sender_id());
        $chat = new Chat($this->chat_id());
        $id = $this->id();
        $css_class = ($_SESSION['user_id'] == $this->sender_id()? ' ownmsg' : '');
        $username = $usr->username();
        $content = $this->content();
        $sent = $this->sent_time() ." - ". $this->sent_date();

        $str = "<div id=\"msg$id\" class=\"msg$css_class\" onclick=\"showMsgDetails('$id')\"><b>$username</b><br> $content<div style=\"display: none;\" class=\"details\" id=\"details".$id."\"> $sent. ";

        if (!$usr->belongsToChat($this->chat_id())) {
            $str .= "{{nolongerinchat}}";
        } else if ($chat->getRole($_SESSION['user_id']) == 'admin' && $_SESSION['user_id'] != $usr->id()) {
            $str .= "<a href=\"removeUser.php?user_id=".$usr->id()."&chat_id=".$this->chat_id()."\">{{removeuser}}</a>";
        }
        
        $str .= "</div></div>";

        return $str;
    }

    //static methods
    /**
     * Static function for sending a new message directly.
     * Chat id, sender user id and message content need to be given as parameters.
     * It will save the new message in the chat and update the last connection of the sender in that chat.
     * Returns the last message saved in the database.
     * Returns false if the user specified is not a member of the chat.
     */
    public static function sendNewMessage($chat_id, $sender_id, $content){
        $usr = new User($sender_id);
        $chat = new Chat($chat_id);
        if ($usr->belongsToChat($chat_id)) {
            $msg = new Message();
            $msg->chat_id($chat_id);
            $msg->sender_id($sender_id);
            $msg->content($content);
            $msg->save();
            $chat->updateLastConnection($sender_id);
            return self::getLastInsertedElement();
        }
        else {
            return false;
        }
    }

    /**
     * Returns an array with all the Message objects saved for a specific chat_id
     */
    public static function getListByChat($chat_id){
        $data = Database::selectMessage("WHERE chat_id = $chat_id ORDER BY id DESC");
        $msgs = [];
        for ($i=0; $i < count($data); $i++) { 
            array_push($msgs, (new Message($data[$i]['id'])));
        }

        return $msgs;
    }

    /**
     * Returns the last message to have been saved in the database.
     */
    public static function getLastInsertedElement(){
        $data = Database::selectMessage("ORDER BY id DESC LIMIT 1");
        return new Message($data[0]['id']);
    }

}

 ?>