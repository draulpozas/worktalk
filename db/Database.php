<?php

require_once __DIR__."/../config/autoload.php";

class Database{
    //connection property
    private static $connection;

    /**
    * Connection method. It will connect to the database and assign the new PDO object to the $connection property.
    */
    private static function connect(){
        try{
            $conn_data = json_decode(file_get_contents(__DIR__."/../config/connection.json"), true);
            self::$connection = new PDO($conn_data['CONN_STRING'], $conn_data['DB_USER'], $conn_data['DB_PASS']);
		} catch (PDOException $e){
			echo "Database error: ".$e->getMessage();
			die();
		}
    }

    /**
    * Query method. It will use the $connection property to communicate directly with the database.
    * It receives the .sql path and a replace array for replacing the correspondent values in the predefined query string.
    * It also allows to specify if the replace array fields should be sanitized or not.
    */
    private static function query($file, $replace, $sanitize = false){
		if (!self::$connection) {
			self::connect();
        }
        
        if ($sanitize) {
            $replace = self::sanitizeReplace($replace);
        }

        $query = file_get_contents(__DIR__."/sql/$file");
        $query = strtr($query, $replace);
        $stm = self::$connection->prepare($query);
		$stm->execute();
		return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    * Basic sanitazing method. Escapes quotes and double quotes.
    */
    private static function sanitizeReplace($replace){
        $final = [];
        
        foreach ($replace as $field => $value) {
            $value = str_replace('\'', '\\\'', $value);
            $value = str_replace('\"', '\\\"', $value);
            // $value = str_replace('<', '\<', $value);
            // $value = str_replace('>', '\>', $value);
            $final[$field] = $value;
        }
        return $final;
    }

    //static methods -----------------------------------------------------------
    #Chat -------------------------------
    public static function insertChat($params){
        $file = 'insertChat.sql';
        $replace = [
            '{{name}}' => $params['name'],
            '{{share_code}}' => $params['share_code'],
        ];

        return self::query($file, $replace, true);
    }

    public static function selectChat($where = ''){
        $file = 'selectChat.sql';
        $replace = [
            '{{where}}' => $where,
        ];

        return self::query($file, $replace);
    }

    public static function updateChat($params, $id){
        $file = 'updateChat.sql';
        $replace = [
            '{{name}}' => $params['name'],
            '{{id}}' => $id,
        ];

        return self::query($file, $replace, true);
    }

    public static function deleteChat($id){
        $file = 'deleteChat.sql';
        $replace = [
            '{{id}}' => $id,
        ];

        return self::query($file, $replace);
    }

    #Message -------------------------------
    public static function insertMessage($params){
        $file = 'insertMessage.sql';
        $replace = [
            '{{chat_id}}' => $params['chat_id'],
            '{{sender_id}}' => $params['sender_id'],
            '{{content}}' => $params['content'],
        ];

        return self::query($file, $replace, true);
    }

    public static function selectMessage($where = ''){
        $file = 'selectMessage.sql';
        $replace = [
            '{{where}}' => $where,
        ];

        return self::query($file, $replace);
    }

    public static function deleteMessage($id){
        $file = 'deleteMessage.sql';
        $replace = [
            '{{id}}' => $id,
        ];

        return self::query($file, $replace);
    }

    #User -------------------------------
    public static function insertUser($params){
        $file = 'insertUser.sql';
        $replace = [
            '{{username}}' => $params['username'],
            '{{passwd}}' => $params['passwd'],
            '{{email}}' => $params['email'],
        ];

        return self::query($file, $replace, true);
    }

    public static function selectUser($where = ''){
        $file = 'selectUser.sql';
        $replace = [
            '{{where}}' => $where,
        ];

        return self::query($file, $replace);
    }

    public static function updateUser($params, $id){
        $file = 'updateUser.sql';
        $replace = [
            '{{username}}' => $params['username'],
            '{{passwd}}' => $params['passwd'],
            '{{email}}' => $params['email'],
            '{{lang}}' => $params['lang'],
            '{{id}}' => $id,
        ];

        return self::query($file, $replace, true);
    }

    public static function deleteUser($id){
        $file = 'deleteUser.sql';
        $replace = [
            '{{id}}' => $id,
        ];

        return self::query($file, $replace);
    }

    #MemberOf -------------------------------
    public static function insertMemberOf($chat_id, $user_id){
        $file = 'insertMemberOf.sql';
        $replace = [
            '{{chat_id}}' => $chat_id,
            '{{user_id}}' => $user_id,
        ];

        return self::query($file, $replace);
    }

    public static function selectMemberOf($where = ''){
        $file = 'selectMemberOf.sql';
        $replace = [
            '{{where}}' => $where,
        ];

        return self::query($file, $replace);
    }

    public static function setAdmin($user_id, $chat_id){
        $file = 'setAdmin.sql';
        $replace = [
            '{{chat_id}}' => $chat_id,
            '{{user_id}}' => $user_id,
        ];

        return self::query($file, $replace);
    }

    public static function setUser($user_id, $chat_id){
        $file = 'setUser.sql';
        $replace = [
            '{{chat_id}}' => $chat_id,
            '{{user_id}}' => $user_id,
        ];

        return self::query($file, $replace);
    }

    public static function updateLastConnection($user_id, $chat_id){
        $file = 'updateLastConnection.sql';
        $replace = [
            '{{chat_id}}' => $chat_id,
            '{{user_id}}' => $user_id,
        ];

        return self::query($file, $replace);
    }

    public static function deleteMemberOf($chat_id, $user_id){
        $file = 'deleteMemberOf.sql';
        $replace = [
            '{{chat_id}}' => $chat_id,
            '{{user_id}}' => $user_id,
        ];

        return self::query($file, $replace);
    }

    #Friendship -------------------------------
    public static function insertFriendship($user_id_1, $user_id_2){
        $file = 'insertFriendship.sql';
        $replace = [
            '{{user_id_1}}' => $user_id_1,
            '{{user_id_2}}' => $user_id_2,
        ];

        return self::query($file, $replace);
    }

    public static function selectFriendship($where){
        $file = 'selectFriendship.sql';
        $replace = [
            '{{where}}' => $where,
        ];

        return self::query($file, $replace);
    }

    public static function acceptFriendship($user_id_1, $user_id_2){
        $file = 'acceptFriendship.sql';
        $replace = [
            '{{user_id_1}}' => $user_id_1,
            '{{user_id_2}}' => $user_id_2,
        ];

        return self::query($file, $replace);
    }

    public static function deleteFriendship($user_id_1, $user_id_2){
        $file = 'deleteFriendship.sql';
        $replace = [
            '{{user_id_1}}' => $user_id_1,
            '{{user_id_2}}' => $user_id_2,
        ];

        return self::query($file, $replace);
    }
}

 ?>