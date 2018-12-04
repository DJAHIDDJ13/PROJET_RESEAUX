<?php

	include_once('includes.php');
	session_start();

	if(!isset($_SESSION['username'])){
	header('Location: connexion_form.php');
	exit;
}

	function search_users($db, $username, $start_signup_date, $end_signup_date) {
		pg_prepare($db, "db_user_search", "SELECT Username, Last_name, First_name, Place_of_Birth, Signup_Date FROM Users Where (STRPOS(Username, $1)>0 OR $1='') AND (Signup_Date>=$2 OR $2 IS NULL) AND (Signup_Date<=$3 OR $3 IS NULL)");
		$start_signup_date = ($start_signup_date=='')?null:$start_signup_date;
		$end_signup_date = ($end_signup_date=='')?null:$end_signup_date;
		$result = pg_execute($db, "db_user_search", array($username, $start_signup_date, $end_signup_date));
		return $result;
	}
	function ban_user($db, $uname) {	
		pg_prepare($db, "db_user_get", "SELECT * FROM Users WHERE Username=$1 AND (Is_Admin=0)");
		pg_prepare($db, "db_user_delete", "UPDATE Users SET Modification_Date=$1, Deletion_Date=$1 WHERE Username=$2");
		pg_prepare($db, "db_user_send_ban_notif", "INSERT INTO Notification(Notification_Content, Notification_Date, Notification_Time, Seen , Username_Receiver) VALUES ('Vous avez été banni de façon permanente', $1, $2, false, $3)");
		$result = pg_execute($db, "db_user_get", array($uname));
		if(pg_num_rows($result)) {
			$row = pg_fetch_row($result);
			if($row[14] != null) {
				echo "<p>account already suspended</p>";
			} else {
				pg_execute($db, "db_user_delete", array(date('j-m-Y'), $uname));
				echo "<p>suspended account '".$uname."'</p>";
				pg_execute($db, "db_user_send_ban_notif", array(date("d-m-Y"), date("H:i:s") , $uname));
				echo "<p>sent notification</p>";
			}
		} else {
			echo "<p>user '".$uname."' not in database</p>";
		}
	}
	
	function show_users($result) {
		if(pg_num_rows($result) == 0) {
			echo '<p style="height:1cm; padding: 0.5cm;">Aucune resultat trouvé</p>';
		} else {
			$i=0;
			echo '<table style="width:850px;">';
			echo '<tr class="user_table_row" style="border-bottom : 1px solid gray;" >';
			echo '<td class="user_table_head" style="padding-left: 0.8cm; border-bottom : 1px solid gray;">Nom d\'utilisateur</td>';
			echo '<td class="user_table_head" style="padding-left: 1.55cm; border-bottom : 1px solid gray;">Nom</td>';
			echo '<td class="user_table_head" style="padding-left: 1.25cm; border-bottom : 1px solid gray;">Prénom</td>';
			echo '<td class="user_table_head" style="padding-left: 1.80cm; border-bottom : 1px solid gray;">Ville</td>';
			echo '<td class="user_table_head" style="padding-left: 1.25cm; border-bottom : 1px solid gray;">Date d\'inscription</td>';
			echo '<td class="user_table_head" style="padding-left: 1cm; border-bottom : 1px solid gray;">Supprimer</td>';
			echo '</tr>';
			
			while ($row = pg_fetch_row($result)) {
				echo '<tr class="user_table_row">';
				$count = count($row);
				$y = 0;
				$uname = current($row);
				while ($y < $count) {
					$c_row = current($row);
					echo '<td class="user_table_cell">' . $c_row . '</td>';

					next($row);
					$y = $y + 1;
				}
				$delete_date = current($row);
				echo '<td class="user_table_cell" style="padding-left:1.25cm;">';
					echo '<form action=" " method="post" onsubmit="return confirm(\'Etes vous sur de bannir cet utilisateur?\', false)" style="background-color:red; width:48px;">';
						/*echo '<input  name="ban_uname" value="'.$uname.'">';*/
						echo '<button name="ban_uname" style="font-size:14pt; color:white; width:48px; background-color:red; border-radius:8px; border-style:none;"><i class="fas fa-user-times" style="padding-bottom:0.25cm; padding-top:0.25cm;"></i></button>';
					echo '</form>';
				echo '</td>';
				echo '</tr>';
				$i = $i + 1;
			}
			echo '</table>';
		}
		pg_free_result($result);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<title>User control center</title>
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
			<li><a href="acceuil_admin.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;Acceuil</a></li>
			<li><a href="sorties_valider.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Sorties à valider</a></li>
			<li><a href="user_control.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Utilisateurs</a></li>
			<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5" aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
		</ul>
</header>
<div class="divadmin">
		<div class="image1"></div>
		<div  class="image2"></div>
		<label>Bonjour <?php echo $_SESSION['username'] ?></label>
</div>
		<form action="" method="post">
			<div class="datediv" style="background-color: white; margin-top: 0cm;margin-top: -9.25cm; margin-left: 11cm; width: 850px; border-radius: 8px; height: 1cm;">
				<label style="margin-left: 0.95cm;">Username </label> <input type="text" name="uname">
			<label>Start signup date </label> <input type="date" name="StartSingupDate">
			<lable>End signup date </label> <input type="date" name="EndSignupDate"></lable>
			<input type="submit" value="Search" name="search">
		</div>
		</form>	
	</div>
		<div style="margin-left: 11cm; width: 850px; background-color: white; margin-top: 0.75cm; border-radius:8px; ">
			<?php
				if(isset($_POST["search"])) {
					$result = search_users($db, $_POST["uname"], $_POST["StartSingupDate"], $_POST["EndSignupDate"]);
					show_users($result);
				} else if(isset($_POST["ban_uname"])) {
					ban_user($_POST["ban_uname"]);
				}
			?>
		</div>
	</body>

</html>

