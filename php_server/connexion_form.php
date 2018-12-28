<?php
include_once 'includes.php';

if(isset($_SESSION['username']) && isset($_SESSION['password'])) {
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueil_admin.php');
		exit;
	} else {
		header('Location: accueil_utilisateur.php');
		exit;
	}
}

if(!empty($_POST)) {
	extract($_POST);
	$username = $username;
	$user_password = $user_password;
	pg_prepare($db, "db_account", "SELECT * from Account WHERE (Username=$1)");
	pg_prepare($db, "db_ban_confirm", "SELECT * from Users WHERE (Username=$1)");
	
	$result = pg_execute($db, "db_account", array($username));
	$result2 = pg_execute($db, "db_ban_confirm", array($username));
	
	$result_data = pg_fetch_assoc($result);
	$result_data2 = pg_fetch_assoc($result2);
	if(!$result_data2) {
		$result_data2['deletion_date'] = '';
		$result_data2['confirmation_date'] = ($result_data['is_admin']=='t')? 't':'';
	}
	pg_free_result($result);
	pg_free_result($result2);
	
	if(password_verify($user_password, $result_data['user_password']) && !$result_data2['deletion_date'] && $result_data2['confirmation_date']) {
		$_SESSION['username'] = $result_data['username'];
		$_SESSION['password'] = $result_data['user_password'];
		if($result_data['is_admin'] == 't') {
			$_SESSION['is_admin'] = 1;
			header('Location: accueil_admin.php');
			exit;
		} else {
			header('Location: accueil_utilisateur.php');
			exit;
		}
	} else if($result_data2['deletion_date']) {
		$_SESSION['flash'] = "Vous avez été banni, vous ne pouvez pas connecter a votre compte";
	} else if (!$result_data2['confirmation_date']) {
		$_SESSION['flash'] = "En attente de la confirmation de l'administrateur";
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
				<br>
				<a href='inscription_form.php'>Vous n'avez pas un compte?</a>
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
