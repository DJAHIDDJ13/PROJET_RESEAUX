<?php
   
   	include_once('includes.php');
   	$now = date("Y-m-d");
   	$time = date("H:i:s");


   	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
		header('Location: connexion_form.php');
		exit;
	}
	if(isset($_SESSION['is_admin'])) {
		header('Location: accueil_admin.php');
		exit;
	}


    function get_themes($db){
		$result = pg_query($db,"SELECT * FROM theme");
		return $result ;
	}

	function get_guests($db){
		$result = pg_query($db,"SELECT * FROM guest");
		return $result ;
	}

	function check_date($db,$username,$date){
		pg_prepare($db, "db_check_date", "SELECT * FROM events WHERE username_organizer=$1 AND event_date=$2 AND deletion_date is null");
	    $res = pg_execute($db, "db_check_date", array($username, $date));
	    if(pg_num_rows($res) == 0) {
	    	return true;
	    }
	    return false;
	}

	if(isset($_POST['propose'])){

		extract($_POST);
		$valid = true ;
		$date = date('Y-m-d', strtotime($date));
		$deadline = date('Y-m-d', strtotime($deadline));

		if($date <= $now) {
			$valid = false ;
			$error_date_e =" Veuillez choisir une date ultérieur";
		} else {
			if(!( $now<=$deadline && $deadline <= $date  )){
			$valid = false ;
			$error_deadline =" Le dernier délai de participation doit etre compris entre  la date de la proposition et la date de l'evenement ";
			}
		}

		//~ if( check_date($db, $_SESSION['username'] ,$date) ==  false){
			//~ $valid = false ;
			//~ $error_date_e =" Vous avez déja proposer un évenement a cette date ";
		//~ }

		
		if($theme == 0){
			$valid = false ;
			$error_theme =" Vous devez choisir le theme de votre Sortie";
		}

		if($guest == 0){
			$valid = false ;
			$error_guest =" Vous devez choisir à qui offrir la sortie ";
		}

		if($_FILES['picture']['name']){

			$image = $_FILES['picture']['name'];
			$target = "img/users/".basename($image);
			if(!move_uploaded_file($_FILES['picture']['tmp_name'], $target)){
				$valid = false;
			}
		}else{
			$image = null;
		}

		if($valid){

			pg_prepare($db,"db_insert_discussion", "INSERT INTO discussion (discussion_date)
			VALUES ($1) RETURNING discussion_id");
			$inser_d = pg_execute($db, "db_insert_discussion", array($now));
			$id = pg_fetch_assoc($inser_d);
			$id_d = $id['discussion_id'];
			pg_prepare($db,"db_insert_event", "INSERT INTO events (event_time,event_date, event_address,event_city,event_title,description,Capacity,event_picture,deadline_date,proposition_date,modification_date,theme_id,guest_id,username_organizer,discussion_id)
			VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15)");
			$inser_event = pg_execute($db, "db_insert_event", array($time,$date,$adresse,$city,$title,$description,$capacity,$image,$deadline,$now,$now,$theme,$guest,$_SESSION['username'],$id_d));
			
			$_SESSION['flash'] ="Votre Proposition a été enregistré avec succée";

			

		}


	}



?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<link rel="stylesheet" type="text/css" href="./style_I.css">
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>Proposer Sortie</title>
	</head>
	<body>
<header class="header1">
		<ul style="margin-top: 0;">
			<li><a href="accueil_utilisateur.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;Accueil</a></li>
			<li><a href="event_search.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Chercher une sortie</a></li>
			<li><a href="user_propose.php"><i class="fas fa-edit f0c0" aria-hidden="true"></i>&nbsp;Proposer une sortie</a></li>
			<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 " aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
		</ul>
</header>
<div class="divadmin">
		<div class="image1"></div>
		<div  class="image2"></div>
		<div  style="margin-top: 1cm; right: 3cm; ">
			<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
		</div>
</div>
<section>

	<div class="propoform" style="background-color: white">
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
	<form  method="post" class="propoform" enctype="multipart/form-data" >
		    <h2>Proposer Sortie</h2> 
		        <table>
				    <tr>
						<td>
							<div >
							<label >Titre :</label>
							</div>
				       		<div >
			                    <input  type="text" name="title" placeholder="Donnez un titre à votre sortie " value="<?php if (isset($title)) echo $title; ?>" required="required"/>
			                    <div><small ></small></div>
			                </div>
						</td>
						<td>
							<div >
								<label >Théme :</label>
							</div>
				       		<div >
			                    <select id="theme" name="theme" >
			                    	<option value="0" >Choisissez le Théme</option>
			                    	<?php
			                    		$themes = get_themes($db);
			                    		while($theme = pg_fetch_assoc($themes)) {?>
			                    			<option value="<?php echo $theme['theme_id'] ?>">
			                    				<?php echo $theme['theme_title'] ?>
			                    			</option>
		                    			<?php } ?>
			                    </select>
			                    <div><small ><?php if (isset($error_theme)) echo $error_theme; ?></small></div>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Déscription :</label>
							</div>
				       		<div >
			                    <textarea  name="description" placeholder="Décriver votre proposition de sorties en quelque lignes "  required="required"> 
			                    </textarea>
			                    <div><small ></small></div>
			                </div>
						</td>
						<td>
							<div >
								<label >Invités :</label>
							</div>
				       		<div >
			                    <select id="guest" name="guest" >
			                    	<option  value="0" >Choisir à qui ouvrir la sortie</option>
			                    	<?php
			                    		$guests = get_guests($db);
			                    		while($guest = pg_fetch_assoc($guests)) {?>
			                    			<option value="<?php echo $guest['guest_id'] ?>">
			                    				<?php echo $guest['guest_title'] ?>
			                    			</option>
		                    			<?php } ?>
			                    </select>
			                    <div><small ><?php if (isset($error_guest)) echo $error_guest; ?></small></div>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Date:</label>
							</div>
				       		<div >
			                    <input  type ="date" name="date"  value="<?php if (isset($date)) echo $date; ?>" required="required"/>
			                    <div><small ><?php if (isset($error_date_e)) echo $error_date_e; ?></small></div>
			                </div>
						</td>
						<td>
							<div >
							<label >Heure:</label>
							</div>
				       		<div >
			                    <input  type ="time" name="time"  value="<?php if (isset($_POST['time'])) echo $time; ?>" required="required"/>
			                    <div><small ></small></div>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Adresse:</label>
							</div>
				       		<div >
			                    <input  type ="text" name="adresse"  value="<?php if (isset($adresse)) echo $adresse; ?>" required="required"/>
			                    <div><small ></small></div>
			                </div>
						</td>
						<td>
							<div >
							<label >Ville:</label>
							</div>
				       		<div >
			                    <input  type ="text" name="city"  value="<?php if (isset($city)) echo $city; ?>" required="required"/>
			                    <div><small ></small></div>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Délai Participation:</label>
							</div>
				       		<div>
			                    <input  type ="date" name="deadline"  value="<?php if (isset($deadline)) echo $deadline; ?>" required="required"/>
			                    <div><small ><?php if (isset($error_deadline)) echo $error_deadline; ?></small></div>
			                </div>
						</td>
						<td>
							<div >
							<label >Nombre Participants:</label>
							</div>
				       		<div >
			                    <input  type ="number" name="capacity"  value="<?php if (isset($capacity)) echo $capacity; ?>" required="required"/>
			                    <div><small ></small></div>
			                </div>
						</td>
					</tr>
					<tr>
						<td>
							<div >
							<label >Illustrer votre Sortie:</label>
							</div>
				       		<div>
			                    <input  type ="file" name="picture"/>
			                    <div><small ></small></div>
			                </div>
						</td>
					</tr>
		        </table>
		<button type="submit" class="" name="propose"> Proposer la sortie </button>  
	</form>
</section>
