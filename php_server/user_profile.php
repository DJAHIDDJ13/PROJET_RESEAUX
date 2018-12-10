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


	 function get_users_profile($db, $username){
   		$result =  pg_query($db, "SELECT * FROM users WHERE username = '".$username."'");
   		$result = pg_fetch_assoc($result);
   		$res = '<div>
   					<label> <b style="color:#2dc997;">Nom d\'utilisateur: </b>  '.$result['username'].'</label>
   				</div>
   				<div>
   					<label> <b style="color:#2dc997;">Nom : </b>'.$result['last_name'].'</label>
   					<label style="padding-left:2cm;">Prénom : </b>'.$result['first_name'].'</label>
   				</div>
   				<div>
   					<label> <b style="color:#2dc997;">Email : </b>'.$result['email'].'</label>
   					<label style="padding-left:2cm;"> <b style="color:#2dc997;">Tel : '.$result['phone_number'].'</label>
   				</div>
   				<div>
   					<label> <b style="color:#2dc997;">Date de naissance: </b>'.$result['birthday_user'].'</label>
   					<label style="padding-left:2cm;">Lieux de naissance: </b>'.$result['place_of_birth'].'</label>
   				</div>
   				<div>
   					<label> <b style="color:#2dc997;">Date d\'inscription : </b>'.$result['signup_date'].'</label>
   					<label style="padding-left:2cm;"> <b style="color:#2dc997;">Date de confirmation: </b>'.$result['confirmation_date'].'</label>
   				</div>';
   				
   		return $res;
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
					<li><a href="Invitations.php" title="Invitations"><i class="fas fa-user-friends f500" aria-hidden="true"></i></a></li>
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
		<div class="userProfile" style="padding: 50px; background-color: white; width: 800px;height: 400px; float: right; margin-right: 3cm; border-radius: 8px; border-color: lightgrey; margin-top: -9.30cm;">

			<?php 

				echo get_users_profile($db, $_GET['username']);
			?>
		</div>
		<div>
			<?php echo show_freinds($db,$_SESSION['username']);  ?>
		</div>
	</body>
</html>
