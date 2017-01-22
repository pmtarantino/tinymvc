# tinymvc
This is a minimal MVC framework as an excercise.

## Purpose and Design

The purpose of this framework is to provide a lightweight MVC framework with minimal tools that allows to prototype simple applications with controllers, models and the simplest templates.

It has an unique access point, the `index.php` file that receives all the requests and handles them.

To do that, the MVC provides a `Router`, which is in charge of identify the endpoints (which its respective parameters) and decides which `Controller` must handle the request.

The `Router` provides very basic functionality. It allows to set a handler for each endpoint (allowing endpoints with dynamic parameters, such as `item/:param:`, for example), and also set a handler for 404 errors. It would be very easy to extend the framework to allow configurations for other class of error, though.

The idea of a `Router` configuration instead of handling the request upon the `Controller` (such as large frameworks do) is to provide a simpler and easier interface, allowing reusing `Controllers` for different URLs. It does not require a specified structure of directories and files, mimicking the endpoint paths.

Each controllers has methods to response to the requests - each one with the name of the proper method. A default `all()` could have been added to response to `REQUEST METHODS` that have not been defined - it could be added to the next version. Altough a controller can be any class with a `get()` method for example (or `post()` or anything), if it extends `Controller` class it can use some helpers to define a status code and the content type of the response (for example, setting it as `application/json` for API development as simple as `content_type('json')`.)

The """template engine""" works as a PHP include. It allows passing variables to use in the template with simple methods.

The `Model` class is it used as a base for all the Models and allows interacting with a MySQL Database easily. For example, an object of class `Book` that extends `Model` is allowed to be saved to the database easily, to alter its attribute and to be deleted. The same with the creation of the new records into the database.

The framework is very minimal but allows to create prototypes and simple applications. There are a basic examples to check its use: Basic Routing, Basic Templates, Controllers and Models, each one with a little more """complexity""" than the previous one, being Models the closer to a complete (but simple) app.

I would have liked to add some Unit Testing and a finished app as an example.

## Documentation

To start using it, just include `tinymvc.php` in your `index.php` file.

### Routing

The Router class (used as static) allows to configure handlers for each endpoint. As its name indicates, it is the one who decides what to do for each access.

It has only three methods. `add`, `config` and `serve`.

`add` configure a new handler for a request. 

```
Router::add(URL, HANDLER)
```

Both parameters are strings. If the URL has a dynamic variable, you can use `:param:`. For example,

```
Router::add('article/:param:', 'ArticleHandler')
```

would catch all the urls like `article/1`, `article/this-is-an-article` and so. All of them would be handled by `ArticleHandler` (we would see more about it in _Controllers_.)

For the index handler, the URL must be `/`.

The `Router` class also allows to config a handler in case it can not find the proper handler. For that, just use

```
Router::config('404',HANDLER)
```

and set your own handler to use in case of a 404 error.

At last, to serve your handlers, you must call

```
Router::serve()
```

### Controllers

The controllers are pretty simple, although they are not required at all and you could ignore them.

Using the `Controller` class as a base or not, you must follow some guidelines so they work with the `Router` class.

Each controller (or handler) must have at least one method to respond to the request. Once the `Router` detects the correct handler, it will call the method based on the request type. For example, let's suppose the handler has been already identified as `OurHandler`. If the request method is `GET`, it will call `OurHandler::get()`. If it is `POST`, it will call `OurHandler::post()` and so on.

The methods also receive an array paremeter, that are the variables from the URL/endopoint in the order from the URL. For example, this route

```
Route::add('article/:param:/:param:','Handler')
```

would pass an array of two items to Handler::get()

```
class Handler {
	public static function get($params) {
		// $params[0] and $params[1]
	}
}
```

If your handler extends the `Controller` class, you can use `self::status_code(INT)` to set an status code for the response, and `self::content_type(STRING)` to set a content type. The `string` can be a full content type, like `text/plain` or `text/html`, or you can use some shortcuts as `plain`, `html` or `json`.

The controllers logic is pretty basic, but allows a lot of freedom too :D

### Templates

The templates are also basic too. They must be in `.php` and although by default they are located at the `templates` directory, you can change them.

The `Template` class has only three methods:

`dir()` allows changing the default directory where the template files live. `add()` adds keys to be used in the template file, through a `key`,`value` format or through an array. Last, `render()` display the file.

A simple example demostrates its use:

```
$t = new Template;
$t->dir('another_directory'); // We are changing the directory where it will look up the files
$t->add('title','Example'); // The template file will have access to the $title variable containing 'Example'
$t->add(array('title' => 'Example')); // Same as previous line
$t->render('my_file'); // It will render another_directory/my_file.php
```

### Models

Models allows interacting with MySQL records. It is implented through two classes: `Database` and `Model`, though the first one would be used only to configurate the connection:

```
Database::connect(HOST, DBUSER, DBPASS, DBNAME);
```

Once you are connected to the database, you can start creating your models.

To create them, create a class that extends `Model` and set the table name (and optionally, the primary key of the table if it is not `id`):

```
class Book extends Model{
	protected static $table = 'books';
	protected static $primary_key = 'isbn'; // Optional, by default is 'id'
}
```
Once you have this, you can start using it.

#### Creating new record

Creating new records is as simple a creating the object and applying `save()` to it:

```
$book = new Book;
$book->title = 'Harry Potter';
$book->save()
```

The new book is now in the database.

#### Fetching an specific record

You can get a record looking for its primary ket with `fetch()`. It will return an object.

```
$book = Book::fetch('9875493942') // Getting by ISBN
```

#### Updating a record

Once you fetch it, you can update its attributes and save it again. The `save()` method works as an "update or create".

```
$book = Book::fetch('9875493942')
$book->author = 'New Author';
$book->save();
```

#### Deleting a record

Just as we use `save()`, we can use `delete()` to erase the record from the database.

```
$book = Book::fetch('9875493942')
$book->delete();
```

#### Fetching all records

If we want to fetch all the records from the table, we may use `fetch()`:

```
$books = Book::fetchAll();
foreach($books as $book){
	// $book->title...
}
```

I think there should be at least another method that allows for query with specific conditions, such as `WHERE` clauses, `LIMIT` and `ORDER`.