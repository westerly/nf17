<html>
<?php include("./head.html")?>
<body>
<?php 

include("./html_form.php");

// Ajout d'un lieu en BD
if(isset($_GET["action"]) && $_GET["action"] == "add"){
	
	// Afficher form d'ajout
	if(!isset($_POST["nom"])){
		
		echo"<form action='./lieux.php?action=add ' method='POST'>";
			echo "<input type='hidden' name='action' value='add' />";
			echo "Nom: <input type='text' name='nom' />";
			echo"<br/>";
			echo "Seuil température: <input type='text' name='seuiltp' />";
			echo"<br/>";
			echo "Seuil vent: <input type='text' name='seuilvt' />";
			echo"<br/>";
			echo "Seuil précipitation (entre 1 et 10): <input type='text' name='seuilpr' />";
			echo"<br/>";
		
			echo "Type de lieu: <select id='type' name='type'>";
			echo "<option>Ville";
			echo "<option>Massif";
			echo"<select/>";
			echo"<br/>";
			
			// Contenu dynamique en fonction du type de lieu choisi
			echo "<div id='dynamic-content-dp1'>";
			
			
			echo "</div>";
			
			echo "<div id='dynamic-content-dp2'>";
				
				
			echo "</div>";
			
		
		echo "<input type='submit' value='Envoyer' />";
		
		echo"</form>";
		
		
		// Traitement des données POST
	}else{
		include("./connect.php");
		
		$sql = "INSERT INTO lieux(nom,
            seuiltp,
            seuilvt,
            seuilpr) VALUES (
            '".$_POST['nom']."',
            ".$_POST['seuiltp'].",
            ".$_POST['seuilvt'].",
            ".$_POST['seuilpr'].");\n";

		$stmt = $db->prepare($sql);
		
		
// 		$stmt->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
// 		$stmt->bindParam(':seuiltp', $_POST['seuiltp'], PDO::PARAM_STR);
// 		$stmt->bindParam(':seuilvt', $_POST['seuilvt'], PDO::PARAM_STR);
// 		$stmt->bindParam(':seuilpr', $_POST['seuilpr'], PDO::PARAM_STR);

		$stmt->execute();
		
		$errors = $db->errorInfo();
		$error = $errors[2];
		
		if(count($error) != 0){
			var_dump($error);
		}else{
			
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
				
				if(count($error) != 0){	
					var_dump($error);
				}else{
					print "Enregistrement effectué avec succès.";
					print"</br>";
					print "<a href='./index.html'>Accueil </a>";
					
				}

				
			}else{
				$sql = "INSERT INTO villes(nom,
	            dpt) VALUES (
	            :nom,
	            :d1);";
			
				$stmt = $db->prepare($sql);
			
				$stmt->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
				$stmt->bindParam(':d1', $_POST['d1'], PDO::PARAM_INT);
			
				$stmt->execute();
					
				$errors = $db->errorInfo();
				$error = $errors[2];
				
				if(count($error) != 0){	
					var_dump($error);
				}else{
					print "Enregistrement du lieu effectué avec succès.";
					print"</br>";
					print "<a href='./index.html'>Accueil </a>";
					
				}
			}
		}
 	}
}else{
	if(isset($_GET["action"]) && $_GET["action"] == "rechercher"){
		
		if(isset($_POST["date1"])){
			
			echo "Afficher les lieux";
		}else{
			
			echo "Afficher le form";
		}
	}


}


?>

</body>

<?php 

if(!isset($_POST["nom"])){
	echo"<script language='Javascript'>
			
		var contentdp1 = document.getElementById('dynamic-content-dp1');
		contentdp1.innerHTML = \"Département 1: ".getListeDepartements("d1")."\"; \n
	
	document.getElementById('type').onchange = function(){
	
		var contentdp2 = document.getElementById('dynamic-content-dp2');";
	echo"					if(document.getElementById('type').value == 'Massif')\n
							{\n";
								
							echo "contentdp2.innerHTML = \"Département 2: ".getListeDepartements("d2",true)."\";\n";
								
						echo"					
							}else{\n
								contentdp2.innerHTML = '';
								
							}\n
	}\n
	</script>";
}
?>

</html>