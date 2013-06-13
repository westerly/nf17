<?php 
include('connect.php');
include('head.php');
?>
<html>
	<head>
		<title>Alertes</title>
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
		<h2>Alertes</h2>
		<div id="Treelle">
			<span>Temperatures réelles dépassant le seuil :</span><br>
			<?php
			$sql = "SELECT * FROM alerteTempReelle;";
			$query = $db->prepare($sql);
			$query->execute();
			$res = $query->fetchAll();
			if (empty($res)):
				echo "Aucunes alertes";
			else:
				?>
				<table>
					<tr>
						<th>Lieu</th>
						<th>Date</th>
						<th>T° Réelle</th>
						<th>Seuil</th>
						<th>Depassement</th>
					</tr>
					
					<?php 
					foreach($res as $row): ?>
					<tr>
						<td><?php echo $row['lieu_id'] ?></td>
						<td><?php echo $row['date'] ?></td>
						<td><?php echo $row['reelle'] ?></td>
						<td><?php echo $row['seuil'] ?></td>
						<td><?php echo $row['depassement'] ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			<?php
			endif;
			?>
		</div>
		<br>
		<div id="Tressentie">
			<span>Temperatures ressenties dépassant le seuil :</span><br>
			<?php
			$sql = "SELECT * FROM alerteTempRessentie;";
			$query = $db->prepare($sql);
			$query->execute();
			$res = $query->fetchAll();
			if (empty($res)):
				echo "Aucunes alertes";
			else:
				?>
				<table>
					<tr>
						<th>Lieu</th>
						<th>Date</th>
						<th>T° Ressentie</th>
						<th>Seuil</th>
						<th>Depassement</th>
					</tr>
					<?php foreach($res as $row): ?>
					<tr>
						<td><?php echo $row['lieu_id'] ?></td>
						<td><?php echo $row['date'] ?></td>
						<td><?php echo $row['ressentie'] ?></td>
						<td><?php echo $row['seuil'] ?></td>
						<td><?php echo $row['depassement'] ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			<?php
			endif;
			?>
		</div>
		<br>
		<div id="Vent">
			<span>Vents dépassant le seuil :</span><br>
			<?php
			$sql = "SELECT * FROM alertevent;";
			$query = $db->prepare($sql);
			$query->execute();
			$res = $query->fetchAll();
			if (empty($res)):
				echo "Aucunes alertes";
			else:
				?>
				<table>
					<tr>
						<th>Lieu</th>
						<th>Date</th>
						<th>Force</th>
						<th>Seuil</th>
						<th>Depassement</th>
					</tr>
					<?php foreach($res as $row): ?>
					<tr>
						<td><?php echo $row['lieu_id'] ?></td>
						<td><?php echo $row['date'] ?></td>
						<td><?php echo $row['force'] ?></td>
						<td><?php echo $row['seuil'] ?></td>
						<td><?php echo $row['depassement'] ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			<?php
			endif;
			?>
		</div>
		<br>
		<div id="Precip">
			<span>Precipitations dépassant le seuil :</span><br>
			<?php
			$sql = "SELECT * FROM alertePrecip;";
			$query = $db->prepare($sql);
			$query->execute();
			$res = $query->fetchAll();
			if (empty($res)):
				echo "Aucunes alertes";
			else:
				?>
				<table>
					<tr>
						<th>Lieu</th>
						<th>Date</th>
						<th>Force</th>
						<th>Seuil</th>
						<th>Depassement</th>
					</tr>
					<?php foreach($res as $row): ?>
					<tr>
						<td><?php echo $row['lieu_id'] ?></td>
						<td><?php echo $row['date'] ?></td>
						<td><?php echo $row['force'] ?></td>
						<td><?php echo $row['seuil'] ?></td>
						<td><?php echo $row['depassement'] ?></td>
					</tr>
					<?php endforeach; ?>
				</table>
			<?php
			endif;
			?>
		</div>
		<br>
		<br>
		<a href=".">Acceuil</a>
	</body>
</html>