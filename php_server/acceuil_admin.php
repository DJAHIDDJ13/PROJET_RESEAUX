<?php
	
	include_once('includes.php');
	session_start();

?>

<!DOCTYPE html>
<html>
	<header>
		<title>Acceuil Admin</title>
	</header>
	<body>
		Hello  
		<p>Bonjour </br> 

		    <label>Username :<?php echo $_SESSION['username'] ?></label></br></br>
		    <label>Password :<?php echo $_SESSION['user_password']?></label></br></br>

	    </p>
	</body>

</html>
