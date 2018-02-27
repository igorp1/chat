<?php namespace Tests;
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__.'/tests/Test.class.php');
require_once(__ROOT__.'/src/API.class.php');

class APITest extends Test{
    use runBeforeEach;

    function runBeforeEach(){
        $this->api = new \API();
    }
    
    function testAPIModuleGetPathMaps(){

        $module1 = new \APIModule("/test");
        $module1->registerEndpoint("/test1", function(){return "test 1";});
        $module1->registerEndpoint("/test2", function(){return "test 2";});
        $module1->registerEndpoint("/hello/:name", function(){return "hello, test";});
        $maps = $module1->getPathMaps(); 

        Assert::isEqual(count($maps), 3);
        Assert::isEqual($maps["/test/test1"]->func->call($this), "test 1");
        Assert::isEqual($maps["/test/test2"]->func->call($this), "test 2");
        Assert::isEqual($maps["/test/hello/:name"]->func->call($this), "hello, test");

    }

    function testAPIRegisterRouteModule(){

        $api = $this->api;

        $module1 = new \APIModule("/test");
        $module1->registerEndpoint("/test1", function(){return "test 1";});
        $module1->registerEndpoint("/test2", function(){return "test 2";});

        $module2 = new \APIModule("/user");
        $module2->registerEndpoint("/test1", function(){return "user 1";});
        $module2->registerEndpoint("/test2", function(){return "user 2";});

        $module3 = new \APIModule("/call");
        $module3->registerEndpoint("/test1", function(){return "call 1";});
        $module3->registerEndpoint("/test2", function(){return "call 2";});

        $api->registerModule($module1);
        $api->registerModule($module2);
        $api->registerModule($module3);

        Assert::isEqual(count($api->endpoints), 6);
        Assert::isEqual($api->endpoints["/call/test1"]->func->call($this), "call 1");
        Assert::isEqual($api->endpoints["/user/test2"]->func->call($this), "user 2");

    }

    function testAPIMatchRoute(){

        $api = $this->api;

        $res1 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/:name", "/test/hello/world"]);
        Assert::isEqual($res1, 2, "res1 does not match");

        $res2 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/world", "/test/hello/world"]);
        Assert::isEqual($res2, 3, "res2 does not match");

        $res3 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/idp", "/test/hello/world"]);
        Assert::isEqual($res3, 0, "res3 does not match");

        $res4 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/:name/:id", "/test/hello/world/21"]);
        Assert::isEqual($res4, 2, "res4 does not match");

        $res5 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/:name/v1", "/test/hello/world/21"]);
        Assert::isEqual($res5, 0, "res5 does not match");

        $res6 = $this->invokeMethod($api, 'matchRoute', ["/test/hello/:name/:id", "///test//hello//world/21/"]);
        Assert::isEqual($res6, 2, "res6 does not match");

    }

    function testAPIGetRoute(){

        $api = $this->api;

        $module1 = new \APIModule("/test");
        $module1->registerEndpoint("/test1", function(){return "test 1";});

        $module2 = new \APIModule("/chat");
        $module2->registerEndpoint("/load/:token", function(){return "loaded chat";});
        $module2->registerEndpoint("/send/:token", function(){return "sent chat";});
        $module2->registerEndpoint("/send/admin", function(){return "sent admin chat";});

        $module3 = new \APIModule("/user");
        $module3->registerEndpoint("/get/:token", function(){return "fetched user";});
        $module3->registerEndpoint("/rename/:token", function(){return "renamed user";});
        
        $api->registerModule($module1);
        $api->registerModule($module2);
        $api->registerModule($module3);

        $res1 = $this->invokeMethod($api, 'getEndpoint', ["/test/hello//world"]);
        Assert::isEqual($res1, null);

        $res2 = $this->invokeMethod($api, 'getEndpoint', ["/chat///send/cbsD4SA"]);
        Assert::isEqual($res2->func->call($this), "sent chat");

        $res3 = $this->invokeMethod($api, 'getEndpoint', ["/chat/send//admin"]);
        Assert::isEqual($res3->func->call($this), "sent admin chat");

        $res4 = $this->invokeMethod($api, 'getEndpoint', ["/user//get/niFDu87"]);
        Assert::isEqual($res4->func->call($this), "fetched user");

    }

}

$apiTest = new APITest();
$apiTest->run();