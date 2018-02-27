<?php namespace user;

class User{

    public static function loadUserFromToken(string $token){
        return ["user" => $token];     
    }

}