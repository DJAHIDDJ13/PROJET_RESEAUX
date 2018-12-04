<?php
	include_once "includes.php";
	session_start();
	$id = $_GET['username'];
	if(filter_var($id, FILTER_SANITIZE_STRING)) {
		$result = pg_query("SELECT user_picture, mime_type FROM Users WHERE Username='".$_GET['username']."'");
		$row = pg_fetch_assoc($result);
		pg_free_result($result);
		if($row['user_picture'] != null) {
			header("Content-type: ".$row['mime_type']);
			echo pg_unescape_bytea($row['user_picture']);
		} else {
			header("Content-type: image/jpeg");
			echo file_get_contents("./img/default.jpeg");
		}
	}
?>
