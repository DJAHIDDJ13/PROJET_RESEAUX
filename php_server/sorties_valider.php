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
	

	function events_non_confirme($db, $now){

		$chaine='';
		$result = pg_query($db,"SELECT * FROM events WHERE confirmation_date IS NULL and event_date >= '".$now."' AND deletion_date IS NULL");

		if(pg_num_rows($result) == 0){
			$chaine.="Aucune proposition pour le moment";
		}else{
			while ($row = pg_fetch_assoc($result)) {
			$chaine.="<tr style='border-style: none;'>
				<td style='border-style: none; padding-left:0.90cm;'>".$row['username_organizer']."</td>
				<td style='border-style: none; padding-left:0.90cm;'>".get_theme($db,$row['theme_id'])."</td>
				<td style='border-style: none; padding-left:0.90cm;'>".$row['event_title']."</td>
				<td style='border-style: none; padding-left:0.90cm;'>".$row['event_city']."</td>
				<td style='border-style: none; padding-left:1cm;'><button style='background-color:green; border-style:none; color:white; font-size:13pt; padding:10px; ' type='submit' name='accepter' ><i class='fas fa-check f00c' aria-hidden='true'></i></button></td>
				<td style='border-style: none; padding-left:1cm;'><button style='background-color:red; color: white; font-size:13pt; border-style:none; padding: 12px;' type='submit' name='refuser'><i class='fas fa-times f0c0' aria-hidden='true' ></i></button></td>
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
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<title>Recherche des utilisateurs</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>sorties à valider</title>
	</head>
	<body>
<header class="header1">
		<ul style="margin-top: 0;">
			<li><a href="acceuil_admin.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;Acceuil</a></li>
			<li><a href="sorties_valider.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Sorties à valider</a></li>
			<li><a href="user_control.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Utilisateurs</a></li>
			<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
		</ul>
</header>
<div class="divadmin">
		<div class="image1"></div>
		<div  class="image2"></div>
		<label>Bonjour <?php echo $_SESSION['username'] ?></label>
</div>
		<form method="post" action="" style="float: right; margin-right: 2cm;">
			<h1 style="margin-top: -9.5cm;">Les propositions de sorties non validées</h1>
			
				<table border="2" border-color="lightgrey" style="margin-left: 11cm; border: 1px none white; background-color: white; width: 800px; border-radius: 8px; font-size: 11pt;">
					<thead style="border-style: none;">
						<tr style="border: 1px none lightgrey;">	
							<td class="tdsorties">Organisateur</td>
							<td class="tdsorties">Theme</td>
							<td class="tdsorties">Titre</td>
							<td class="tdsorties">Ville</td>
							<td class="tdsorties">Valider</td>
							<td class="tdsorties">Refuser</td>
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
