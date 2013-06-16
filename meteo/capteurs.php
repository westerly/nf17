<html>
	<head>
	<?php include("./head.php")?>
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
		
		<?php 
		
		include("./html_form.php");
		
		// Ajout d'un bulletin en BD
		if (isset($_GET["action"])) {
			
			if(!isset($_SESSION["userCo"]) && in_array($_GET["action"], array ("update", "add"))){
				echo "Vous devez être connecté pour utiliser cette fonction.";
				goto end;
			}
			
			
			if ($_GET["action"] == "add") {
				// Afficher form d'ajout
				if (!isset($_POST["lieu"])) { ?>
					<form method=POST>
						Choisir le lieu ou se trouve le capteur: 
						<?php echo getListeLieux("lieu",true); ?>
						<br>
						Laisser vide pour que le capteur ne soit pas affecté
						<br>
						<input type='submit' value='Envoyer' />
					</form>
					<?php
				} else {
				
					include("./connect.php");
					
					$sql = "INSERT INTO capteurs (lieu_id) VALUES(";
					// Créer un Capteur affecter ou non
					$_POST['lieu']=='0'?$sql.='null'.");":$sql.="'".$_POST['lieu']."');";
					
					$stmt = $db->prepare($sql);

					$stmt->execute();
					$row = $stmt->fetch();
									
					$errors = $db->errorInfo();
					$error = $errors[2];
					
					
					if (count($error) != 0) {
						var_dump($error);
					} else {
						print "Enregistrement du capteur effectué avec succès.";
						print"</br>";
						print "<a href='./index.php'>Accueil </a>";
					}
			
				}
			
			} else if($_GET["action"] == "update") {
				if (isset($_GET["id"])) {
					// Afficher form d'update d'un capteur
					if(!isset($_POST["lieu"])) {
						
						include("./connect.php");
							
						$sql = "SELECT lieu_id FROM capteurs WHERE capteur_id = ".$_GET["id"];
						
						$stmt = $db->prepare($sql);
						$stmt->execute();
						$row = $stmt->fetch();
						
						echo "Le capteur est actuellement affecté au lieu ".$row["lieu_id"];
					
						echo"<form action='./capteurs.php?action=update&id=".$_GET["id"]."' method='POST'>";
						//echo"<input type='hidden' name='capteur_id' value='".$_GET["id"]."' />";
						
						echo "Choisir le nouveau lieu ou sera affecté le capteur (ne rien choisir pour le mettre en réparation): ".getListeLieux("lieu",true, array($row["lieu_id"]));
						?>
							</br>
							<input type='submit' value='Envoyer' />
						</form>
					<?php
					} else {
						
						include("./connect.php");
						
						$sql = "UPDATE capteurs SET lieu_id = ";
						
						$_POST['lieu']=='0'?$sql.="null":$sql.="'".$_POST['lieu']."'";
						
						$sql .= " WHERE capteur_id = '".$_GET['id']."';";
						$stmt = $db->prepare($sql);

						$stmt->execute();
						$row = $stmt->fetch();
										
						$errors = $db->errorInfo();
						$error = $errors[2];
						
						if(count($error) != 0) {
							var_dump($error);
						}else{
							print "La nouvelle affectation du capteur a été effectuée avec succès.";
						}
					
					}

				}else{
					echo"<form action='./capteurs.php' method='GET'>";
					echo"<input type='hidden' name='action' value='update' />";
					
					echo getListeCapteurs("id",false);
					
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
					echo"</form>";
				}
			} else if($_GET["action"] == "historique") {
				if(isset($_GET["etape"])) {
					if ($_GET["etape"] == "1") {
						echo"<form action='./capteurs.php?action=historique&etape=2 ' method='POST'>";
						// On peut créer un capteur sans l'affecter à un lieu
						echo "Choisir un capteur: ".getListeCapteurs("capteur",false);
							
						echo "</br>";
						echo "<input type='submit' value='Envoyer' />";
						echo"</form>";
					} else if($_GET["etape"] == "2") {
						$histo = getrowsHistoriqueCapteur($_POST["capteur"]);
							
						if(count($histo)!=0){
							echo"<table style='border:1px solid black'>";
							echo"<th>Lieu d'affectation</th><th>Date de début d'affectation</th><th>Date de fin d'affectation</th>";
							foreach($histo as $ligne){
								$formatedDateDebut=null;
								$formatedDateFin=null;
								
								if(isset($ligne['debut'])){
									$formatedDateDebut = date("d-m-Y H:i", strtotime($ligne['debut']));
								}
								if(isset($ligne['fin'])){
									$formatedDateFin = date("d-m-Y H:i", strtotime($ligne['fin']));
								}
								
								echo"<tr><td>".$ligne["lieu_id"]."</td><td>".$formatedDateDebut."</td><td>".$formatedDateFin."</td></tr>";
							}
							
							echo"</table>";
							
						}else{
							echo "Pas d'historique disponible pour ce capteur.";
						}
					}
				}
			} else if($_GET["action"] == "view") {
				include("./connect.php");
				$sql = "Select * FROM capteurs;";
				
				$stmt = $db->prepare($sql);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				echo "<table><tr><th>Capteur</th><th>Lieu</th></tr>";
				foreach ($rows as $row) {
					echo "<tr><td>".$row['capteur_id']."</td><td>".$row['lieu_id']."</td></tr>";
				}
				echo "</table>";
			}
		}
		
		end:
		?>
	<br>
	<br>
	<a href=".">Accueil</a>
	</body>
</html>