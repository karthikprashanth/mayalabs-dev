<?php
	mysql_connect("localhost", "root", "") or die(mysql_error());
	mysql_select_db("sample") or die(mysql_error());
	$fPtr = fopen($_FILES['image']['tmp_name'],'r');
	$data = mysql_real_escape_string(fread($fPtr,$_FILES['image']['size']));
	$query = "INSERT INTO xyz(image) VALUES('$data')";
	$result = mysql_query($query) or die(mysql_error());
	$id = mysql_insert_id();
	
	$query = "SELECT * FROM xyz WHERE id = " . $id;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	file_put_contents("sample.jpg",$row['image']);
	echo "<img src = 'sample.jpg'>";
?>