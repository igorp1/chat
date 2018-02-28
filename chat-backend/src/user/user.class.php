<?php

require_once('src/DB.class.php');
require_once('src/Helpers.class.php');


class User{
    
    public static function setupNewUser(){

        $token = Helpers::generateToken();

        $emptyConfig = array(
            "name"=>"Guest",
            "color"=>Helpers::generateRandomColor()
        );

        $newUser = array(":token"=>$token, ":config"=>json_encode($emptyConfig));

        // ADD TO DB =>
        $db = new DB("db/chat.db");
        $db->runQuery("INSERT INTO user (token, config) VALUES (:token, :config)", $newUser);
        
        return array("token"=>$token, "config"=>$emptyConfig);
    }

    public static function fetchUser(string $token){

        $db = new DB("db/chat.db");
        $res = $db->runQuery("SELECT config FROM user WHERE token=:token", array(":token"=>$token))[0];
        $config = json_decode( $res['config'], true );
        return $config;

    }

    public static function updateUserConfig(string $token, string $config){
        $db = new DB("db/chat.db");
        $res = $db->runQuery(
            "UPDATE user SET config = :config WHERE token=:token", 
            array(":token"=>$token, ":config"=> $config)
        );
        return ["res"=>"OK"];
    }

}