<?php

require_once('src/API.class.php');
require_once('src/chat/chat.class.php');

$chat = new \APIModule("/chat");

$chat->registerEndpoint("/new", function(){
    /*
        Sets up a new chat and return the new token  
    */
    return Chat::setupNewChat();

}, ["method"=>"GET"]);

$chat->registerEndpoint("/:token/load", function($params){
    /*
        Loads a chat or creates a new one with the given token 
    */
    return Chat::loadChat($params["token"]);

}, ["method"=>"GET"]);

$chat->registerEndpoint("/:token/info", function($params){
    /*
        Loads a chat or creates a new one with the given token 
    */
    return Chat::loadChatInfo($params["token"]);

}, ["method"=>"GET"]);

$chat->registerEndpoint("/:token/info/update", function($params){
    /*
        Loads a chat or creates a new one with the given token 
    */
    return Chat::updateChatInfo($params["token"], $params['json']);

}, ["method"=>"POST"]);

$chat->registerEndpoint("/:token/history/:step", function($params){
    /*
        Fetches messages for given chat applying an offset step
    */
    return Chat::fetchMessages($params["token"], $params["step"]);

}, ["method"=>"GET"]);

$chat->registerEndpoint("/:token/send", function($params){
    /*
        Saves a new message to the db
    */
    return Chat::saveMessage($params["token"], $params["json"]["text"], $params["json"]["userToken"]);

}, ["method"=>"POST"]);

$chat->registerEndpoint("/:token/update/:last", function($params){
    /*
        Retrieves new messages from the db
    */
    return Chat::fetchNewMessages($params["token"], $params["last"]);

}, ["method"=>"GET"]);

$chat->registerEndpoint("/:token/poll/:last", function($params){
    /*
        Retrieves new messages from the db using long polling
    */

    $timeStart = time();
    $POLLING_INTERVAL = 30; // 20 seconds
    
    $newMessages = Chat::fetchNewMessages($params["token"], $params["last"]);
    $hasNewMessages = count($newMessages) > 0;

    while(!$hasNewMessages && (time() - $timeStart < $POLLING_INTERVAL )){

        // fetch something new (?)
        $newMessages = Chat::fetchNewMessages($params["token"], $params["last"]);
        $hasNewMessages = count($newMessages) > 0;

        // chill out for .5 seconds
        usleep(500000);

    }
	
    return  $newMessages;

}, ["method"=>"GET"]);