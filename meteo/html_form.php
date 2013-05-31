<?php 


$enum_moments = array('Matin', 'Après-midi', 'Soirée', 'Nuit');
$enum_directions = array('Nord', 'Sud', 'Ouest', 'Est');
$enum_types = array('Pluie', 'Neige', 'Grêle', 'Sable');

// Le troisième param permet de ne pas afficher les lieux qui se trouvent dans le tableau
function getListeLieux($name, $empty=false, $lieux = array()){
	include("./connect.php");
	$list="<select name='".$name."'>";
	if($empty){
		$list.="<option value='0'/>";
	}
	$sql = "SELECT nom FROM lieux;";
	foreach ($db->query($sql) as $row)
	{
		if(!in_array($row['nom'],$lieux )){
			$list.= "<option value='".$row['nom']."'>".$row['nom']."</option>";
		}
	}
	$list.= "</select>";
	return $list;
}

function getListeDepartements($name, $empty=false){
	include("./connect.php");
	$list="<select name='".$name."'>";
	if($empty){
		$list.="<option value='0'/>";
	}
	$sql = "SELECT departement_id as id, nom FROM departements ORDER BY departement_id";
	foreach ($db->query($sql) as $row)
	{
		$list.= "<option value='".$row['id']."'>".$row['id']."-".$row['nom']."</option>";
	}
	$list.= "</select>";
	return $list;
}

function getListeCapteurs($name, $empty=false){
	include("./connect.php");
	$list="<select name='".$name."'>";
	if($empty){
		$list.="<option value='0'/>";
	}
	$sql = "SELECT * FROM capteurs";
	foreach ($db->query($sql) as $row)
	{
		$list.= "<option value='".$row['capteur_id']."'>".$row['capteur_id']."-".$row['lieu_id']."</option>";
	}
	$list.= "</select>";
	return $list;
}

function getListeEnum($name, $enum, $empty=false){
	
	$list="<select name='".$name."'>";
	if($empty){
		$list.="<option value='0'/>";
	}
	
	foreach ($enum as $moment)
	{
		$list.= "<option value='".$moment."'>".$moment."</option>";
	}
	$list.= "</select>";
	return $list;
}


function getInputDate(){
	return "<input type='number' name='jour' min='2' max='2'/> <input type='number' name='mois' min='2' max='2'/><input type='number' name='annee' min='4' max='4'/>";
}




?>