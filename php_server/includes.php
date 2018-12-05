<?php
	include_once 'connexion.conf.php';
	$db = connexion_DB();
	session_start();


	function get_user_Information($db , $username){

		pg_prepare($db, "db_infos", "SELECT * from users WHERE (Username=$1) ");
		$result = pg_execute($db, "db_infos", array($username));
		$result_data = pg_fetch_assoc($result);
		
		$_SESSION['user_picture'] = $result_data['user_picture'];
		$_SESSION['last_name'] = $result_data['user_picture'];
		$_SESSION['first_name'] = $result_data['user_picture'];
		$_SESSION['place_of_birth'] = $result_data['user_picture'];
	}

?>
