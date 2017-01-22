<h1>All the books</h1>
<ul>
<?php foreach($books as $book):	?>
	<li><a href="book/<?php echo $book->id; ?>"><?php echo $book->title; ?></a> [<a href="book/<?php echo $book->id; ?>/delete">Delete book</a>]</li>
<?php endforeach; ?>
</ul>
<p><a href="add">Add new book</a></p>