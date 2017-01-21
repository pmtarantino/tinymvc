<?php
require_once '../../src/tinymvc.php';

Router::add('/','Index');
Router::add('two','Two');
Router::add('two/:param:','Two');

Router::serve();

class Index{
	static function get(){
		echo "Hello, world";
		echo '<p><a href="two">Go to two.</a></p>';
	}
}

class Two{
	static function get($params){
		echo "This is two! Add whatever you want to the end of the URL, for example, <em>two/three</em>";
		if(isset($params[0])){
			echo "<p>You just added " . $params[0] . "</p>";
		}
	}
}