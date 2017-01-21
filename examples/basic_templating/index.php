<?php
require_once '../../src/tinymvc.php';

Router::add('/','Index');
Router::add(':param:','Custom');
Router::add('more','More');

Router::serve();

class Index{
	static function get(){
		$t = new Template();
		$t->render('index');
	}
}

class Custom{
	static function get($params){
		$t = new Template();
		$t->add('custom',$params[0]);
		$t->render('custom');
	}
}

class More{
	static function get(){
		$t = new Template();
		$t->dir('more_templates/');
		$t->add(array('a' => 'Hello', 'b' => 'World!'));
		$t->render('more');
	}
}