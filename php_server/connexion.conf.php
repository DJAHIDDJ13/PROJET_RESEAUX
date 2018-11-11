<?php
	function connexion_DB(){
		$hostname='localhost';
		// non de la base de donnée 
		$dbname='dbprojsortie';
		// username pgAdmin en local 
		$user='postgres';
		//password pgAdmin 
		$password='123456';
		$dbconn = pg_connect("host=".$hostname." dbname=".$dbname." user=".$user." password=".$password."");
		if(!$dbconn) {
			echo("Echec connexion à la base de données");
		}
		return $dbconn;
	}
