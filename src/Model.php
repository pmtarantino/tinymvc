<?php

class Database{

	private static $instance;
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;
	private $connection = false;

	public static function connect($dbhost,$dbuser,$dbpass,$dbname){
		$instance = self::get_instance();

		$dsn = 'mysql:dbname='.$dbname.';host='.$dbhost;
		$user = $dbuser;
		$password = $dbpass;

		try {
		    $instance->connection = new PDO($dsn, $user, $password);
		} catch (PDOException $e) {
		    trigger_error("Database connection failed: " . $e->getMessage(), E_USER_ERROR);
		}
	}

	public static function query($query){
		$instance = self::get_instance();
		if(!$instance->connection){
			trigger_error("Database connection not configured", E_USER_ERROR);
		} else {
			return $instance->connection->query($query);
		}
	}

	public static function run($query,$values){
		$instance = self::get_instance();
		if(!$instance->connection){
			trigger_error("Database connection not configured", E_USER_ERROR);
		} else {
			$pre = $instance->connection->prepare($query);
			$pre->execute($values);
			return $pre->fetchAll();
		}
	}

	public static function get_instance() {
        if (empty(self::$instance)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}

class Model{

	protected static $table;
	protected static $primary_key = 'id';

	public static function fetchAll(){
		return Database::query("SELECT * FROM " . static::$table);
	}

	public static function fetch($id){
		return Database::run("SELECT * FROM " . static::$table . " WHERE " . static::$primary_key . " = ? LIMIT 1",array($id));
	}
	
}

class Dibi extends Model{
	protected static $table = 'db';
	protected static $primary_key = 'Host';
}