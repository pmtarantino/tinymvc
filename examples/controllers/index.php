<?php
require_once '../../src/tinymvc.php';

Router::add('/','Index');
Router::add('json','Json');
Router::add('form','Form');
Router::config('404', 'Error404');


class Index extends Controller{
	static function get(){
		self::status_code(200);
		echo "Hello World";
		echo "<br>Go to /form to see a form in action,<br>or go to /json to see a simple json.";
	}
}

class Form extends Controller{
	static function get(){
		self::status_code(200);
		self::content_type('html');
		$t = new Template();
		$t->render('form');
	}
	static function post(){
		self::status_code(200);
		self::content_type('html');
		$t = new Template();
		$t->add('name',$_POST['name']);
		$t->render('process');
	}
}


class Json extends Controller{
	static function get(){
		self::status_code(200);
		self::content_type('json');
		$json['msg'] = 'Success';
		echo json_encode($json);
	}
}

class Error404 extends Controller{
	static function get(){
		self::status_code(404);
		self::content_type('html');
		echo "<b>This page doesnt exist</b>";
	}
}

Router::serve();