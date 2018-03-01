<?php

require_once('src/DB.class.php');
require_once('src/Helpers.class.php');

class Chat{

    private static $DEFAULT_CHAT_NAME = "💻 Chat Room";
    private static $DEFAULT_FETCH_AMOUNT = 20;

    public static function setupNewChat(string $chatToken=null){

        if($chatToken === null){ $chatToken = Helpers::generateToken(8); }

        // ADD TO DB =>
        $db = new DB("db/chat.db");
        $db->runQuery(
            "INSERT INTO chat (token, name) VALUES (:token, :name)", 
            array(":token"=>$chatToken, ":name"=>Chat::$DEFAULT_CHAT_NAME)
        );
        
        return array("token"=>$chatToken);
    }

    public static function loadChat(string $chatToken){

        $db = new DB("db/chat.db");
        $res = $db->runQuery(
            "SELECT name FROM chat WHERE token=:token", 
            array(":token"=>$chatToken)
        );

        if(count($res) === 0){ 
            Chat::setupNewChat($chatToken); 
            $name = Chat::$DEFAULT_CHAT_NAME;
            $messageList = [];
        }
        else{
            $name = $res[0]['name'];
            $messageList = Chat::fetchMessages($chatToken);
        }
   
        return array("token"=>$chatToken, "name"=>$name, "messages"=>$messageList);
    }

    public static function loadChatinfo(string $chatToken){

        $db = new DB("db/chat.db");
        $res = $db->runQuery(
            "SELECT name FROM chat WHERE token=:token", 
            array(":token"=>$chatToken)
        );

        if(count($res) === 0){ 
            Chat::setupNewChat($chatToken); 
            $name = Chat::$DEFAULT_CHAT_NAME;
        }
        else{
            $name = $res[0]['name'];
        }
   
        return array("name"=>$name);
    }

    public static function updateChatInfo(string $chatToken, array $chatInfo){

        $name = $chatInfo['name'];

        $db = new DB("db/chat.db");
        $res = $db->runQuery(
            "UPDATE chat SET `name`=:name WHERE token=:token", 
            array(":token"=>$chatToken, ":name"=>$name)
        );

        return ['res'=>'OK'];
    }

    public static function fetchMessages(string $chatToken, int $offsetStep=0){

        $fetchBatchAmount = Chat::$DEFAULT_FETCH_AMOUNT;

        $query = "
            SELECT m.`ID`, m.`text`,  u.`token`, u.`config` 
            FROM message m
            LEFT JOIN user u ON u.`ID` = m.`from`
            LEFT JOIN chat c ON c.`ID` = m.`parent_chat`
            WHERE c.`token` = :token
            ORDER BY m.`ID` DESC
            LIMIT {$fetchBatchAmount}
            OFFSET :offset
        ";

        $db = new DB("db/chat.db");
        $res = $db->runQuery($query, array(":token"=>$chatToken, ":offset"=>$offsetStep*$fetchBatchAmount) );

        $resObject = [];
        foreach($res as $row){
            $resObject[] = array(
                "id"=>$row['ID'],
                "text"=>$row['text'],
                "from"=>array_merge([ "token"=>$row['token'] ], json_decode($row['config'], true) )
            );
        }

        return array_reverse($resObject); 

    }

    public static function saveMessage(string $chatToken, string $message, string $userToken){

        $db = new DB("db/chat.db");
        $res = $db->runQuery("
            INSERT INTO message (`text`, `parent_chat`, `from`)
            SELECT :msg as text, c.ID as chatID, u.ID as userID  FROM user u
            CROSS JOIN chat c 
            WHERE c.token=:chat AND u.token=:user;
        ",
        array(":chat"=>$chatToken, ":user"=> $userToken, ":msg"=>$message), $insert_id );

        return array( 'id'=>$insert_id );

    }

    public static function fetchNewMessages(string $chatToken, string $latestID){

        $db = new DB("db/chat.db");
        $res = $db->runQuery("
            SELECT m.`ID`, m.`text`,  u.`token`, u.`config` 
            FROM message m
            LEFT JOIN user u ON u.`ID` = m.`from`
            LEFT JOIN chat c ON c.`ID` = m.`parent_chat`
            WHERE c.`token` = :token AND m.ID > :latest
            ORDER BY m.`ID` DESC
        ",
        array(":token"=>$chatToken, ":latest"=> $latestID) );

        $resObject = [];
        foreach($res as $row){
            $resObject[] = array(
                "id"=>$row['ID'],
                "text"=>$row['text'],
                "from"=>array_merge([ "token"=>$row['token'] ], json_decode($row['config'], true) )
            );
        }

        return array_reverse($resObject); 

    }

}