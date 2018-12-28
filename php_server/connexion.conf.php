<?php
	function connexion_DB(){
		$hostname='localhost';
		// nom de la base des données
		$dbname='projdbsortie';
		// username pgAdmin en local 
		$user='postgres';
		//password pgAdmin 
		$password='201320';
		$dbconn = pg_connect("host=".$hostname." dbname=".$dbname." user=".$user." password=".$password."");
		if(!$dbconn) {
			echo("Echec connexion à la base de données");
		}
		return $dbconn;
	}
