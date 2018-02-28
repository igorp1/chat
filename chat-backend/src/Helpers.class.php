<?php


class Helpers{

    static function generateRandomColor($limit=255){

        $r = rand ( 0 , $limit );
        $g = rand ( 0 , $limit );
        $b = rand ( 0 , $limit );

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    static function generateToken($length=6){
        return substr(md5(uniqid($your_user_login, true)), 0, $length);
    }

}