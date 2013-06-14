
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<?php include("./head.php")?>


<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="styles.css">
	
	<title>Gestion des bulletins météo</title>
</head>
<body>
	<?php
			if(isset($_SESSION['userCo']) && $_SESSION['userCo'] == 1){ ?>
				<form action="user.php?action=deconnect" method="POST">
				<input type="submit" value="Déconnexion"/>
				</form>
			<?php }else{ ?>
	<p>
		<form action="user.php?action=connect" method="POST">
			Login: <input type="text" name = "login" value="admin"/>
			Mot de passe: <input type="password" name = "mdp" value="admin"/>
			<input type="submit" value="Connexion"/>
		</form>
	</p>
	
		<?php } ?>
	
	<h2>Menu</h2>
	<a href="./capteurs.php?action=view">Liste des capteurs</a></br>
	<a href="./capteurs.php?action=historique&etape=1">Historique de l'affectation des capteurs</a>
	</br></br>
	
	<a href="./stats.php">Statistiques sur les mesures</a></br></br>
	<a href="./alertes.php">Alertes</a></br></br>
	<a href="./bulletins.php?action=search&etape=1">Rechercher un bulletin</a></br>
	<a href="./lieux.php?action=rechercher&step=1">Rechercher les lieux, départements ou régions les plus concernés par certains types de prévisions</a></br>
	
	
	
	<?php if(isset($_SESSION['userCo']) && $_SESSION['userCo'] == 1){ ?>
		<h2>Menu administrateur</h2>
		<a href="./lieux.php?action=add">Ajouter un lieu</a></br>
		<a href="./capteurs.php?action=add">Ajouter un capteur</a><br>
		<a href="./bulletins.php?action=add">Ajouter un bulletin</a></br></br>

		
		<a href="./lieux.php?action=delete">Supprimer un lieu</a></br></br>
		
		<a href="./capteurs.php?action=update">Modifier les capteurs</a><br>
	<?php }?>
	
	
</body>
</html>
