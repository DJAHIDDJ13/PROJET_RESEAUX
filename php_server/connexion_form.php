<?php
include_once 'includes.php';
session_start();

if(isset($_SESSION['pseudo'])) {
	header('Location: accueil.php');
	exit;
}

if(!empty($_POST)) {
	extract($_POST);
	$username = $username;
	$user_password = $user_password;
	pg_prepare($db, "db_account", "SELECT * from Account WHERE (Username=$1) ");
	
	$result = pg_execute($db, "db_account", array($username));
	$result_data = pg_fetch_assoc($result);
	
	pg_free_result($result);
	if(password_verify($user_password, $result_data['user_password'])) {
		$_SESSION['username'] = $result_data['username'];
		$_SESSION['password'] = $result_data['user_password'];
		if($result_data['is_admin'] == 't') {
			$_SESSION['is_admin'] = 1;
			header('Location: acceuil_admin.php');
			exit;
		} else {
			header('Location: acceuil_utilisateur.php');
			exit;
		}
	} else {
		$_SESSION['flash'] = "Votre mail ou mot de passe ne correspondent pas";
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<link rel="Stylesheet" href="./style.css" type="text/css" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<title>Connexion</title>
    </head>
    <body id="connexionAdmin">
	
		<center>
			<form  method="post" action="" class="connexionform" style="background-color: white;" >
				<h2 >Connexion !</h2>
				<div style="margin-top: 1.5cm;">
				<label style="margin-right:11.30cm ">Login</label>
				</div>
	       		<div class="inputdiv">
                    <input  type="text" name="username" placeholder="Login" value="<?php if (isset($username)) echo $username; ?>" required="required"/>
                </div>
                <div style="margin-top: 1cm;">
				<label>Password</label>
                </div>
                <div class="inputdiv">
				<input  type="password" name="user_password" placeholder="Password" value="<?php if (isset($user_password)) echo $user_password; ?>" required="required"/></div>

				<button type="submit" class="bouttonConnect"> Se connecter </button>
				<?php 
				if(isset($_SESSION['flash'])){ 
				?>
					<div id="alert" ><a class="close"></a>
						<?= $_SESSION['flash']; ?>
					</div>	
				
				<?php
					unset($_SESSION['flash']);
				}
				?>
			</form>
		</center>
	</body>
	<footer>
	</footer>
</html>
