<?php

	include_once "includes.php";
	session_start();
	$now = date("d-m-Y");
	$time = date("H:i:s");
	
	if(!isset($_SESSION['username'])){
	header('Location: connexion_form.php');
	exit;
}

	function get_organizer_username($db,$event_id){
 		
 		pg_prepare($db, "db_event_organizer_username", "SELECT username_organizer FROM events WHERE event_id=$1");
 		$result = pg_execute($db, "db_event_organizer_username", array($event_id));
		$result = pg_fetch_assoc($result);
		$result = $result['username_organizer'];
		return $result ;
	}

	function create_notification($db,$time,$now,$event_id,$message){
		pg_prepare($db, "db_user_send_ban_notif2", "INSERT INTO Notification(Notification_Content, Notification_Date, Notification_Time, Seen , Username_Receiver) VALUES ($1, $2, $3, false, $4)");
		$username_organizer = get_organizer_username($db,$event_id);
		if(isset($username_organizer)){
			pg_execute($db, "db_user_send_ban_notif2", array($message, $now, $time, $username_organizer));
		}	

	}

	function get_theme($db,$theme_ID){
		$result = pg_query($db,"SELECT theme_title FROM theme WHERE theme_ID = ".$theme_ID);
		$result = pg_fetch_assoc($result);
		$result = $result['theme_title'];
		return $result ;
	}
	

	function events_non_confirme($db,$now){

		$chaine='';
		$result = pg_query($db,"SELECT * FROM events WHERE confirmation_date IS NULL and event_date >= '".$now."' AND deletion_date IS NULL");

		if(pg_num_rows($result) == 0){
			$chaine.="Aucune proposition pour le moment";
		}else{
			while ($row = pg_fetch_assoc($result)) {
			$chaine.="<tr>
				<td>".$row['username_organizer']."</td>
				<td>".get_theme($db,$row['theme_id'])."</td>
				<td>".$row['event_title']."</td>
				<td>".$row['event_city']."</td>
				<td><button type='submit' name='accepter' >Accepter</button></td>
				<td><button type='submit' name='refuser'>Refuser</button></td>
				<input type='hidden' name='event_id' value='".$row['event_id']."' />";
			}
		}
		return $chaine;
	}

	function accepter_Event($db,$event_id,$now,$time){

		if(isset($event_id)){
			$result=pg_query($db,"UPDATE events SET confirmation_date='".$now."', modification_date='".$now."',confirmed ='true' WHERE event_id='".$event_id."'");
			 
			if($result != null){
				$message =" Votre proposition est accéptée";
				echo $message;
				create_notification($db, $time, $now, $event_id ,$message);
			}
		}
	}

	function refuser_Event($db,$event_id,$now,$time){
		if(isset($event_id)){
			$result=pg_query($db,"UPDATE events SET confirmation_date='".$now."', modification_date='".$now."',confirmed ='false' WHERE event_id='".$event_id."'");
			if($result != null){
				$message =" Votre proposition est refusée";
				create_notification($db, $time, $now, $event_id ,$message);
			}
		}	
	}

	

	if(isset($_POST['accepter'])){
		echo 'clique accept';
		$event_id = $_POST['event_id'];
		if(isset($event_id)){

			accepter_Event($db,$event_id,$now,$time);	
		}
		header('Location: sorties_valider.php');
	}

	if(isset($_POST['refuser'])){
		$event_id = $_POST['event_id'];
		if(isset($event_id)){
			refuser_Event($db,$event_id,$now,$time);	
		}
		header('Location: sorties_valider.php');
	}
	

?>


<!DOCTYPE html>
<html>
	<header>
		<title></title>
	</header>
	<body>

		Hello  
			<a href="acceuil_admin.php">Acceuil</a>
			<a href="sorties_valider.php">Sorties à valider</a>
			<a href="user_control.php">Utilisateurs</a>
			<a href="deconnexion.php">Se Déconnecter</a>
		<p>Bonjour </br> 

		    <label>Username :<?php echo $_SESSION['username'] ?></label></br></br>
		    

	    </p>

		<form method="post" action="" >
			<h1>Les sorties à confirmer </h1>
			
				<table border="2" border-color="black">
					<thead>
						<tr>	
							<td>Organisateur</td>
							<td>Theme</td>
							<td>Titre</td>
							<td>Ville</td>
							<td>Valider</td>
							<td>Refuser</td>
						</tr>
					</thead>
					<tbody>
						<?php echo events_non_confirme($db,$now) ?>
					</tbody>
				</table>
			
		</form>
	</body>
	<footer>
	</footer>
</html>
