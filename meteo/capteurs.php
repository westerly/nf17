<html>
<?php include("./head.html")?>

	<body>
		
		<?php 
		
		include("./html_form.php");
		
		// Ajout d'un bulletin en BD
		if(isset($_GET["action"])  && $_GET["action"] == "add"){
			
			// Afficher form d'ajout
			if(!isset($_POST["lieu"])){
				
				echo"<form action='./capteurs.php?action=add ' method='POST'>";
					
					// On peut créer un capteur sans l'affecter à un lieu 
					echo "Choisir le lieu ou se trouve le capteur: ".getListeLieux("lieu",true);
					
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
				echo"</form>";

			}else{
				
				include("./connect.php");
				
				$sql = "INSERT INTO capteurs (lieu_id) VALUES(";
				
			    $_POST['lieu']=='0'?$sql.='null'.");":$sql.="'".$_POST['lieu']."');";
			    									
				$stmt = $db->prepare($sql);

				$stmt->execute();
				$row = $stmt->fetch();
								
				$errors = $db->errorInfo();
				$error = $errors[2];
				
				
				if(count($error) != 0){
					var_dump($error);
				}else{
					print "Enregistrement du capteur effectué avec succès.";
					print"</br>";
					print "<a href='./index.html'>Accueil </a>";
				}
			
			}
			
		}else{

			if(isset($_GET["action"])  && $_GET["action"] == "update" && isset($_GET["id"])){
				// Afficher form d'update d'un capteur
				if(!isset($_POST["lieu"])){
					
					include("./connect.php");
						
					$sql = "SELECT lieu_id FROM capteurs WHERE capteur_id = ".$_GET["id"];
					
					$stmt = $db->prepare($sql);
					$stmt->execute();
					$row = $stmt->fetch();
					
					echo "Le capteur est actuellement affecté au lieu ".$row["lieu_id"];
				
					echo"<form action='./capteurs.php?action=update&id=".$_GET["id"]."' method='POST'>";
					//echo"<input type='hidden' name='capteur_id' value='".$_GET["id"]."' />";
					
					echo "Choisir le nouveau lieu ou sera affecté le capteur (ne rien choisir pour le mettre en réparation): ".getListeLieux("lieu",true, array($row["lieu_id"]));
						
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
					echo"</form>";
				
				}else{
					
					include("./connect.php");
					
					$sql = "UPDATE capteurs SET lieu_id = ";
					$_POST['lieu']=='0'?$sql.='null':$sql.="'".$_POST['lieu']."'";
					
					$sql .= " WHERE capteur_id = ".$_GET["id"];
										
					$stmt = $db->prepare($sql);
					
					$stmt->execute();
					$row = $stmt->fetch();
					
					$errors = $db->errorInfo();
					$error = $errors[2];
					
					if(count($error) != 0){
						var_dump($error);
					}else{
						print "La nouvelle affectation du capteur a été effectuée avec succès.";
						print"</br>";
						print "<a href='./index.html'>Accueil </a>";
					}
				
				}

			}else{
				if(isset($_GET["action"])  && $_GET["action"] == "update"){
					
					echo"<form action='./capteurs.php' method='GET'>";
					echo"<input type='hidden' name='action' value='update' />";
					
					echo getListeCapteurs("id",false);
					
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
					echo"</form>";

				}else{
					if(isset($_GET["action"])  && $_GET["action"] == "historique")
					{
						if(isset($_GET["etape"])  && $_GET["etape"] == "1"){
							echo"<form action='./capteurs.php?action=historique&etape=2 ' method='POST'>";
							// On peut créer un capteur sans l'affecter à un lieu
							echo "Choisir un capteur: ".getListeCapteurs("capteur",false);
								
							echo "</br>";
							echo "<input type='submit' value='Envoyer' />";
							echo"</form>";
							
						}else{
							if(isset($_GET["etape"])  && $_GET["etape"] == "2"){
								
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
					}
				}

			}

		}
		
		?>
	
	
	</body>
	


</html>