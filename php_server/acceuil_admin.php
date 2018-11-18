<?php
	include_once('includes.php');
	session_start();
	$now = date("d-m-Y");


	if(!isset($_SESSION['username'])){
	header('Location: connexion_form.php');
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
	<header>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<title>Acceuil_Admin</title>
	</header>
	<body>
		Hello  
			<a href="acceuil_admin.php">Acceuil</a>
			<a href="sorties_valider.php">Sorties à valider</a>
			<a href="user_control.php">Utilisateurs</a>
			<a href="deconnexion.php">Se Déconnecter</a>
		<p>Bonjour </br> 

		    <label>Username :<?php echo $_SESSION['username'] ?></label></br></br>
		    

	    </p>
	    
		<form method="post" action="">
	    <h1> Récaputilatif : </h1>
	    	Du 
	    	<input type="date" name="date_1">
	    	A
	    	<input type="date" name="date_2">
	    <input type="submit" value="Recherche" name="rech"> 
		<h3>Personnes Inscrits : <?php echo $nombres_personnes?></h3>
		<h3>Sorties Proposées  : <?php echo $nombres_sortie_proposées?></h3>
		<h3>Sorties Passées    : <?php echo $nombres_sortie_passées?></h3>
		</form>


		<h3>Nombre Utilisateurs pour chaque ville</h3>
		<div id="piechart" style="width: 600px; height: 400px;"></div>
	</body>

	<footer>
	</footer>
	
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
