<?php namespace Tests;
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__.'/tests/Test.class.php');
require_once(__ROOT__.'/src/DB.class.php');

class DBTests extends Test{
    
    use runBeforeEach;
    function runBeforeEach(){
        $this->db = new \DB("db/test_chat.db");
    }

    use runAfterEach;
    function runAfterEach(){
        $cleanup = 'DELETE * FROM user; DELETE * FROM chat';
        $this->db->runQuery($cleanup);
    }

    function testDBRunQuery(){

        $TOKEN = 'abcxyz';

        $this->db->runQuery("
        INSERT INTO user 
        (token) VALUES (:token)
        ", ['token'=>$TOKEN]);

        $res = $this->db->runQuery('SELECT token, config FROM user');

        Assert::isEqual(count($res), 1);
        Assert::isEqual($res[0]['token'], $TOKEN);

    }
    

}

$dbTest = new DBTests();
$dbTest->run();