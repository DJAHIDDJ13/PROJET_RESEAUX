<?php
	include_once 'connexion.conf.php';
	$db = connexion_DB();
	session_start();

	function get_image($db, $username, $event) {
		if($event != 0) {
			$result = pg_query($db, "SELECT event_picture FROM Events WHERE event_id=".$event);
			$result_data = pg_fetch_assoc($result);
			pg_free_result($result);
			if($result_data)
				if($result_data['event_picture'])
					return 'events/'.$result_data['event_picture'];
			return 'friends.jpeg';
		} else if($username){
			$result = pg_query($db, "SELECT user_picture FROM Users WHERE Username='".$username."'");
			$result_data = pg_fetch_assoc($result);
			pg_free_result($result);
			if($result_data)
				if($result_data['user_picture'])
					return 'users/'.$result_data['user_picture'];
			return 'default.jpeg';
		}
	}
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
