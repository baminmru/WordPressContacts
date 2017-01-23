<?php
include_once('config.php');

class Database 
{
	public static $config;
	
	
	private static $cont  = null;
	
	public function __construct() {
		
	
	}
	
	
	public static function connect()
	{
	
		global $config;
		
        self::$config = $config;
	
	   // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$config['db']['server'].";"."dbname=".self::$config['db']['database'], self::$config['db']['username'], self::$config['db']['password']);  
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
        }
       } 
	   self::$cont->exec("SET CHARACTER SET utf8");
       return self::$cont;
	}
	
	public static function disconnect()
	{
		self::$cont = null;
	}
}
?>