<?php

class Controller{

}

class Router{

	private static $instance;
	private $routes = array();
	private $config = array();

	public static function add($endpoint, $handler){
		$instance = self::get_instance();
		$instance->routes[$endpoint] = $handler;
	}

	public static function config($exception, $handler){
		$instance = self::get_instance();
		$instance->config[$exception] = $handler;
	}

	public static function serve(){
		$instance = self::get_instance();
		if(array_key_exists('url', $_GET)){
			// There is something in the URL
			$endpoint = rtrim($_GET['url'],'/'); // Remove last dash if is in there
		} else {
			// Endpoint is empty, then I set it for the index
			$endpoint = '/';
		}

		if(array_key_exists($endpoint, $instance->routes)){
			// This endpoint was defined.
			$handler = $instance->routes[$endpoint];	
			$instance->use_handler($handler);

		} else {
			if(array_key_exists('404', $instance->config)){
				$handler = $instance->config['404'];
				$instance->use_handler($handler);
			} else {
				trigger_error("404 Error: Endpoint not configured", E_USER_ERROR);
			}
		}

	}

	private static function use_handler($handler){
		if(!class_exists($handler)){
			trigger_error("Handler undefined", E_USER_ERROR);
		}

		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if(!method_exists($handler, $method)){
			trigger_error("Method undefined for handler", E_USER_ERROR);
		}

		call_user_func(array($handler, $method));
	}

	public static function get_instance() {
        if (empty(self::$instance)) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

}