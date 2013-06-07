<html>
	<head>
	<?php include("./head.html")?>
	<style>
		th {
			border-width:1px; 
			border-style:solid; 
			border-color:black;
		}
		
		table { 
			border-width:1px;
			border-style:solid; 
			border-color:black;
			text-align:center;
		}
	</style>
	</head>
	<body>

	<?php if (isset($_GET["action"])) {
		// Ajout d'un lieu en BD
		if ($_GET["action"] == "add") {
			include("./html_form.php");
			// Afficher form d'ajout
			if (!isset($_POST["nom"])) { ?>
				
				<form action='./lieux.php?action=add ' method='POST'>
					<input type='hidden' name='action' value='add' />
					Nom: <input type='text' name='nom' />
					<br>
					Seuil température: <input type='text' name='seuiltp' />
					<br>
					Seuil vent: <input type='text' name='seuilvt' />
					<br>
					Seuil précipitation (entre 1 et 10): <input type='text' name='seuilpr' />
					<br>
					Type de lieu: 
					<select id='type' name='type'>
						<option>Ville</option>
						<option>Massif</option>
					</select>
					<br>
					<!-- Contenu dynamique en fonction du type de lieu choisi -->
					<div id='dynamic-content-dp1'>
					</div>
					<div id='dynamic-content-dp2'>
					</div>
					<input type='submit' value='Envoyer' />
				
				</form>
				
				<?php 

				if(!isset($_POST["nom"])) { 
					?>
					<script language='Javascript'>
								
					var contentdp1 = document.getElementById('dynamic-content-dp1');
					contentdp1.innerHTML = "Département 1: <?php echo getListeDepartements("d1") ?>";
					document.getElementById('type').onchange = function() {
						var contentdp2 = document.getElementById('dynamic-content-dp2');
						if (document.getElementById('type').value == 'Massif') {
							contentdp2.innerHTML = "Département 2: <?php echo getListeDepartements("d2",true) ?>";
						} else {
							contentdp2.innerHTML = '';
						}
					}
					</script>
					<?php
				}
			// Traitement des données POST
			} else {
				include("./connect.php");
				
				$sql = "INSERT INTO lieux(nom, seuiltp, "
					."seuilvt, seuilpr) VALUES ('"
					.$_POST['nom']."',"
					.$_POST['seuiltp'].","
					.$_POST['seuilvt'].","
					.$_POST['seuilpr'].");\n";

				$stmt = $db->prepare($sql);
				
				// stmt->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
				// $stmt->bindParam(':seuiltp', $_POST['seuiltp'], PDO::PARAM_STR);
				// $stmt->bindParam(':seuilvt', $_POST['seuilvt'], PDO::PARAM_STR);
				// $stmt->bindParam(':seuilpr', $_POST['seuilpr'], PDO::PARAM_STR);

				$stmt->execute();
				
				$errors = $db->errorInfo();
				$error = $errors[2];
				
				if (count($error) != 0) {
					var_dump($error);
				} else {
					
					// Insertion d'un massif
					if($_POST["type"] == "Massif"){
					
						$sql = "INSERT INTO massifs(nom,
					    d1,
					    d2) VALUES (
					    :nom,
					    :d1,
					    :d2);";

						$stmt = $db->prepare($sql);
							
						$stmt->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
						$stmt->bindParam(':d1', $_POST['d1'], PDO::PARAM_INT);
						
						if($_POST['d2'] == 0){
							$varNull = null;
							$stmt->bindParam(':d2',$varNull, PDO::PARAM_NULL);
						}else{
							$stmt->bindParam(':d2', $_POST['d2'], PDO::PARAM_INT);
						}
						
						$stmt->execute();
							
						$errors = $db->errorInfo();
						$error = $errors[2];
						
						if (count($error) != 0) {	
							var_dump($error);
						} else {
							print "Enregistrement effectué avec succès.";
							print"</br>";
							print "<a href='./index.html'>Accueil </a>";
							
						}
					} else {
						$sql = "INSERT INTO villes(nom, dpt) VALUES (:nom, :d1);";
					
						$stmt = $db->prepare($sql);
					
						$stmt->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
						$stmt->bindParam(':d1', $_POST['d1'], PDO::PARAM_INT);
					
						$stmt->execute();
							
						$errors = $db->errorInfo();
						$error = $errors[2];
						
						if (count($error) != 0) {	
							var_dump($error);
						} else {
							print "Enregistrement du lieu effectué avec succès.";
							print"</br>";
							print "<a href='./index.html'>Accueil </a>";
							
						}
					}
				}
			}
		}
		else if ($_GET["action"] == "delete")
		{
			include("./connect.php");
			if(!isset($_POST['nom'])) {
				$sql = "SELECT l.nom as nom FROM lieux l "
					."LEFT JOIN capteurs c ON l.nom = c.lieu_id "
					."WHERE c.lieu_id IS NULL;";
				//$sql = "SELECT * FROM lieux WHERE nom NOT IN (SELECT lieu_id FROM capteurs);";
				$query = $db->prepare($sql);
				$query->execute();
				$res = $query->fetchAll();?>
				<h2>Lieux non "couverts"</h2>
				<table>
				<?php foreach($res as $lieu):?>
					<tr><form method='POST' action='./lieux.php?action=delete'>
						<input name="nom" type="hidden" value="<?php echo $lieu['nom']; ?>"/>
						<td><?php echo $lieu['nom']; ?></td>
						<td><input type='submit' value="supprimer"/></td>
					</form></tr>
				<?php endforeach;?>
				</table>
				<a href=".">Acceuil</a>
				<?php 
			} else {
				$sql = "DELETE FROM lieux WHERE nom LIKE '".$_POST['nom']."'";
				$query = $db->prepare($sql);
				$query->execute();
				echo "<meta http-equiv=\"refresh\" content=\"0; URL=./lieux.php?action=delete\">";
			}
		}
		else if ($_GET["action"] == "rechercher")
		{
			if(isset($_GET['step'])) {
				switch($_GET['step']) {
					case '1':
						?>
						<span>Quelle type de mesure vous interesse ?</span>
						<form method=POST action="lieux.php?action=rechercher&step=2">
							<input type="radio" name="type" value="Tmp" checked>Température<br>
							<input type="radio" name="type" value="Vent">Vents<br>
							<input type="radio" name="type" value="Prec">Précipitations<br>
							<input type="submit">
						</form>
						<?php
						break;
					case '2':
						if (isset($_POST['type'])) {
							switch($_POST['type']) {
								case 'Tmp':
									?>
									<span>Quelle type de phénomène vous interesse ?</span>
									<form method=POST action="lieux.php?action=rechercher&step=3">
										<select name="type_m">
											<option value="Treelle" selected>Température réelle</option>
											<option value="Tres">Température ressentie</option>
										</select>
										&nbsp;
										<select name="comp">
											<option value="inf" selected>Inférieure à </option>
											<option value="infe">Inférieure ou égale à </option>
											<option value="e">Egale à </option>
											<option value="supe">Supèrieure ou égale à </option>
											<option value="sup">Supèrieure à </option>
										</select>
										<input type="text" name="value">
										<br>
										<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>"/>
										<input type="submit">
									</form>
									<?php
									break;
								case 'Vent':
									?>
									<span>Quelle type de phénomène vous interesse ?</span>
									<form method=POST action="lieux.php?action=rechercher&step=3">
										<span>Force du vent </span>
										<select name="comp">
											<option value="inf" selected>Inférieure à </option>
											<option value="infe">Inférieure ou égale à </option>
											<option value="e">Egale à </option>
											<option value="supe">Supèrieure ou égale à </option>
											<option value="sup">Supèrieure à </option>
										</select>
										<input type="text" name="value">
										<br>
										<span>Direction : </span>
										<select name="type_m">
											<option value="0" selected>Toutes</option>
											<option value="n">Nord</option>
											<option value="s">Sud</option>
											<option value="e">Est</option>
											<option value="o">Ouest</option>
										</select>
										<br>
										<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>"/>
										<input type="submit">
									</form>
									<?php
									break;
								case 'Prec':
									?>
									<span>Quelle type de phénomène vous interesse ?</span>
									<form method=POST action="lieux.php?action=rechercher&step=3">
										<span> Force des précipitations </span>
										<select name="comp">
											<option value="inf" selected>Inférieure à </option>
											<option value="infe">Inférieure ou égale à </option>
											<option value="e">Egale à </option>
											<option value="supe">Supèrieure ou égale à </option>
											<option value="sup">Supèrieure à </option>
										</select>
										<input type="text" name="value">
										<br>
										<span>Type de précipitation : </span>
										<select name="type_m">
											<option value="0" selected>Toutes</option>
											<option value="p">Pluie</option>
											<option value="n">Neige</option>
											<option value="g">Grêle</option>
											<option value="s">Sable</option>
										</select>
										<br>
										<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>"/>
										<input type="submit">
									</form>
									<?php
									break;
								default:
									break;
							}
						}
						break;
					case '3':
							?>
							
							<form method=POST action="lieux.php?action=rechercher&step=4">
								<?php foreach(array('type','comp','type_m','value') as $key): ?>
								<input type="hidden" name="<?php echo $key; ?>"
									value="<?php echo $_POST[$key];?>">
								<?php endforeach; ?>
								<span>Quel type de résultats souhaitez-vous ?</span>
								<br>
								<select name="type_l">
									<option value="lieu" selected>Lieux</option>
									<option value="dept">Départements</option>
									<option value="region">Régions</option>
								</select>
								<br>
								<input type="submit">
							</form>
							
							<?php
						break;
					case '4':
						if (isset($_POST['type']) && isset($_POST['comp']) && isset($_POST['type_m']) 
						&& isset($_POST['value'])) {
							
							include("./connect.php");
							
							$args = array();
							
							$args['headers'] = array();
							$args['display'] = array(array('v',$_POST['type_l']));
							
							switch($_POST['type_l']) {
								case 'lieu':
									$title = "Lieux correspondants";
									$args['headers'][] = 'Lieu';
									break;
								case 'dept':
									$title = "Départements correspondants";
									$args['headers'][] = 'Département';
									break;
								case 'region':
									$title = "Régions correspondantes";
									$args['headers'][] = 'Région';
									break;
							}
							
							$args['headers'][] = 'Date';
							$args['display'][] = array('b','date');
							
							
							$comp = null;
							switch($_POST['comp']) {
								case 'inf':
									$comp = '<';
									break;
								case 'infe':
									$comp = '<=';
									break;
								case 'e':
									$comp = '=';
									break;
								case 'supe':
									$comp = '>=';
									break;
								case 'sup':
									$comp = '>';
									break;
							}
							
							switch ($_POST['type']) {
								case 'Tmp':
									$args['table'] = 'temperatures';
									$args['join'] = 'temperature_id';
									if ($_POST['type_m'] == 'Treelle') {
										$args['headers'][] = 'T° Réelle';
										$args['display'][] = array('x','reelle');
										$args['conditions'] = array('x.reelle '.$comp.' '.$_POST['value']);
									} else {
										$args['headers'][] = 'T° Ressentie';
										$args['display'][] = array('x','ressentie');
										$args['conditions'] = array('x.ressentie '.$comp.' '.$_POST['value']);
									}
									break;
								case 'Vent':
									$args['table'] = 'vents';
									$args['join'] = 'vent_id';
									$args['headers'][] = 'Force';
									$args['display'][] = array('x','force');
									$args['conditions'] = array('x.force '.$comp.' '.$_POST['value']);
									switch($_POST['type_m']) {
										case '0':
											$args['headers'][] = 'Direction';
											$args['display'][] = array('x','direction');
											break;
										case 'n':
											$args['conditions'][] = 'x.direction = \'Nord\'';
											break;
										case 's':
											$args['conditions'][] = 'x.direction = \'Sud\'';
											break;
										case 'e':
											$args['conditions'][] = 'x.direction = \'Est\'';
											break;
										case 'o':
											$args['conditions'][] = 'x.direction = \'Ouest\'';
											break;
									}
									break;
								case 'Prec':
									$args['table'] = 'precipitations';
									$args['join'] = 'precipitation_id';
									$args['headers'][] = 'Force';
									$args['display'][] = array('x','force');
									$args['conditions'] = array('force '.$comp.' '.$_POST['value']);
									switch($_POST['type_m']) {
										case '0':
											$args['headers'][] = 'Type';
											$args['display'][] = array('x','type');
											break;
										case 'p':
											$args['conditions'][] = 'x.type = \'Pluie\'';
											break;
										case 'n':
											$args['conditions'][] = 'x.type = \'Neige\'';
											break;
										case 'g':
											$args['conditions'][] = 'x.type = \'Grêle\'';
											break;
										case 's':
											$args['conditions'][] = 'x.type = \'Sable\'';
											break;
									}
									break;
							}
							
							$selected = implode('.',$args['display'][0]);
							
							$sql = "SELECT ".$selected.", COUNT(x.".$args['join'].") as nb ";
							$sql .= "FROM bulletins b ";
							$sql .= "INNER JOIN vlieudeptreg v ON v.lieu = b.lieu_id ";
							$sql .= "INNER JOIN ".$args['table']." x ON b.".$args['join']." = x.".$args['join']." ";
							$sql .= "WHERE ".implode(' AND ',$args['conditions'])." ";
							$sql .= "GROUP BY ".$selected." ";
							$sql .= "ORDER BY nb DESC";
							
							$query = $db->prepare($sql);
							$query->execute();
							$res = $query->fetchAll();
							
							$selected = array($args['display'][0][1],'nb');
							
							$headers = array($args['headers'][0],'Nombre correspondant');
							
							?>
								<h3><?php echo $title; ?> :</h3>
								<table>
									<tr>
										<?php foreach($headers as $h): ?>
											<th><?php echo $h; ?></th>
										<?php endforeach; ?>
									</tr>
									<?php foreach($res as $row) : ?>
										<tr>
										<?php foreach($selected as $col): ?>
											<td><?php echo $row[$col]; ?></td>
										<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								</table>
							<?php
							
							
							$selected = array();
							foreach($args['display'] as $disp) {
								$selected[] = implode('.',$disp);
							}
							
							$sql2 = "SELECT ".implode(', ',$selected)." ";
							$sql2 .= "FROM bulletins b ";
							$sql2 .= "INNER JOIN vlieudeptreg v ON v.lieu = b.lieu_id ";
							$sql2 .= "INNER JOIN ".$args['table']." x ON b.".$args['join']." = x.".$args['join']." ";
							$sql2 .= "WHERE ".implode(' AND ',$args['conditions'])." ";
							$sql2 .= "ORDER BY v.lieu;";
							
							$query = $db->prepare($sql2);
							$query->execute();
							$res = $query->fetchAll();
							
							?>
							<br>
							<h3>Détails :</h3>
							<table>
								<tr>
									<?php foreach($args['headers'] as $h): ?>
										<th><?php echo $h; ?></th>
									<?php endforeach; ?>
								</tr>
								<?php foreach($res as $row) : ?>
									<tr>
									<?php foreach($args['display'] as $col): ?>
										<td><?php echo $row[$col[1]]; ?></td>
									<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</table>
							<?php
						}
						break;
					default:
						break;
				}
			}
		}


	}
	?>
	<br>
	<br>
	<a href=".">Acceuil</a>
	</body>
</html>