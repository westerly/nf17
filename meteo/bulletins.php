<html>
<?php include("./head.html")?>

	<body>
		
		<?php 
		
		include("./html_form.php");
		
		// Ajout d'un bulletin en BD
		if($_GET["action"] == "add"){
			
			// Afficher form d'ajout
			if(!isset($_POST["nom"])){
				
				echo"<form action='./bulletins.php?action=add ' method='POST'>";
					echo "Lieu : ".getListeLieux("lieu");
					echo "</br>";
					echo "Moment : ".getListeEnum("moment", $enum_moments);
					echo "</br>";
					echo "Date (jj/mm/aaaa): ".getInputDate();
					
					echo "<br/>";
					echo "Type de prévision: <select id='typePrevision' name='typePrevision'>";
					echo "<option>Précipitation";
					echo "<option>Température";
					echo "<option>Vent";
					echo "<select/>";
					echo "<br/>";
					
					echo "<textarea name='info' rows='4' cols='50'></textarea>";
					
					echo "<br/>";
					
					echo "<div id='dynamic-content-type'>";
					echo "</div>";
					echo "Capteur : ".getListeCapteurs("capteur");
					
					
					echo "</br>";
					echo "<input type='submit' value='Envoyer' />";
				echo"</form>";
		

			}
			
		}
		
		?>
	
	
	</body>
	
<?php if(!isset($_POST["nom"])){ ?>
	<script>
			
		var content = document.getElementById('dynamic-content-type');

		content.innerHTML = "Type de précipitation: <?php echo getListeEnum("typePrecipitation", $enum_types)?>" + 
		" </br> Seuil précipitation: <input type='text' name='seuil'/> </br> Force: <input type='text' name='force'/>";

		document.getElementById("typePrevision").value = "Précipitation";
	
		document.getElementById('typePrevision').onchange = function(){
	
			var content = document.getElementById('dynamic-content-type');
			if(document.getElementById('typePrevision').value == 'Précipitation')
				{
					content.innerHTML = "Type de précipitation: <?php echo getListeEnum("typePrecipitation", $enum_types)?>" + 
					" </br> Seuil précipitation (entre 1 et 10): <input type='text' name='seuil'/> </br> Force (entre 1 et 10): <input type='text' name='force'/>";						
				}else if(document.getElementById('typePrevision').value == 'Température'){
					content.innerHTML ="Température réelle: <input type='text' name='reelle'/> </br> Température ressentie: <input type='text' name='ressentie'/>  </br> Seuil température: <input type='text' name='seuil'/>";	
					
				}else{
					content.innerHTML = "Type de précipitation: <?php echo getListeEnum("direction", $enum_directions)?>" + 
					" </br> Seuil vent: <input type='text' name='seuil'/> </br> Force: <input type='text' name='force'/>";		

				}
		}
	</script>
<?php }?>


</html>