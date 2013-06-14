<?php 

$db;
try {
	$dbname = "dbnf17p095";
	$host = "tuxa.sme.utc";
	$user = "nf17p095";
	$mdp = "#########";
	
	$db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $mdp );
}
catch(PDOException $e)
{
	echo $e->getMessage();
}
	
	
?>