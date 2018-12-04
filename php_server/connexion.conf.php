<?php
	function connexion_DB(){
		$hostname='localhost';
		// non de la base de donnée 
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
