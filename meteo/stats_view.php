<?php
	include("head.php");
	include("connect.php");
	if(isset($_POST['check'])) {
		// recuperation de la liste de lieu
		$sql_1 = "SELECT ";
		$sql_2 = "FROM bulletins b ";
		$sql_3 = "WHERE ";
		switch($_POST['type_lieu']) {
			// REGION
			case 'reg':
				$sql_1 .= "r.nom, ";
				$sql_2 .= "INNER JOIN vLieuDeptReg ld ON ld.lieu=b.lieu_id ";
				$sql_2 .= "INNER JOIN regions r ON r.num=ld.region ";
				$sql_3 .= "ld.region = '".$_POST['reg']."' ";
				break;
			// DEPARTEMENT
			case 'dept':
				$sql_1 .= "d.nom, ";
				$sql_2 .= "INNER JOIN vLieuDeptReg ld ON ld.lieu=b.lieu_id ";
				$sql_2 .= "INNER JOIN departements d ON d.departement_id=ld.dept ";
				$sql_3 .= "ld.dept = '".$_POST['dept']."' ";
				break;
			// LIEU
			case 'lieu':
				$sql_3 .= "b.lieu_id = '".$_POST['lieu']."' ";
				break;
		}
		
		$annee_d = $_POST['annee_d'] < 10 ? '0'.$_POST['annee_d'] : $_POST['annee_d'];
		$mois_d = $_POST['mois_d'];
		$jour_d = $_POST['jour_d'];
		$annee_d = $_POST['annee_d'];
		$mois_d = $_POST['mois_d'];
		$jour_d = $_POST['jour_d'];
		
		$date_d = $annee_d.'-'.$_POST['mois_d'].'-'.$_POST['jour_d'];
		$date_f = $_POST['annee_f'].'-'.$_POST['mois_f'].'-'.$_POST['jour_f'];
		$sql_3 .= 'AND date_trunc(\'day\', b.date) >= \''.$date_d.'\' ';
		$sql_3 .= 'AND date_trunc(\'day\', b.date) <= \''.$date_f.'\' ';
		
		
		$temp = false;
		$vent = false;
		$pres = false;
		$init = false;
		if(isset($_POST['temp']) && $_POST['temp'] == 'on') {
			$sql_1 .= "temperature_id ";
			$init = true;
			$temp = true;
		}
		if(isset($_POST['vent']) && $_POST['vent'] == 'on') {
			if($init) {
				$sql_1 .= ", ";
			}
			$sql_1 .= "vent_id ";
			$init = true;
			$vent = true;
		}
		if(isset($_POST['pres']) && $_POST['pres'] == 'on') {
			if($init) {
				$sql_1 .= ", ";
			}
			$sql_1 .= "precipitation_id ";
			$pres = true;
		}
		
		
		
		$sql = $sql_1.$sql_2.$sql_3.';';
		//echo $sql;
		$query = $db->prepare($sql);
		$query->execute();
		$res = $query->fetchAll();
		
		$nb = count($res);
		
		$init = false;
		if($temp) {
			$temp_a = array();
			for($i = 0;$i < $nb;$i++) {
				if ($res[$i]['temperature_id'] != null) {
					$temp_a[] = $res[$i]['temperature_id'];
				}
			}
			if (!empty($temp_a)) {
				$init = true;
				$temp_s = implode(',',$temp_a);
				$count = count($temp_a);
				$sql = "SELECT sum(reelle) as reelle, sum(ressentie) as ressentie, sum(seuil) as seuil FROM temperatures WHERE temperature_id IN (".$temp_s.");";				
				$query = $db->prepare($sql);
				$query->execute();
				$infos = $query->fetchAll();
				//var_dump($infos);
				$moy['reelle'] = $infos[0]['reelle'] / $count;
				$moy['ressentie'] = $infos[0]['ressentie'] / $count;
				$moy['seuil'] = $infos[0]['seuil'] / $count;
				?>
				<b style="font-size:15pt">Températures moyennes</b>
				<table>
					<tr><th>Réelle</th><th>Ressentie</th><th>Seuil</th></tr>
					<tr><td><?php echo $moy['reelle']; ?></td><td><?php echo $moy['ressentie']; ?></td><td><?php echo $moy['seuil']; ?></td></tr>
				</table>
				<?php
			}
		}
		if($vent) {
			$vent_a = array();
			for($i = 0;$i < $nb;$i++) {
				//var_dump($res[$i]);
				if ($res[$i]['vent_id'] != null) {
					$vent_a [] = $res[$i]['vent_id'];
				}
			}
			if (!empty($vent_a)) {
				$init = true;
				$vent_s = implode(',',$vent_a);
				$count = count($vent_a);
				$sql = "SELECT sum(force) as force, sum(seuil) as seuil FROM vents WHERE vent_id IN (".$vent_s.");";				
				$query = $db->prepare($sql);
				$query->execute();
				$infos = $query->fetchAll();
				//var_dump($infos);
				$moy['force'] = $infos[0]['force'] / $count;
				$moy['seuil'] = $infos[0]['seuil'] / $count;
				?>
				<br>
				<b style="font-size:15pt">Vents moyens</b>
				<table>
					<tr><th>Force</th><th>Seuil</th></tr>
					<tr><td><?php echo $moy['force']; ?></td><td><?php echo $moy['seuil']; ?></td></tr>
				</table>
				<?php
			}
		}
		if($pres) {
			$pres_a = array();
			for($i = 0;$i < $nb;$i++) {
				if ($res[$i]['precipitation_id'] != null) {
					$pres_a[] = $res[$i]['precipitation_id'];
				}
			}
			if (!empty($pres_a)) {
				$init = true;
				$pres_s = implode(',',$pres_a);
				$count = count($pres_a);
				$sql = "SELECT sum(force) as force, sum(seuil) as seuil FROM precipitations WHERE precipitation_id IN (".$pres_s.");";				
				$query = $db->prepare($sql);
				$query->execute();
				$infos = $query->fetchAll();
				//var_dump($infos);
				$moy['force'] = $infos[0]['force'] / $count;
				$moy['seuil'] = $infos[0]['seuil'] / $count;
				?>
				<br>
				<b style="font-size:15pt">Précipitations moyennes</b>
				<table>
					<tr><th>Force</th><th>Seuil</th></tr>
					<tr><td><?php echo $moy['force']; ?></td><td><?php echo $moy['seuil']; ?></td></tr>
				</table>
				<?php
			}
		}
		
		if (!$init) {
			echo "Aucun enregistrement ne correspond.<br>";
		}
		
		
	}
?>
<br>
<br>
<a href=".">Accueil</a>