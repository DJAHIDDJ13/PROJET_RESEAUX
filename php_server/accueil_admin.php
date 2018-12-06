<?php
	include_once('includes.php');

	$now = date("d-m-Y");

	if(!isset($_SESSION['username']) || !isset($_SESSION['password'])){
		header('Location: connexion_form.php');
		exit;
	}
	if(!isset($_SESSION['is_admin'])) {
		header('Location: accueil_utilisateur.php');
		exit;
	}


	function users_city($db){

		$result = pg_query($db,"SELECT count(*) AS num,place_of_birth FROM users GROUP BY place_of_birth");
		return $result;
    }

	function search_users($db,$start_signup_date, $end_signup_date) {
		pg_prepare($db, "db_user_search", "SELECT count(*) as nombres FROM Users Where  (Signup_Date>=$1 OR $1 IS NULL) AND (Signup_Date<=$2 OR $2 IS NULL)");
		$start_signup_date = ($start_signup_date=='')?null:$start_signup_date;
		$end_signup_date = ($end_signup_date=='')?null:$end_signup_date;
		$result = pg_execute($db, "db_user_search", array( $start_signup_date, $end_signup_date));
		$result = pg_fetch_assoc($result);
		return $result['nombres'];
	}

	function search_sorties_proposées($db,$start_propose_date, $end_propose_date) {
		pg_prepare($db, "db_proposed_events_search", "SELECT count(*) as nombres FROM events Where  (proposition_Date>=$1 OR $1 IS NULL) AND (proposition_Date<=$2 OR $2 IS NULL)");
		$start_propose_date = ($start_propose_date=='')?null:$start_propose_date;
		$end_propose_date = ($end_propose_date=='')?null:$end_propose_date;
		$result = pg_execute($db, "db_proposed_events_search", array( $start_propose_date, $end_propose_date));
		$result = pg_fetch_assoc($result);
		return $result['nombres'];
	}

	function search_sorties_passées($db,$start_passed_date, $end_passed_date) {
		pg_prepare($db, "db_passed_events_search", "SELECT count(*) as nombres FROM events Where  (event_Date>=$1 OR $1 IS NULL) AND (event_Date<=$2 OR $2 IS NULL)");
		$start_passed_date = ($start_passed_date=='')?null:$start_passed_date;
		$end_passed_date = ($end_passed_date=='')?null:$end_passed_date;
		$result = pg_execute($db, "db_passed_events_search", array( $start_passed_date, $end_passed_date));
		$result = pg_fetch_assoc($result);
		return $result['nombres'];
	}

	if(isset($_POST['rech'])){
		$nombres_personnes = search_users($db,$_POST['date_1'],$_POST['date_2']);
		$nombres_sortie_proposées = search_sorties_proposées($db,$_POST['date_1'],$_POST['date_2']);
		$nombres_sortie_passées = search_sorties_passées($db,$_POST['date_1'],$_POST['date_2']);
	}else{
		$nombres_personnes = search_users($db,'','');
		$nombres_sortie_proposées = search_sorties_proposées($db,'','');
		$nombres_sortie_passées = search_sorties_passées($db,'',$now);
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./style1.css">
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<title>accueil_Admin</title>
	</head>
	<body>
<header class="header1">
		<ul style="margin-top: 0;">
			<li><a href="accueil_admin.php"><i class="fa fa-home fa-fw" aria-hidden="true"></i>&nbsp;accueil</a></li>
			<li><a href="sorties_valider.php"><i class="fas fa-search f002" aria-hidden="true"></i>&nbsp;Sorties à valider</a></li>
			<li><a href="user_control.php"><i class="fas fa-users f0c0" aria-hidden="true"></i>&nbsp;Utilisateurs</a></li>
			<li><a href="deconnexion.php"><i class="fas fa-sign-out-alt f2f5 "aria-hidden="true"></i>&nbsp;Se Déconnecter</a></li>
		</ul>
</header>
<div class="divadmin">
		<div class="image1"></div>
		<div  class="image2" style="background-image: url(img/<?php echo get_image($db, $_SESSION['username'], 0);?>);"></div>
		<div  style="margin-top: 1cm; right: 3cm; ">
		<label style="font-size: 14pt; font-family: arial; padding-left: 2.9cm;">Bonjour <?php echo $_SESSION['username'] ?></label>
	</div>
</div>
<section>
	    <h1> Récaputilatif : </h1>
	    <form class="divRech" >
	    	<div class="datediv"> 
	    			<label style="padding-left: 1cm; color: #2dc997;">Du :</label> 
	    			<input  style="padding-left: 1cm; padding-right: 1cm; "  type="date" name="date_1">
	    			<label style="padding-left: 2cm; color: #2dc997;">A :</label>
	    			<input style="padding-left: 1cm; padding-right: 1cm; " type="date" name="date_2">
					<button style=" background-color: white; top: 1cm; font-size:13pt; color: grey;border-style: none;" type="submit"  name="rech"><i class="fas fa-search f002" aria-hidden="true"></i></button>
			</div>
		</form>
		<div class="divRes">
			<p>Personnes Inscrits : <?php echo $nombres_personnes?></p>
		</div>
		<div class="divRes">
			<p>Sorties Proposées  : <?php echo $nombres_sortie_proposées?></p>
		</div>
		<div class="divRes">
			<p>Sorties Passées    : <?php echo $nombres_sortie_passées?></p>
		</div>
		<div id="piechart" style="width: 500px; height: 400px; margin-left: 11cm; margin-top: 1cm;"></div>
</section>
	</body>
	
	
	<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Ville', 'Nombre de personnes'],
            <?php  
            	$result = users_city($db);
	            while($row = pg_fetch_assoc($result))  
	            {  
	            	if($row["place_of_birth"] == null){
	            		$row["place_of_birth"]="Non Spécifiée";
	            	}
	                echo "['".$row["place_of_birth"]."', ".$row["num"]."],";  
	            }  
            ?>
        ]);
        var options = {
          title: 'Nombre de personne pour chaque ville'
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
    </script>
	
</html>
