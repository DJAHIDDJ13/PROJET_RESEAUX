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
		post_message($db, $_POST['text_message'], get_discussion($db, $_GET['event_id']));
		header('Location: event_page.php?event_id='.$_GET['event_id']);
		exit;
	}
	function post_message($db, $message_content, $disc_id) {
		$message_content = htmlspecialchars($message_content);
		pg_prepare($db, "db_post_message", "INSERT INTO Message(Message_Content,Sending_Date,Sending_Time,Username_Transmitter,Discussion_ID) VALUES ($1, $2, $3, $4, $5)");
		$result = pg_execute($db, "db_post_message", array($message_content, date("Y-m-d"), date("H:i"), $_SESSION["username"], $disc_id));
		pg_free_result($result);
	}
	function get_theme($db,$theme_ID){
		$result = pg_query($db,"SELECT theme_title FROM theme WHERE theme_ID = ".$theme_ID);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		return $result_data['theme_title'];
	}
	function get_seen($db, $message_id, $disc_id) {
		$result = pg_query($db, "SELECT * FROM (SELECT Username_Receiver, max(message_id) AS message_id FROM (Receive NATURAL JOIN Message) WHERE Discussion_ID=".$disc_id." GROUP BY Username_receiver) AS _ WHERE message_ID=".$message_id);
		$result_data = 'vu par: ';
		while($res = pg_fetch_assoc($result)) 
			$result_data .= $res['username_receiver']." ";
		pg_free_result($result);
		return ($result_data == 'vu par: ')? '': $result_data;
	}
	function mark_as_read($db, $message_id) {
		$result = pg_query($db , "SELECT * FROM Receive WHERE message_id=".$message_id." AND username_receiver='".$_SESSION['username']."'");
		$result_data = pg_fetch_assoc($result);
		if(!$result_data) 
			pg_free_result(pg_query($db, "INSERT INTO Receive(message_id,username_receiver,seen_time,seen_date) VALUES (".$message_id.", '".$_SESSION['username']."', '".date("H:i")."', '".date("Y-m-d")."')"));
		
	}
	function get_message_div($db, $message_id, $disc_id) {
		$result = pg_query($db,"SELECT * FROM Message WHERE Message_id = ".$message_id);
		$result_data = pg_fetch_assoc($result);
		pg_free_result($result);
		$to_ret = '';
		mark_as_read($db, $message_id);
		if($result_data['username_transmitter'] == $_SESSION['username']) {
			$to_ret .= '<div class="my_message">
							<b class="right">'.$result_data['username_transmitter'].'</b>
							<p class="right">'.$result_data['message_content'].'</p>
							<span class="time-right">'.$result_data['sending_time'].' '.$result_data['sending_date'].'<br>'.get_seen($db, $message_id, $disc_id).'</span>
						</div>';
		} else {
			$to_ret .= '<div class="other_message">
							<b class="left">'.$result_data['username_transmitter'].'</b>
							<p class="left">'.$result_data['message_content'].'</p>
							<span class="time-left">'.$result_data['sending_time'].' '.$result_data['sending_date'].'<br>'.get_seen($db, $message_id, $disc_id).'</span>
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
			$to_ret .= get_message_div($db, $result_data['message_id'], $disc_id);
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
					<li><a href="accueil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;Accueil</a></li>
					<li><a href="user_profile.php" title="Profile"><i class="fas fa-user f007" aria-hidden="true"></i>&nbsp;<?php echo $_SESSION['username'] ?></a></li>
					<li><a href="Invitations.php" title="Invitations"><i class="fas fa-user-friends f500" aria-hidden="true"></i></a></li>
					<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
					<li><a href="user_propose.php"><i class="fas fa-edit f044" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
					<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
				</ul>
		</header>
		<div class="divadmin">
				<div class="image1"></div>
				<div  class="image2" style="background-image: url(img/<?php echo get_image($db, $_SESSION['username'], 0);?>);"></div>
				<div style="margin-top: 1cm; right: 3cm;">
				<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
			</div>
		</div>
	</body>
	<div id="event_info" style="padding: 30px 30px 30px 30px; float:top; margin-left: 11cm; margin-top: -9.5cm; margin-right: 5%; background-color: white; border: 1px none white; border-radius: 8px; ">
		<h1><?php echo $event_info['event_title']." | ".$event_info['event_city'];?></h1>
		<h3 align="right">Date: <?php echo $event_info['event_date'];?></h3>
		<h3 align="right">Temps: <?php echo $event_info['event_time'];?></h3>
		<p style="padding-left: 50px;"><?php echo $event_info['description'];?></p>
		<br>
		<p>Theme: <?php echo get_theme($db, $event_info['theme_id']);?></p>
		<p>Adresse: <?php echo $event_info['event_address'];?></p>
		<form method="post" action="" name="joindre">
			<?php echo get_join_button($db, $_GET['event_id']);?>
		</form>
	</div>
	<?php if(get_participation_status($db, $_GET["event_id"]) == 0 || get_participation_status($db, $_GET["event_id"]) == -2) {
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
