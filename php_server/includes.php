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
	function is_friend_with($db, $username) {
		$result = pg_query($db, "SELECT * FROM Invitation WHERE ((Username_Sender='".$_SESSION['username']."' AND Username_Receiver='".$username."') OR (Username_Sender='".$username."' AND Username_Receiver='".$_SESSION['username']."')) AND Acceptance_Time IS NOT NULL AND Acceptance_Date IS NOT NULL");
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		if($result_data) {
			return true;
		}
		return false;
	}
	function show_freinds($db,$username){
		pg_prepare($db, "db_freinds_get", "SELECT username_sender FROM Invitation WHERE username_receiver=$1  AND Acceptance_Time IS NOT NULL AND Acceptance_Date IS NOT NULL");
		$result = pg_execute($db, "db_freinds_get", array($username));
		if(pg_num_rows($result)){
		echo'	<table style="background-color: white; border:1px solid lightgrey;border-radius: 8px; margin-left: 0.75cm; width: 350px; margin-top:1cm;">';
				echo '<tr style="color:#2dc997; font-size:14pt;"><td style="padding-top:0.5cm; padding-left:1cm; padding-bottom:0.5cm;" >Amis</td> </tr>';
				while ($row = pg_fetch_row($result)) {
					echo '<tr>';
				$count = count($row);
				$y = 0;
				while ($y < $count) {
					$c_row = current($row);
					echo '<td  style="padding-top:0.5cm; padding-left:1cm; padding-bottom:0.5cm;">' . $c_row . '</td>';

					next($row);
					$y = $y + 1;
				}
					echo '</tr>';
			}
			echo "</table>";
	}
}

?>
