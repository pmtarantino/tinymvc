<?php

class Controller{

	public static function status_code($status_code){
		http_response_code($status_code);
	}

	public static function content_type($type){
		// Some shortcuts
		if($type == 'text' or $type == 'plain'){
			$content_type = "text/plain";
		} elseif($type == 'html'){
			$content_type = "text/html";
		} elseif($type == "json"){
			$content_type = "application/json";
		}
		$content_type = (isset($content_type)) ? $content_type : $type;

		header("Content-Type: " . $content_type);
	}

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
		$called = false;

		if(array_key_exists('url', $_GET)){
			// There is something in the URL
			$endpoint = rtrim($_GET['url'],'/'); // Remove last slash if it is in there
		} else {
			// Endpoint is empty, then I set it for the index
			$endpoint = '/';
		}

		if(array_key_exists($endpoint, $instance->routes)){
			// This endpoint was defined.
			$handler = $instance->routes[$endpoint];	
			$instance->use_handler($handler);

		} else {
			// Check if matches with an endpoint with parameters.

			foreach($instance->routes as $pattern => $handler){
				$pattern = strtr($pattern, array(':param:' => '([a-zA-Z0-9-_]+)'));
				if (preg_match('#^/?' . $pattern . '/?$#', $endpoint, $matches)) {
					$params = array_splice($matches,1);
					$instance->use_handler($handler, $params);
					$called = true;
					break;
				}
			}

			if(!$called){
				if(array_key_exists('404', $instance->config)){
					$handler = $instance->config['404'];
					$instance->use_handler($handler);
				} else {
					trigger_error("404 Error: Endpoint not configured", E_USER_ERROR);
				}
			}
		}

	}

	private static function use_handler($handler, $params=array()){
		if(!class_exists($handler)){
			trigger_error("Handler undefined", E_USER_ERROR);
		}

		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if(!method_exists($handler, $method)){
			trigger_error("Method undefined for handler", E_USER_ERROR);
		}

		call_user_func(array($handler, $method), $params);
	}

	public static function get_instance() {
        if (empty(self::$instance)) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

}