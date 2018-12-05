<?php
	include_once('includes.php');
	include_once('event_joining_utility.php');


	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
		header('Location: connexion_form.php');
		exit;
	}
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueil_admin.php');
		exit;
	}
	if(isset($_POST['event_id'])) {
		join_event($db, $_POST['event_id']);
		header('Location: accueil_utilisateur.php');
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
			$chaine .= 
				"<tr style='border-style: none;'>
					<td style='border-style: none; padding-left:0.90cm;'><a href='user_profile.php?username=".$row['username_organizer']."'>".$row['username_organizer']."</a></td>
					<td style='border-style: none; padding-left:0.90cm;'>".get_theme($db,$row['theme_id'])."</td>
					<td style='border-style: none; padding-left:0.90cm;'><a href='event_page.php?event_id=".$row['event_id']."'>".$row['event_title']."</a></td>
					<td style='border-style: none; padding-left:0.90cm;'>".$row['event_city']."</td>
					<td style='border-style: none; padding-left:1cm;'>
						<form action='' method='post'>".get_join_button($db, $row['event_id'])
						."<input type='hidden' name='event_id' value='".$row['event_id']."'>
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
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>accueil</title>
	</head>
	<body>
		<header class="header1">
				<ul style="margin-top: 0;">
					<li><a href="accueil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;accueil</a></li>
					<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
					<li><a href="user_propose.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
					<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
				</ul>
		</header>
		<div class="divadmin">
				<div class="image1"></div>
				<div  class="image2"></div>
				<div  style="margin-top: 1cm; right: 3cm; ">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
		<h1 style="margin-top: -9.5cm;">Les Evenements récents</h1>
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
