<?php 

try {
		$dbname = "nf17";
		$host = "127.0.0.1";
		$user = "postgres";
		$mdp = "VUMcid38";
		
		$db = new PDO("pgsql:dbname=$dbname;host=$host", "$user", "$mdp" );
		//echo "PDO connection object created";
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
	
?>