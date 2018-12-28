<?php

	include_once('includes.php');

	if(!isset($_SESSION['username'])){
		header('Location: connexion_form.php');
		exit;
	}

	if(isset($_POST['rejeter'])){
		rejeter_user($db,$_POST['username']);
	}

	if(isset($_POST['confirmer'])){
		confirmer_user($db,$_POST['username']);
	}

	function search_no_confirmed_users($db) {
		pg_prepare($db, "db_account_no_confirmed", "SELECT * FROM users Where confirmed IS NULL AND 
			confirmation_date is NULL");
		$result = pg_execute($db, "db_account_no_confirmed", array());
		return $result;
	}
	
	
	function show_users($result) {
		if(pg_num_rows($result) == 0) {
			echo '<p style="height:1cm; padding: 0.5cm;">Pas de nouvelles Inscriptions</p>';
		} else {
			echo '<table style="width:850px;">';
			echo '<tr class="user_table_row" style="border-bottom : 1px solid gray;" >';
			echo '<td class="user_table_head" style="padding-left: 0.8cm; border-bottom : 1px solid gray;">Nom d\'utilisateur</td>';
			echo '<td class="user_table_head" style="padding-left: 1.55cm; border-bottom : 1px solid gray;">Nom</td>';
			echo '<td class="user_table_head" style="padding-left: 1.25cm; border-bottom : 1px solid gray;">Prénom</td>';
			echo '<td class="user_table_head" style="padding-left: 1.80cm; border-bottom : 1px solid gray;">Ville</td>';
			echo '<td class="user_table_head" style="padding-left: 1.25cm; border-bottom : 1px solid gray;">Date d\'inscription</td>';
			echo '<td class="user_table_head" style="padding-left: 1cm; border-bottom : 1px solid gray;">Confirmer</td>';
			echo '<td class="user_table_head" style="padding-left: 1cm; border-bottom : 1px solid gray;">Rejeter</td>';
			echo '</tr>';
		}
		while ($row = pg_fetch_assoc($result)) {
				echo '<tr class="user_table_row">';
				echo '<td class="user_table_cell">'.$row['username'].'</td>';
				echo '<td class="user_table_cell">'. $row['first_name'].'</td>';
				echo '<td class="user_table_cell">'. $row['last_name'].'</td>';
				echo '<td class="user_table_cell">'. $row['place_of_birth'].'</td>';
				echo '<td class="user_table_cell">'. $row['signup_date'].'</td>';
				echo '<td class="user_table_cell" style="padding-left:1.25cm;">';
					echo '<form action=" " method="post"  style="background-color:green; width:48px;">';
						echo '<input  name="username" value="'.$row['username'].'" type="hidden">';
						echo '<button name="confirmer" style="font-size:14pt; color:white; width:48px; background-color:green; border-radius:8px; border-style:none;"><i class="fas fa-user-times" style="padding-bottom:0.25cm; padding-top:0.25cm;"></i></button>';
					echo '</form>';
				echo '</td>';
				echo '<td class="user_table_cell" style="padding-left:1.25cm;">';
					echo '<form action=" " method="post"  style="background-color:red; width:48px;">';
						echo '<input  name="username" value="'.$row['username'].'" type="hidden">';
						echo '<button name="rejeter" style="font-size:14pt; color:white; width:48px; background-color:red; border-radius:8px; border-style:none;"><i class="fas fa-user-times" style="padding-bottom:0.25cm; padding-top:0.25cm;"></i></button>';
					echo '</form>';
				echo '</td>';
				echo '</tr>';
		}
		echo '</table>';
		pg_free_result($result);
	}

	// fonction qui sert a confirmer les comptes 
	function confirmer_user($db,$username){
		$now = date("Y-m-d");
		pg_prepare($db, "db_confirm_account", "UPDATE users SET confirmed =$1 , confirmation_date =$2 ,
			modification_date =$3 WHERE username = $4"); 
	    pg_execute($db, "db_confirm_account", array(true,$now,$now,$username));
	    $_SESSION['flash'] = "Vous venez de confirmer le compte de " .$username;
	}
	// 
	function rejeter_user($db,$username){
		$now = date("Y-m-d");
		pg_prepare($db, "db_reject_account", "UPDATE users SET confirmed =$1 , confirmation_date =$2 ,
			modification_date =$3 WHERE username = $4"); 
	    pg_execute($db, "db_reject_account", array('false',$now,$now,$username));
	    $_SESSION['flash'] = "Vous venez de rejeter le compte de " .$username;
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<title>Confirmation comptes</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<style>
			.user_table{ 
						 background-color: white;
			}
			.user_table_row{
						 font-size: 10pt;	 
						 }
			.user_table_head { 
						
						 font-size: 12pt;
						 padding-top: 0.25cm ;
						 padding-bottom:0.25cm;
						 
						
						 }
			.user_table_cell{
				padding-left: 1.75cm;
				padding-top: 0.25cm ;
				padding-bottom:0.25cm;
						 
						 
			}
		</style>
	</head>
	<body>
<header class="header1">
		<ul style="margin-top: 0;">
			<li><a href="accueil_admin.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;accueil</a></li>
			<li><a href="sorties_valider.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Sorties à valider</a></li>
			<li><a href="user_control.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Utilisateurs</a></li>
			<li><a href="confirm_user.php"><i class="fas fa-user-check f0c0" aria-hidden="true"></i>&nbsp;Inscriptions</a></li>
			<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5" aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
		</ul>
</header>
<div class="divadmin">
		<div class="image1"></div>
		<div  class="image2" style="background-image: url(img/<?php echo get_image($db, $_SESSION['username'], 0);?>);"></div>
		<label>Bonjour <?php echo $_SESSION['username'] ?></label>
</div>
		
<section>	

		<div style="margin-left: 11cm; width: 850px; background-color: white; margin-top: 0.75cm; border-radius:8px; ">
			<?php 
			    if(isset($_SESSION['flash'])){ 
			    ?>
			    	<center>
					<div id="alert" ><a class="close"></a>
						<?= $_SESSION['flash']; ?>

					</div>
					</center>
			<?php
				    unset($_SESSION['flash']);
			}
			?>
		</div>
		<div style="margin-left: 11cm; width: 850px; background-color: white; margin-top: 0.75cm; border-radius:8px; ">
			<?php

					$result = search_no_confirmed_users($db);
					show_users($result);
			?>
		</div>
	</body>
</section>
</html>

