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

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>Profile de <?echo $_GET['username']?>></title>
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
				<div  class="image2"></div>
				<div  style="margin-top: 1cm; right: 3cm; ">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
	</body>
</html>
