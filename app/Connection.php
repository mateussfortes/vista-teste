<?php

  class Connection {

    protected static $conn;
  
    public static function getConnection() {
      
      if(empty(self::$conn)) {
        
        $connection = parse_ini_file('env.ini');
        
        $host = $connection['host'];
        $user = $connection['user'];
        $password = $connection['password'];
        $dbname = $connection['table'];

        self::$conn = new PDO("mysql:host={$host};dbname={$dbname}", $user, $password);
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
      }
  
      return self::$conn;

    }
  }