<?php

require_once('src/API.class.php');
require_once('src/user/user.class.php');

$user = new \APIModule("/user");

$user->registerEndpoint("/new", function(){ // OK
    /*
        Sets up a new user and return the user token and config 
    */
    return User::setupNewUser();

}, ["method"=>"GET"]);

$user->registerEndpoint("/:token/fetch", function($params){ //OK
    /*
        Returns the config object for a given user
    */
    return User::fetchUser($params["token"]);

}, ["method"=>"GET"]);

$user->registerEndpoint("/:token/update", function($params){
    /*
        Updates the config object for a given user
    */
    return User::updateUserConfig( $params['token'], json_encode($params['json']));

}, ["method"=>"POST"]);