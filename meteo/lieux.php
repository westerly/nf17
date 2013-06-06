<html>
	<?php include("./head.html")?>
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
			
			if(isset($_POST["date1"])){
				
				echo "Afficher les lieux";
			}else{
				
				echo "Afficher le form";
			}
		}


	}
	?>
	</body>
</html>