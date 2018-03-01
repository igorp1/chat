<?php
/*
    All URIs get routed through here and processed by the API.
*/

// intantiate API
require_once('./src/API.class.php');
$api = new API();

// config API
$api->setCrossOriginWhiteList(['*']);

// register hello endpoint, serves an example and test if the server is up
$hello_module = new APIModule("/hello");
$hello_module->registerEndpoint("/:name", 
    function($params){ 
        $name=$params['name'];
        $styles = 'text-align:center;font-family:monospace;margin-top:6em;';
        return ["<h1 style='{$styles}'>👋🏾 Hello, {$name}!</h1>"]; 
    }, 
    ['method'=>'GET', 'response'=>'HTML']
);
$api->registerModule( $hello_module );

// register api endpoints
require_once('./src/user/user.api.php');
$api->registerModule( $user );

require_once('./src/chat/chat.api.php');
$api->registerModule( $chat );

// process the request
$api->processRequest($_REQUEST, $_SERVER);
