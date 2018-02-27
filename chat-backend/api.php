<?php
/*
    All URIs get routed through here and processed by the API.
*/

// intantiate API
require_once('./src/API.class.php');
$api = new API();

// config API
$api->setCrossOriginWhiteList(['*']);

// register api endpoints
require_once('./src/user/user.api.php');
$api->registerModule( $user );


// process the request
$api->processRequest($_REQUEST, $_SERVER);