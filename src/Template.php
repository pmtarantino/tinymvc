<?php

class Template{
	
	private $dir_template = 'templates/';
	private $keys = array();

	public function dir($new_directory){
		$this->dir_template = $new_directory;
	}

	public function add($key, $value=false){
		if(is_string($key)){
			$this->keys[$key] = $value;
		} elseif(is_array($key)){
			$this->keys = array_merge($this->keys, $key);
		}
	}

	public function render($file){
		extract($this->keys);
		include $this->dir_template . $file . '.php';
	}


}