<?php namespace user;

require_once('src/API.class.php');
require_once('src/user/user.class.php');

$user = new \APIModule("/user");

$user->registerEndpoint("/load/:token", function($params){
    
    return $params;

    //$token = $params['token'];
    //return User::loadUserFromToken($token);
}, ["method"=>"GET"]);

$user->registerEndpoint("/load/:id/:token", function($params){
    
    return $params;

    //$token = $params['token'];
    //return User::loadUserFromToken($token);
}, ["method"=>"GET"]);
