<?php 


$enum_moments = array('Matin', 'Après-midi', 'Soirée', 'Nuit');
$enum_directions = array('Nord', 'Sud', 'Ouest', 'Est');
$enum_types = array('Pluie', 'Neige', 'Grêle', 'Sable');

// Le troisième param permet de ne pas afficher les lieux qui se trouvent dans le tableau
function getListeLieux($name, $empty=false, $lieux = array()){
	include("./connect.php");
	$list="<select name='".$name."' id = '".$name."'>";
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
	$query = $db->prepare($sql);
	$query->execute();
	$res = $query->fetchAll();
	foreach ($res as $row)
	{
		$list.= "<option value='".$row['id']."'>".$row['id']."-".$row['nom']."</option>";
	}
	$list.= "</select>";
	return $list;
}

// Le deuxième param permet de produire une liste avec les capteurs affecté à un lieu (si true que les lieux affectés sinon tous les lieux)
function getListeCapteurs($name, $empty=false, $affected = false){
	include("./connect.php");
	$list="<select name='".$name."' id = '".$name."'>";
	if($empty){
		$list.="<option value='0'/>";
	}
	$sql = "SELECT * FROM capteurs";
	foreach ($db->query($sql) as $row)
	{
		if($affected && isset($row["lieu_id"])){
			$list.= "<option value='".$row['capteur_id']."'>".$row['capteur_id']."-".$row['lieu_id']."</option>";
		}
		
		if(!$affected){
			$list.= "<option value='".$row['capteur_id']."'>".$row['capteur_id']."-".$row['lieu_id']."</option>";
		}
		
	}
	$list.= "</select>";
	return $list;
}

function getListeEnum($name, $enum, $empty=false){
	
	$list="<select name='".$name."' id = '".$name."'>";
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
	return "<input type='text' name='jour'/> <input type='text' name='mois'/><input type='text' name='annee'/>";
}

function getLieuIdFromCapteurId($id){
	include("./connect.php");
	
	$sql = "SELECT lieu_id as id FROM capteurs WHERE capteur_id = ".$id;
	
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();
	
	return $row["id"];
	
}

function getListeDatesBulletinsFromLieu($name, $lieu){
	include("./connect.php");
	$list="<select name='".$name."' id = '".$name."'>";
	
	
	$sql = "SELECT distinct date FROM bulletins WHERE lieu_id = '".$lieu."';";
	foreach ($db->query($sql) as $row)
	{
		
		$formatedDate = date("d-m-Y", strtotime($row['date']));
		$list.= "<option value='".$formatedDate."'>".$formatedDate."</option>";
	}
	$list.= "</select>";
	return $list;
}

function getBulletinsRowsFromLieuAndDate($lieu, $date){
	include("./connect.php");
	
	$sql = "select b.moment, b.date, p.type, p.seuil as seuilp, p.force as forcep,
	p.info as infop, p.capteur_id as capteurP, t.reelle, t.ressentie,
	t.seuil as seuilt, t.info as infot, t.capteur_id as capteurt, 
	v.force as forcev, v.direction, v.seuil as seuilv, v.info as infov, v.capteur_id as capteurv from bulletins b 
	LEFT JOIN precipitations p ON b.precipitation_id = p.precipitation_id
	LEFT JOIN temperatures t ON b.temperature_id = t.temperature_id
	LEFT JOIN vents v ON b.vent_id = v.vent_id WHERE date = '".$date."' AND lieu_id='".$lieu."';";
		
	$sth = $db->prepare($sql);
	$sth->execute();
	$res = $sth->fetchAll();
	
	return $res;
	
	
}

function getrowsHistoriqueCapteur($capteur){
	include("./connect.php");
	
	$sql = "select * from historiques where capteur_id = ".$capteur." order by debut";
	
	$sth = $db->prepare($sql);
	$sth->execute();
	$res = $sth->fetchAll();
	
	return $res;
}




?>