<html>
<?php include("./head.html")?>

	<body>
		
		<?php 
		
		include("./html_form.php");
		
		// Ajout d'un bulletin en BD
		if(isset($_GET["action"])):
			if ($_GET["action"] == "add"):
				// Afficher form d'ajout
				if(!isset($_POST["lieu"])):?>
					
					<form action='./capteurs.php?action=add ' method='POST'>
						
						<?php // On peut créer un capteur sans l'affecter à un lieu ?>
						Choisir le lieu ou se trouve le capteur: <?php echo getListeLieux("lieu",true); ?>
						</br>
						<input type='submit' value='Envoyer' />
					</form>
				<?php
				//Ajouter
				else:
					
					include("./connect.php");
					
					$sql = "INSERT INTO capteurs (lieu_id) VALUES(";
					
					$_POST['lieu']=='0'?$sql.='null'.");":$sql.="'".$_POST['lieu']."');";
													
					$stmt = $db->prepare($sql);

					$stmt->execute();
					$row = $stmt->fetch();
									
					$errors = $db->errorInfo();
					$error = $errors[2];
					
					if(count($error) != 0):
						var_dump($error);
					else:
					?>
						Enregistrement du capteur effectué avec succès.
						</br>
						<a href='./index.html'>Accueil </a>
					<?php
					endif;
				endif;
			else:
				if($_GET["action"] == "update"):
					if(isset($_GET["id"])):
						// Afficher form d'update d'un capteur
						if(!isset($_POST["lieu"])):
							
							include("./connect.php");
								
							$sql = "SELECT lieu_id FROM capteurs WHERE capteur_id = ".$_GET["id"];
							
							$stmt = $db->prepare($sql);
							$stmt->execute();
							$row = $stmt->fetch();
							?>
							Le capteur est actuellement affecté au lieu <?php echo $row["lieu_id"]; ?>
						
							<form action='./capteurs.php?action=update&id=<?php echo $_GET["id"] ?>' method='POST'>
							
								Choisir le nouveau lieu ou sera affecté le capteur (ne rien choisir pour le mettre en réparation): 
								<?php echo getListeLieux("lieu",true, array($row["lieu_id"])); ?>
								<br>
								<input type='submit' value='Envoyer' />
							</form>
						<?php
						else:
							
							include("./connect.php");
							
							$sql = "UPDATE capteurs SET lieu_id = ";
							$_POST['lieu']=='0'?$sql.='null':$sql.="'".$_POST['lieu']."'";
							
							$sql .= " WHERE capteur_id = '".$_GET["id"]."'";
												
							$stmt = $db->prepare($sql);
							
							$stmt->execute();
							$row = $stmt->fetch();
							
							$errors = $db->errorInfo();
							$error = $errors[2];
							
							if(count($error) != 0):
								var_dump($error);
							else:
							?>
								La nouvelle affectation du capteur a été effectuée avec succès.
								<br>
								<a href='./index.html'>Retour </a>
								<meta http-equiv=\"refresh\" content=\"2; URL=./capteurs.php?action=update\">
							<?php
							endif;
						endif;
					else:
					?>
						<form action='./capteurs.php' method='GET'>
							<input type='hidden' name='action' value='update' />
							<?php echo getListeCapteurs("id",false); ?>
							</br>
							<input type='submit' value='Envoyer' />
						</form>
					<?php
					endif;
				endif;
			endif;
		endif;
		?>
	
	
	</body>
	


</html>