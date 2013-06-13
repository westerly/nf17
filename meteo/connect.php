<?php 

$db;
try {
		$dbname = "nf17";
		$host = "127.0.0.1";
		$user = "postgres";
		$mdp = "VUMcid38";
		
		$db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $mdp );
		// echo "PDO connection object created";
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
	
?>