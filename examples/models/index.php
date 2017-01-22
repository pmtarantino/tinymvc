<?php
require_once '../../src/tinymvc.php';

// Config with your own credentials
// Database::connect(HOST, DBUSER, DBPASS, DBNAME);

class Book extends Model{
	protected static $table = 'books';
}

Router::add('/','Index');
Router::add('add','AddBook');
Router::add('book/:param:','BookPage');
Router::add('book/:param:/delete','BookDelete');

class Index extends Controller{
	static function get(){
		$books = Book::fetchAll();
		$t = new Template();
		$t->add('books',$books);

		self::status_code(200);
		$t->render('index');
	}
}

class AddBook extends Controller{
	static function get($params){
		$t = new Template();
		self::status_code(200);
		$t->render('add');
	}
	static function post(){
		$book = new Book;
		$book->title = $_POST['title'];
		$book->author = $_POST['author'];
		$book->publisher = $_POST['publisher'];
		$book->isbn = $_POST['isbn'];
		if($book->save()){
			self::status_code(201);
			$msg = 'Book added';
		} else {
			self::status_code(400);
			$msg = 'Book not added. Try again';
		}

		$t = new Template;
		$t->add('msg', $msg);
		$t->render('add');
	}
}

class BookPage extends Controller{
	static function get($params){
		$book = Book::fetch($params[0]);
		$t = new Template();
		$t->add('book',$book);

		self::status_code(200);
		$t->render('book');
	}
}

class BookDelete extends Controller{
	static function get($params){
		$book = Book::fetch($params[0]);
		if($book->delete()){
			self::status_code(200);
			$msg = 'Book deleted';
		} else {
			self::status_code(400);
			$msg = 'Book not deleted. Try again';
		}

		$t = new Template;
		$t->add('msg', $msg);
		$t->render('delete');
	}
}

Router::serve();