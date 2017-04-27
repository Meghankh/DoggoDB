<?php
class Database
{

/*==========================================
	THIS FILE MUST BE INCLUDED BY EVERY FILE
	ONCE AND ONLY ONCE
	
	THE VARAIBLES ARE USED TO CONNECT TO MY COMPUTERS LOCALHOST
	EACH COMPUTER IS DIFFERENT
============================================*/
    private static $dbName = 'DoggoDB' ;
    private static $dbHost = 'doggo.cwmarjk4rlar.us-west-1.rds.amazonaws.com';
    private static $dbUsername = 'root';
    private static $dbUserPassword = 'Password123';
     
    private static $cont  = null;
     
    public function __construct() {
        die('Init function is not allowed');
    }
     
    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {     
        try
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName,
           self::$dbUsername, self::$dbUserPassword); 
        }
        catch(PDOException $e)
        {
          die($e->getMessage()); 
        }
       }
       return self::$cont;
    }
     
    public static function disconnect()
    {
        self::$cont = null;
    }
}
<<<<<<< HEAD
?>
=======
?>
>>>>>>> 8f00b309670efc5a92ee7518f571fb461a6b63fc
