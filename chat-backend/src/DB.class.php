<?php

class DB{

    private $db;

    /*
        Pass the database coneection string to start a connection.
    */
    function __construct(string $dir){
        $this->openDB($dir);
    }

    /*
        Executes SQL of the given query and params.
        You can also pass a reference value to "insertID" to get the last inserted ID on the DB   
    */
    function runQuery($query, $params=[], &$insertID=-1){
        $sth = $this->db->prepare($query);
        foreach($params as $name => $value){
            $sth->bindValue($name, $value);
        }
        $sth->execute();
        $insertID = $this->db->lastInsertId();
        return $sth->fetchAll();

    }

    private function openDB(string $dir){
        try{
            $this->db =new PDO("sqlite:".$dir);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $err){
            echo "Error in opening DB : " . $err->getMessage();
        }
    }

}