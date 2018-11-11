<?php
include_once 'includes.php';
session_start();

if(isset($_SESSION['pseudo'])) {
	header('Location: accueil.php');
	exit;
}

if(!empty($_POST)) {
	extract($_POST);
	$username = trim($username);
	$user_password = trim($user_password);

	pg_prepare($db, "db_account", "SELECT * from Account WHERE Username = $1");
	pg_prepare($db, "db_admin", "SELECT * FROM Administrator WHERE Admin_Username = $1");
	
	$result = pg_execute($db, "db_account", array($username));
	$result_data = pg_fetch_assoc($result);
	pg_free_result($result);

	$result_admin =  pg_execute($db, "db_admin", array($result_data['username']));
	$result_admin_data = pg_fetch_assoc($result_admin);
	pg_free_result($result_admin);
	
	if($result_admin_data['admin_username'] && password_verify($user_password, $result_data['user_password'])) {
		$_SESSION['username'] = $result_data['username'];
		$_SESSION['user_password'] = $result_data['user_password'];
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
