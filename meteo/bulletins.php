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
		
		
		if(isset($_GET["action"]) && !isset($_SESSION["userCo"]) && in_array($_GET["action"], array ("add"))){
			echo "Vous devez être connecté pour utiliser cette fonction.";
			goto end;
		}
		
		// Ajout d'un bulletin en BD
		if(isset($_GET["action"]) && $_GET["action"] == "add"){
			
			// Afficher form d'ajout
			if(!isset($_POST["jour"])){
				
				echo"<form action='./bulletins.php?action=add ' method='POST'>";
					//echo "Lieu : ".getListeLieux("lieu");
					echo "</br>";
					echo "Moment : ".getListeEnum("moment", $enum_moments);
					echo "</br>";
					echo "Date (jj/mm/aaaa): ".getInputDate();
					
					echo "<br/>";
					echo "<h1> Précipitation: </h1>";
					echo "Type de précipitation: ";
					echo getListeEnum("typePrecipitation", $enum_types);
					echo " </br> Seuil précipitation (entre 1 et 10): <input type='text' name='seuilpr'/> </br> Force (entre 1 et 10): <input type='text' name='forcepr'/> </br>";
					echo "Commentaires: ";
					
					echo "</br><textarea name='infopr' rows='4' cols='50'></textarea>";
						
					echo "<br/>";
					echo "Capteur ayant enregistré les mesures: ".getListeCapteurs("capteurpr", false, true);
						
					
					echo "<h1> Température: </h1>";
					echo "Température réelle: <input type='text' name='reelle'/> </br> Température ressentie: <input type='text' name='ressentie'/>  </br> Seuil température: <input type='text' name='seuiltp'/> </br>";
					echo "Commentaires: ";
					
					echo "</br> <textarea name='infotp' rows='4' cols='50'></textarea>";
						
					echo "<br/>";
					echo "Capteur ayant enregistré les mesures: ".getListeCapteurs("capteurtp", false, true);
						
					
					echo "<h1> Vent: </h1>";
					echo "Direction: ".getListeEnum( "direction", $enum_directions);
					echo " </br> Seuil vent: <input type='text' name='seuilvt'/> </br> Force: <input type='text' name='forcevt'/>";
					
					echo "<br>Commentaires: ";
					echo "</br><textarea name='infovt' rows='4' cols='50'></textarea>";
				
						
					echo "<br/>";
					echo "Capteur ayant enregistré les mesures: ".getListeCapteurs("capteurvt", false, true);
						
					
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
					
				echo"</form>";
		

			}else{
				if(!empty($_POST["seuilpr"]) || !empty($_POST["seuilvt"]) || !empty($_POST["seuiltp"])){
					include("./connect.php");
					
					
					$idtp = null;
					$idvt = null;
					$idpr = null;
					
					$continue = true;
					
					$db->beginTransaction();
					
					if(!empty($_POST["seuilpr"]))
					{
						
						
						$sql = "INSERT INTO precipitations(type,
				            seuil,
				            force,
				            info,
							capteur_id) VALUES (
				            '".$_POST['typePrecipitation']."',
				            ".$_POST['seuilpr'].",
				            ".$_POST['forcepr'].",
				            '".$_POST['infopr']."',
				            ".$_POST['capteurpr'].");";
						
							//print $sql;
								
							$stmt = $db->prepare($sql);
							
							$stmt->execute();
								
							$stmt = $db->prepare("select currval(pg_get_serial_sequence('precipitations','precipitation_id'));");
							$stmt->execute();
							$row = $stmt->fetch();
							$idpr = $row["currval"];
								
								
							$errors = $db->errorInfo();
							$error = $errors[2];
							
							if(count($error) != 0){
								var_dump($error);
								$continue = false;
								$db->rollBack();
							}	
					}
					
					
					if($continue){
						if(!empty($_POST["seuiltp"]))
						{							
							$sql = "INSERT INTO temperatures(reelle,
				            ressentie,
				            seuil,
				            info,
							capteur_id) VALUES (
				            '".$_POST['reelle']."',
				            ".$_POST['ressentie'].",
				            ".$_POST['seuiltp'].",
				            '".$_POST['infotp']."',
				            ".$_POST['capteurtp'].");\n";
							
							//print $sql;
							
							$stmt = $db->prepare($sql);
							
							$stmt->execute();
							
							$stmt = $db->prepare("select currval(pg_get_serial_sequence('temperatures','temperature_id'));");
							$stmt->execute();
							$row = $stmt->fetch();
							$idtp = $row["currval"];
							
							
							$errors = $db->errorInfo();
							$error = $errors[2];
							
							if(count($error) != 0){
								var_dump($error);
								$continue = false;
								$db->rollBack();
							}
						
						}
						
					}
						
						
					if($continue){
						if(!empty($_POST["seuilvt"]))
						{
							$sql = "INSERT INTO vents(force,
					            direction,
					            seuil,
					            info,
								capteur_id) VALUES (
					            '".$_POST['forcevt']."',
					            '".$_POST['direction']."',
					            ".$_POST['seuilvt'].",
					            '".$_POST['infovt']."',
					            ".$_POST['capteurtp'].");\n";
							
								//print $sql;
								
								$stmt = $db->prepare($sql);
									
								$stmt->execute();
									
								$stmt = $db->prepare("select currval(pg_get_serial_sequence('vents','vent_id'));");
								$stmt->execute();
								$row = $stmt->fetch();
								$idvt = $row["currval"];
									
								$errors = $db->errorInfo();
								$error = $errors[2];
								
								if(count($error) != 0){
									var_dump($error);
									$continue = false;
									$db->rollBack();
								}
								
						}
					}
							

					if($continue){
						
						if(isset($_POST['capteurtp'])){
							$lieuBulletin = getLieuIdFromCapteurId($_POST['capteurtp']);
						}else{
							if(isset($_POST['capteurpr'])){
								$lieuBulletin = getLieuIdFromCapteurId($_POST['capteurpr']);
							}else{
								if(isset($_POST['capteurvt'])){
									$lieuBulletin = getLieuIdFromCapteurId($_POST['capteurvt']);
								}
							}
						}
						
						
						$sql = "INSERT INTO bulletins(lieu_id,
					            moment,
					            date,
								precipitation_id,
								temperature_id,
								vent_id
					            ) VALUES (
					            '".$lieuBulletin."',
					            '".$_POST['moment']."',
					            '".$_POST['jour']."-".$_POST['mois']."-".$_POST['annee']."',";
					            isset($idpr)?$sql.=$idpr:$sql.="null";
					           	$sql.=", ";
					           	isset($idtp)?$sql.=$idtp:$sql.="null";
					            $sql.=", ";
					            isset($idvt)?$sql.=$idvt:$sql.="null";
					            $sql.=");\n";
						
						//print $sql;
						
						$stmt = $db->prepare($sql);
							
						$stmt->execute();
						$errors = $db->errorInfo();
						$error = $errors[2];
							
						if(count($error) != 0){
							var_dump($error);
						}else{
							$db->commit();
							echo "Le bulletin a été enregistré avec succès.";
						}
						
					}					
					
				}else{
					echo "Vous devez remplir les informations sur au moins un type de prévision (Température, Précipitations, Vent)";
				}

			}
			
		}
		?>
		
		<?php if(isset($_GET["action"]) && $_GET["action"] == "search"){?>
			<?php if(isset($_GET["etape"]) && $_GET["etape"] == "1"){?>
			
			<form action='./bulletins.php?action=search&etape=2' method='POST'>
				Choisir un lieu:
				<?php echo getListeLieux("lieu");?>
				</br>
				<input type='submit' value='Envoyer' />
			</form>
		
			<?php 
		
				}else{
					if(isset($_GET["etape"]) && $_GET["etape"] == "2"){ ?>
						
						<form action='./bulletins.php?action=search&etape=3' method='POST'>
							<input type = "hidden" name="lieu" value="<?php echo $_POST["lieu"];?>" />
							<?php 						
								echo getListeDatesBulletinsFromLieu("date", $_POST["lieu"]);						
							?>
							<input type='submit' value='Envoyer' />
						</form>
					
					<?php 


					}else{
						// Affichage des bulletins
						if(isset($_GET["etape"]) && $_GET["etape"] == "3"){
							
							$bulletins = getBulletinsRowsFromLieuAndDate($_POST["lieu"], $_POST["date"]);
							
							//var_dump($bulletins);
							if(count($bulletins)!=0){

								$i = 1;
								foreach($bulletins as $b){
									echo "<b>Bulletin numéro ".$i++." : </b>";
									echo"<table style='border:1px solid black'>";
										echo "<tr><th>Moment</th></tr>";
										echo"<tr>";
											echo "<td>".$b['moment']."</td>";
										echo"</tr>";
									echo"</table>";
									echo"</br>";
									
									if(isset($b["type"])){
										echo "Precipitations: ";
										echo"<table style='border:1px solid black'>";
											echo "<tr style='border:1px'><th>Type</th><th>Seuil</th><th>Force</th><th>Infos</th><th>Capteur Id</th></tr>";
											echo"<tr>";
											echo "<td>".$b['type']."</td><td>".$b['seuilp']."</td><td>".$b['forcep']."</td><td>".$b['infop']."</td><td>".$b['capteurp']."</td>";
											echo"</tr>";
										echo"</table>";
										echo"</br>";
									}
									
									if(isset($b["reelle"])){
										echo "Températures: ";
										echo"<table style='border:1px solid black'>";
										echo "<tr><th>Reelle</th><th>Ressentie</th><th>Seuil</th><th>Infos</th><th>Capteur Id</th></tr>";
										echo"<tr>";
										echo "<td>".$b['reelle']."</td><td>".$b['ressentie']."</td><td>".$b['seuilt']."</td><td>".$b['infot']."</td><td>".$b['capteurt']."</td>";
										echo"</tr>";
										echo"</table>";
										echo"</br>";
									}
									
									if(isset($b["forcev"])){
										echo "Vents: ";
										echo"<table style='border:1px solid black'>";
										echo "<tr><th>Force</th><th>Direction</th><th>Seuil</th><th>Infos</th><th>Capteur Id</th></tr>";
										echo"<tr>";
										echo "<td>".$b['forcev']."</td><td>".$b['direction']."</td><td>".$b['seuilv']."</td><td>".$b['infov']."</td><td>".$b['capteurv']."</td>";
										echo"</tr>";
										echo"</table>";
										echo"</br>";
									}
								}
							}else{
								echo "Aucun bulletin disponible.";
							}
							
						}

					}

				} 
			
	

		}

		end:
		?>
		
		</br>
		</br>
		<a href=".">Acceuil</a>
		
		
	
	
	</body>
	


</html>