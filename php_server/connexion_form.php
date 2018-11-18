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

	pg_prepare($db, "db_account", "SELECT * from Account WHERE (Username=$1) AND (Is_Admin=true)");
	
	$result = pg_execute($db, "db_account", array($username));
	$result_data = pg_fetch_assoc($result);
	pg_free_result($result);
	if(password_verify($user_password, $result_data['user_password'])) {
		$_SESSION['username'] = $result_data['username'];
		header('Location: acceuil_admin.php');
		exit;
	} else {
		$_SESSION['flash'] = "Votre mail ou mot de passe ne correspondent pas";
	}
}
?>

<html>
	<header>
		<title>Connexion</title>
	</header>
	<body>
		<center>
			<?php 
		    if(isset($_SESSION['flash'])){ 
		    ?>
				<div id="alert" ><a class="close"></span></a>
					<?= $_SESSION['flash']; ?>
				</div>	
		    
			<?php
			    unset($_SESSION['flash']);
			}
			?>
			<h1>Connexion</h1>
			<form  method="post" action="" >
				<label>Login</label>
	       		<input  type="text" name="username" placeholder="Login" value="<?php if (isset($username)) echo $username; ?>" required="required"/></br></br>
				<label>Password</label>
				<input  type="password" name="user_password" placeholder="Mot de passe" value="<?php if (isset($user_password)) echo $user_password; ?>" required="required"/></br></br>

				<button type="submit" > Se connecter </button>
			</form>
		</center>
	</body>
	<footer>
	</footer>
</html>
