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
			return $pre->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	public static function last_id(){
		$instance = self::get_instance();
		return $instance->connection->lastInsertId();
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
	protected $data;
	protected $new = true;
	protected $attr = array();

	public static function fetchAll(){
		return Database::query("SELECT * FROM `" . static::$table ."`");
	}

	public static function fetch($id){
		$result = Database::run("SELECT * FROM `" . static::$table . "` WHERE " . static::$primary_key . " = ? LIMIT 1",array($id));
		// Return a new object with the information that I get from the database
		$new_obj = new static;
		$new_obj->set_attr($result[0]);
		$new_obj->new = false;
		return $new_obj;
	}

	public static function update($attr){
		$fields = '';
		$binds = array();
		foreach($attr as $key => $value){
			if(static::$primary_key == $key){
				$binds[':primary_key'] = $value;
				continue;
			}
			$binds[':'.$key] = $value;
			$fields .= ' ' . $key . ' = :' .$key .',';
		}
		$fields = rtrim($fields,',');
		return Database::run("UPDATE `" . static::$table . "`
		SET " . $fields . "
		WHERE " . static::$primary_key . " = :primary_key LIMIT 1", $binds);
	}

	public static function create($attr){
		$fields = '';
		$placeholders = '';
		$binds = array();
		foreach($attr as $key => $value){
			$binds[':'.$key] = $value;
			$fields .= ' ' . $key .',';
			$placeholders .= ' :' . $key .',';
		}
		$fields = rtrim($fields,',');
		$placeholders = rtrim($placeholders,',');
		return Database::run("INSERT INTO `" . static::$table . "`
		(" . $fields . ") VALUES (". $placeholders .")", $binds);
	}

	// Set an array as all the attributes of the record
	public function set_attr($attributes){
		$this->attr = $attributes;
	}

	// Magic method to get attributes of the record
	public function &__get($attr){
		return $this->attr[$attr];
	}

	// Magic method to set attributes of the record
	public function __set($key,$value){
		$this->attr[$key] = $value;
	}

	public function save(){
		if($this->is_new()){
			$this->create($this->attr);
			$this->__set(static::$primary_key,Database::last_id());
			$this->new = false;
		} else {
			$this->update($this->attr);
		}
	}

	public function is_new(){
		return $this->new;
	}

}