<?php include("./head.php")?>

<?php 
	
	if(isset($_GET["action"]) &&  $_GET["action"] == "connect"){
		
		include("./connect.php");
		
		$sql = "select * from users where login = '".trim($_POST["login"])."' AND mdp = '".trim($_POST["mdp"])."'";
		
		$sth = $db->prepare($sql);
		$sth->execute();
		$row = $sth->fetch();
		
		if($row["user_id"] != ""){
			//session_start();
			// store session data
			$_SESSION['userCo']=1;
			header("Location: index.php");
		}else{
			echo "Login ou mot de passe incorrect.";
		}
		
	}
	
	if(isset($_GET["action"]) &&  $_GET["action"] == "deconnect"){
	
			//unset($_SESSION['userCo']);
			session_destroy();
			header("Location: index.php");
	}


?>

<br>
<br>
<a href=".">Accueil</a>