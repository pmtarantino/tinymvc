<h1>Add Book</h1>
<form method="POST">
<p>Title:
<br/><input type="text" name="title" required></p>
<p>Author:
<br/><input type="text" name="author" required></p>
<p>Publisher:
<br/><input type="text" name="publisher" required></p>
<p>ISBN:
<br/><input type="text" name="isbn" required></p>
<p><input type="submit" value="Add">
</form>
<?php if(isset($msg)): ?>
	<p><b><?php echo $msg; ?></b></p>
<?php endif; ?>