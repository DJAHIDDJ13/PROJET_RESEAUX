<?php
	include_once('includes.php');
	include_once('event_joining_utility.php');

	if(!filter_var($_GET['event_id'], FILTER_VALIDATE_INT)) {
		echo "Error: Invalid Event_ID";
		exit;
	}
	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
		header('Location: connexion_form.php');
		exit;
	}
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueil_admin.php');
		exit;
	}
	$result = pg_query("SELECT * from Events WHERE Event_ID=".$_GET['event_id']);
	$event_info = pg_fetch_assoc($result);
	if(!$event_info || !$event_info['confirmation_date']){
		echo "Error: Event_ID doesn't exist";
		exit;
	}
	if(isset($_POST['joindre'])) {
		join_event($db, $_GET['event_id']);
		header('Location: event_page.php?event_id='.$_GET['event_id']);
		exit;
	}
	if(isset($_POST['submit_message'])) {
		post_message($db, $_POST['message_text']);
		header('Location: event_page.php?event_id='.$_GET['event_id']);
		exit;
	}
	function post_message($db, $message_content) {
		
	}
	function get_theme($db,$theme_ID){
		$result = pg_query($db,"SELECT theme_title FROM theme WHERE theme_ID = ".$theme_ID);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		return $result_data['theme_title'];
	}
	function get_message_div($db, $message_id) {
		$result = pg_query($db,"SELECT * FROM Message WHERE Message_id = ".$message_id);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		$to_ret = '';
		if($result_data['username_transmitter'] == $_SESSION['username']) {
			$to_ret .= '<div class="my_message">
							<b class="right">'.$result_data['username_transmitter'].'</b>
							<p class="right">'.$result_data['message_content'].'</p>
							<span class="time-right">'.$result_data['sending_time'].' '.$result_data['sending_date'].'</p>
						</div>';
		} else {
			$to_ret .= '<div class="other_message">
							<b class="left">'.$result_data['username_transmitter'].'</b>
							<p class="right">'.$result_data['message_content'].'</p>
							<span class="time-left">'.$result_data['sending_time'].' '.$result_data['sending_date'].'</p>
						</div>';	
		}
		return $to_ret;
	}
	function get_discussion($db, $event_id) {
		$result = pg_query($db,"SELECT discussion_id FROM Events WHERE event_id = ".$event_id);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		return $result_data['discussion_id'];
	}
	
	function get_messages($db, $disc_id) {
		$result = pg_query($db,"SELECT * FROM Message WHERE Discussion_ID=".$disc_id);
		$to_ret = '';
		while($result_data = pg_fetch_assoc($result)) {
			$to_ret .= get_message_div($db, $result_data['message_id']);
		}
		return $to_ret;
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<link rel="stylesheet" type="text/css" href="./event_page.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>Sortie <?php echo $_GET['event_id'];?></title>
	</head>
	<body>
		<header class="header1">
				<ul style="margin-top: 0;">
					<li><a href="accueil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;accueil</a></li>
					<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
					<li><a href="user_propose.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
					<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
				</ul>
		</header>
		<div class="divadmin">
				<div class="image1"></div>
				<div class="image2" src="img/<?echo ($event_info['event_picture'])?"users/".$event_info['event_picture']:"default.jpeg";?>"></div>
				<div style="margin-top: 1cm; right: 3cm;">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
	</body>
	<div id="event_info" style="padding: 30px 30px 30px 30px; float:top; margin-left: 11cm; margin-top: -9.5cm; margin-right: 5%; background-color: white; border: 1px none white; border-radius: 8px; ">
		<h1><?echo $event_info['event_title']." | ".$event_info['event_city'];?></h1>
		<h3 align="right">Date: <?echo $event_info['event_date'];?></h3>
		<h3 align="right">Temps: <?echo $event_info['event_time'];?></h3>
		<p style="padding-left: 50px;"><?echo $event_info['description'];?></p>
		<br>
		<p>Theme: <?echo get_theme($db, $event_info['theme_id']);?></p>
		<p>Adresse: <?echo $event_info['event_address'];?></p>
		<form method="post" action="" name="joindre">
			<?echo get_join_button($db, $_GET['event_id']);?>
		</form>
	</div>
	<? if(get_participation_status($db, $_GET["event_id"]) == 0 || get_participation_status($db, $_GET["event_id"]) == -2) {
			echo 
			'<div class="chat_container">
				<div class="chat_header">
					<h3>Messages de sortie</h3>
				</div>
				<div class="chat_messages">
					'.get_messages($db, get_discussion($db, $_GET['event_id'])).'
				</div>
				<div class="chat_bottom">
					<form action="" method="post">
						<input type="text" placeholder="Ecrivez votre message" name="text_message"></input>
						<input type="submit" value="Envoyer" name="submit_message"></input>
					</form>
				</div>
			</div>';
		}
	?>
</html>
