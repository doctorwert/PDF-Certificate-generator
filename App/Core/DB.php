<?php

class DB extends Singleton
{
    private static $db;

    private function _init()
    {
        if ( empty(self::$db) ){
            self::$db = new PDO('mysql:host=' . __DB_HOST__ . ';dbname=' . __DB_NAME__.';charset=utf8mb4', __DB_USER__, __DB_PWD__);
            self::$db->exec("set names utf8mb4");
        }
        
        return self::$db;
    }
    
    public static function getDB()
    {
        $dbs = static::getInstance();
        return $dbs->_init();
    }

}
