<?php

	function get_join_button($db, $event_id) {
		$status = get_participation_status($db, $event_id);
		$dis = ($status < 0)?" disabled": "";
		$col = ($status)?($status < 0)?"gray":"green":"red";
		$mes = ($status)?($status < 0)?"-----------":"Participer":"Abandoner";
		return 	"<button style='background-color:".$col."; border-style:none; color:white; font-size:13pt; padding:10px;' type='submit' name='joindre'".$dis.">".$mes."</button>";
	}
	function join_event($db, $event_id) {
		$stat = get_participation_status($db, $event_id);
		if($stat == 1) {  // case when subscribing 
			pg_prepare($db, "db_user_join_add", "INSERT INTO Participate VALUES ($1, $2, $3, NULL)");
			pg_prepare($db, "db_user_join_update", "UPDATE Participate SET Unsubscription_date=null, Subscription_date=$1 WHERE Event_ID=$2 AND Username_participant=$3");
			$result1 = pg_query($db, "SELECT * FROM Participate WHERE username_participant='".$_SESSION["username"]."' AND Event_ID='".$event_id."'");
			$result_data = pg_fetch_assoc($result1);
			pg_free_result($result1);
			if(!$result_data) {
				$result = pg_execute($db, "db_user_join_add", array($_SESSION['username'], $event_id, date("Y-m-d")));
				pg_free_result($result);
			} else {
				$result = pg_execute($db, "db_user_join_update", array(date("Y-m-d"), $event_id, $_SESSION['username']));
				pg_free_result($result);				
			}
		} else if($stat == 0) { // case where unsubscribing
			pg_prepare($db, "db_user_unjoin", "UPDATE Participate SET unsubscription_date=$1 WHERE Event_ID=$2 AND Username_Participant=$3");
			$result = pg_execute($db, "db_user_unjoin", array(date("Y-m-d"), $event_id, $_SESSION['username']));
			pg_free_result($result);
		}
	}
	function get_event_status($db, $event_id) {
		$result = pg_query($db, "SELECT * FROM Events WHERE Event_ID=".$event_id);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		return $result_data['confirmation_date'] && !$result_data['deletion_date'] && $result_data['deadline_date'] >= date("Y-m-d") && $result_data['username_organizer'] != $_SESSION['username'];
	}
	function get_participation_status($db, $event_id) {
		$result = pg_query($db, "SELECT * FROM Events WHERE Event_ID=".$event_id);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		if($result_data['username_organizer'] == $_SESSION['username'])
			return -2;
		if(!get_event_status($db, $event_id) || (!is_friend_with($db, $result_data['username_organizer']) && $result_data['guest_id'] == 2)) {
			return -1;
		}
		
		$result = pg_query($db, "SELECT * FROM Participate WHERE Event_ID=".$event_id." AND Username_participant='".$_SESSION['username']."'");
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		if($result_data) {
			if($result_data['unsubscription_date'])
				return 1;
			else
				return 0;
		} else {
			return 1;
		}
	}
?>
