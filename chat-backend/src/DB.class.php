<?php

class DB{

    private $db;

    function __construct($dir){
        $this->openDB($dir);
    }

    private function openDB($dir){
        try{
            $this->db =new PDO("sqlite:".$dir);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $err){
            echo "Error in opening DB : " . $err->getMessage();
        }
    }


    function runQuery($query, $params=[]){
        $sth = $this->db->prepare($query);
        foreach($params as $name => $value){
            $sth->bindValue($name, $value);
        }
        $sth->execute();
        return $sth->fetchAll();
        
    }

}