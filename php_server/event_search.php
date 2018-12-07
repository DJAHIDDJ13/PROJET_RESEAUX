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
		header('Location: event_search.php');
		exit;
	}
	function get_theme($db,$theme_ID){
		$result = pg_query($db,"SELECT theme_title FROM theme WHERE theme_ID = ".$theme_ID);
		$result = pg_fetch_assoc($result);
		$result = $result['theme_title'];
		return $result ;
	}

	function get_themes($db){
		$result = pg_query($db,"SELECT * FROM theme");
		return $result ;
	}

	function search_events($db,$title,$start_date,$end_date,$organizer,$theme) {
		$title = trim($title);
		$organizer = trim($organizer);
		pg_prepare($db, "db_event_search", "SELECT username_organizer ,theme_id ,event_title, event_city ,event_id FROM events Where (STRPOS(event_title, $1)>0 OR $1='') AND (event_date>=$2 OR $2 IS NULL) AND (event_Date<=$3 OR $3 IS NULL) AND (STRPOS(username_organizer, $4)>0 OR $4='') AND (theme_id>=$5 OR $5 IS NULL)AND confirmed = true ");
		$start_date = ($start_date=='')?null:$start_date;
		$end_date = ($end_date=='')?null:$end_date;
		$result = pg_execute($db, "db_event_search", array($title,$start_date,$end_date,$organizer,intval($theme)));
		$chaine = "";
		if(pg_num_rows($result) == 0) {
			$chaine .= "
				<tr style='border-style: none;'>
				    <td style='border-style: none; padding-left:0.90cm;'>
				    	Aucune proposition pour le moment
				    </td>
				</tr>" ;
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
		<link rel="stylesheet" type="text/css" href="./proposition_style.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>Recherche Sorties</title>
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
				<div  class="image2" style="background-image: url(img/<?php echo get_image($db, $_SESSION['username'], 0);?>);"></div>
				<div  style="margin-top: 1cm; right: 3cm; ">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
		<section>
			<form method="POST">
				<div class="searchform" style="background-color: white">
					<h2>Recherche Des Sorties :</h2>
					<table>
						<tr>
							<td>
								<div >
								<label >Date Du:</label>
								</div>
					       		<div >
				                    <input type="date" name="start_date" >
				                </div>
							</td>
							<td>
								<div >
								<label >Date à:</label>
								</div>
					       		<div >
				                    <input type="date" name="end_date" >
				                </div>
							</td>
							<td>
								<div >
								<label >Théme:</label>
								</div>
					       		<div >
				                    <select id="theme" name="theme" >
			                    	<option value="" >Choisissez le Théme</option>
			                    	<?php
			                    		$themes = get_themes($db);
			                    		while($theme = pg_fetch_assoc($themes)) {?>
			                    			<option value="<?php echo $theme['theme_id'] ?>">
			                    				<?php echo $theme['theme_title'] ?>
			                    			</option>
		                    			<?php } ?>
			                        </select>
				                </div>
							</td>
							<td>
								<div >
								<label >Organisateur:</label>
								</div>
					       		<div >
					       			<input type="text" name="organizer" />
				                </div>
							</td>
						</tr>
						<tr>
							
							<td>
								<div >
								<label >Titre:</label>
								</div>
					       		<div >
				                    <input type="text" name="title">
				                </div>
							</td>
						</tr>
					</table>
					<button type="submit" name="search_events">Rechecher</button>
				</div>
			</form>

			<table border="2" border-color="lightgrey" style="margin-left: 11cm; margin-top: 2cm ; border: 1px none white; background-color: white; width: 850px; border-radius: 8px; font-size: 11pt;">
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
					<?php
						if(isset($_POST["search_events"])) {
							extract($_POST);
							$result = search_events($db, $_POST["title"], $_POST["start_date"],$_POST["end_date"], $_POST["organizer"] ,$_POST['theme']);
							echo $result;
					    }
					?>
				</tbody>
			</table>
		</section>
	</body>
</html>
