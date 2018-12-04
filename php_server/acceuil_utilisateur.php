<?php
	include_once('includes.php');
	session_start();

	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
		header('Location: connexion_form.php');
		exit;
	}
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueuil_admin.php');
		exit;
	}
	if(isset($_POST['event_id'])) {
		$event_id = $_POST['event_id'];
		join_event($db, $event_id, date("Y-m-d"));
		header('Location: acceuil_utilisateur.php');
		exit;
	}
	function get_theme($db,$theme_ID){
		$result = pg_query($db,"SELECT theme_title FROM theme WHERE theme_ID = ".$theme_ID);
		$result = pg_fetch_assoc($result);
		$result = $result['theme_title'];
		return $result ;
	}

	function get_last_events($db, $n, $now) {
		$result = pg_query($db, "SELECT * FROM events WHERE confirmation_date IS NOT NULL and event_date >= '".date("Y-m-d")."' AND deletion_date IS NULL ORDER BY proposition_date DESC");
		$chaine = "";
		$i = 0;
		if(pg_num_rows($result) == 0) {
			$chaine .= "Aucune proposition pour le moment";
		} else {
			while ($row = pg_fetch_assoc($result)) {
			$status = get_participation_status($db, $row['event_id']);
			$col = ($status)?"green":"red";
			$mes = ($status)?"Participer":"Abandoner";
			$chaine .= 
				"<tr style='border-style: none;'>
					<td style='border-style: none; padding-left:0.90cm;'><a href='user_profile.php?username=".$row['username_organizer']."'>".$row['username_organizer']."</a></td>
					<td style='border-style: none; padding-left:0.90cm;'>".get_theme($db,$row['theme_id'])."</td>
					<td style='border-style: none; padding-left:0.90cm;'><a href='event_page.php?event_id=".$row['event_id']."'>".$row['event_title']."</a></td>
					<td style='border-style: none; padding-left:0.90cm;'>".$row['event_city']."</td>
					<td style='border-style: none; padding-left:1cm;'>
						<form action='' method='post'>
							<button style='background-color:".$col."; border-style:none; color:white; font-size:13pt; padding:10px;' type='submit'>".$mes."</button>
							<input type='hidden' name='event_id' value='".$row['event_id']."'>
						</form>
					</td>
				</tr>";
				
				if($i >= $n-1)
					break;
				$i = $i+1;
			}
		}
		pg_free_result($result);
		return $chaine;
	}

	function join_event($db, $event_id, $now) {
		if(get_participation_status($db, $event_id)) {  // case when subscribing 
			pg_prepare($db, "db_user_join_add", "INSERT INTO Participate VALUES ($1, $2, $3, NULL)");
			pg_prepare($db, "db_user_join_update", "UPDATE Participate SET Unsubscription_date=null, Subscription_date=$1 WHERE Event_ID=$2 AND Username_participant=$3");
			$result1 = pg_query($db, "SELECT * FROM Participate WHERE username_participant='".$_SESSION["username"]."' AND Event_ID='".$event_id."'zefazeéé&é'..55");
			$result_data = pg_fetch_assoc($result1);
			pg_free_result($result1);
			if($result_data) {
				$result = pg_execute($db, "db_user_join_add", array($_SESSION['username'], $event_id, date("Y-m-d")));
				pg_free_result($result);
			} else {
				$result = pg_execute($db, "db_user_join_update", array(date("Y-m-d"), $event_id, $_SESSION['username']));
				pg_free_result($result);				
			}
		} else { // case where unsubscribing
			pg_prepare($db, "db_user_unjoin", "UPDATE Participate SET unsubscription_date=$1 WHERE Event_ID=$2 AND Username_Participant=$3");
			$result = pg_execute($db, "db_user_unjoin", array(date("Y-m-d"), $event_id, $_SESSION['username']));
			pg_free_result($result);
		}
	}
	function get_participation_status($db, $event_id) {
		$result = pg_query($db, "SELECT * FROM Participate WHERE Event_ID=".$event_id." AND Username_participant='".$_SESSION['username']."'");
		$result_data = pg_fetch_assoc($result);
		if($result_data) {
			if($result_data['unsubscription_date'])
				return true;
			else
				return false;
		} else {
			return true;
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<title>Acceuil</title>
	</head>
	<body>
		<header class="header1">
				<ul style="margin-top: 0;">
					<li><a href="acceuil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;Acceuil</a></li>
					<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
					<li><a href="user_propose.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
					<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
				</ul>
		</header>
		<div class="divadmin">
				<div class="image1"></div>
				<div  class="image2" style="background-image: url(get_user_image.php?username=<?php echo $_SESSION["username"]?>);"></div>
				<div  style="margin-top: 1cm; right: 3cm; ">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
		<h1 style="margin-top: -9.5cm;">Les evenements récents</h1>
			<table border="2" border-color="lightgrey" style="margin-left: 11cm; border: 1px none white; background-color: white; width: 800px; border-radius: 8px; font-size: 11pt;">
				<thead style="border-style: none;">
						<tr style="border: 1px none lightgrey;">	
						<td class="tdsorties">Organisateur</td>
						<td class="tdsorties">Theme</td>
						<td class="tdsorties">Titre</td>
						<td class="tdsorties">Ville</td>
						<td class="tdsorties"></td>
					</tr>
				</thead>
				<tbody>
					<?php echo get_last_events($db, 5, date("Y-m-d")) ?>
				</tbody>
			</table>
	</body>
	
</html>
