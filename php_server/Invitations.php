<?php 
  		include_once('includes.php');


	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
		header('Location: connexion_form.php');
		exit;
	}
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueil_admin.php');
		exit;
	}
		function get_invitations($db,$username){

		pg_prepare($db, "db_invitation_get", "SELECT username_sender FROM Invitation WHERE username_receiver=$1  AND Acceptance_Time IS NULL AND Acceptance_Date IS NULL");
		$result = pg_execute($db, "db_invitation_get", array($username));
		if(pg_num_rows($result)==0){

			return '<p style="background-color:white; width:500px;height:50px; font-family:12pt arial sans-serif; float:right; margin-right:6cm; margin-top:-9.25cm;padding-top:1cm; border-radius:8px; padding-left:3cm;">Vous n\'avez aucune invitation pour le moment</p>';
		}else{
		echo'	<table style="background-color: white; border:1px solid lightgrey;border-radius: 8px; margin-left:16cm; width: 400px; margin-top: -9.30cm;">';
				while ($row = pg_fetch_row($result)) {
					echo '<tr>';
				$count = count($row);
				$y = 0;
				while ($y < $count) {
					$c_row = current($row);
					echo '<td  style="padding-top:0.5cm; padding-left:1cm; padding-bottom:0.5cm;">' . $c_row . '<td>';

					next($row);
					$y = $y + 1;
				}
					echo '</tr>';
			}
			echo "</table>";
}
}
 function invitations_statement($db,$username){




 }




 ?>
 <!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<link rel="stylesheet" type="text/css" href="./style_I.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title><?php echo $_SESSION['username']?></title>
	</head>
	<body>
		<header class="header1">
				


				<ul style="margin-top: 0;">
					
					<li><a href="accueil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;accueil</a></li>
					<li><a href="user_profile.php" title="Profile"><i class="fas fa-user f007" aria-hidden="true"></i>&nbsp;<?php echo $_SESSION['username'] ?></a></li>
					<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
					<li><a href="user_propose.php"><i class="fas fa-edit f044" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
					<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5" aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
				</ul>
		</header>
		<div class="divadmin">
				<div class="image1"></div>
				<div  class="image2" style="background-image: url(./img/<?php  echo get_image($db, $_SESSION['username'],0)?>);"></div>
				<div  style="margin-top: 1cm; right: 3cm; ">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
		<div>
			<?php echo get_invitations($db,$_SESSION['username']);?>
		</div>
		<div>
			<?php echo show_freinds($db,$_SESSION['username']);  ?>
		</div>

	</body>
	</html>