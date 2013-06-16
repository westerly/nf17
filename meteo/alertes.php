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
		<script>
			var mois_d;
			var jour_d;
			var m_31;
			var m_30;
			var m_28;
			
			function init() {
				mois_d = document.getElementById("mois");
				jour_d = document.getElementById("jour");
				m_31 = document.getElementById("m_31");
				m_30 = document.getElementById("m_30");
				m_28 = document.getElementById("m_28");
				update_d();
			}
			
			function update_d() {
				var mois = mois_d.value;
				// 31 : JAN1 / MAR3 / MAI5 / JUIL7 / AOU / OCT / DEC
				if((mois < 8 && mois%2 == 1) || (mois >= 8 && mois%2 == 0)) {
					jour_d.innerHTML = m_31.innerHTML;
				} 
				// 28 : FEV
				else if (mois == 2) {
					jour_d.innerHTML = m_28.innerHTML;
				} 
				// 30 : RESTE
				else {
					jour_d.innerHTML = m_30.innerHTML;
				}
			}
		</script>
	</head>
	<body onload="init()">
		<div id="search">
			<span>N'afficher que les alertes postérieures au </span>
			<form method=POST action="./alertes.php">
				<select id="jour" name="jour">
					<?php
					if(isset($_POST['jour'])) {
						for($i=1; $i<=31;$i++) {
							if($i == $_POST['jour']) {
								echo "<option value='$i' selected>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
						}
					} else {
						for($i=1; $i<=31;$i++) {
							echo "<option value='$i'>$i</option>";
						}
					}
					?>
				</select>
				<select id="mois" name="mois" onChange="update_d()">
					<?php
					$months = array(
						'01' => 'Janvier',
						'02' => 'Fevrier',
						'03' => 'Mars',
						'04' => 'Avril',
						'05' => 'Mai',
						'06' => 'Juin',
						'07' => 'Juillet',
						'08' => 'Août',
						'09' => 'Septembre',
						'10' => 'Octobre',
						'11' => 'Novembre',
						'12' => 'Decembre'
					);
					if(isset($_POST['mois'])) {
						foreach($months as $key => $month) {
							if($key == $_POST['mois']) {
								echo "<option value='$key' selected>$month</option>";
							} else {
								echo "<option value='$key'>$month</option>";
							}
						}
					} else {
						foreach($months as $key => $month) {
							echo "<option value='$key'>$month</option>";
						}
					}
					?>
				</select>
				<select name="annee">
					<?php
					$years = array(						
						'1990',
						'1991',
						'1992',
						'1993',
						'1994',
						'1995',
						'1996',
						'1997',
						'1998',
						'1999',
						'2000',
						'2001',
						'2002',
						'2003',
						'2004',
						'2005',
						'2006',
						'2007',
						'2008',
						'2009',
						'2010',
						'2011',
						'2012',
						'2013',
						'2014',
						'2015',
						'2016',
						'2017',
						'2018',
						'2019',
						'2020',
					);
					if(isset($_POST['annee'])) {
						foreach($years as $year) {
							if($year == $_POST['annee']) {
								echo "<option value='$year' selected>$year</option>";
							} else {
								echo "<option value='$year'>$year</option>";
							}
						}
					} else {
						foreach($years as $year) {
							echo "<option value='$year'>$year</option>";
						}
					}
					?>
				</select>
				<input type="submit" value="Filtrer">
			</form>
		</div>
		<h2>Alertes</h2>
		<?php
			$condition = "";
			if (isset($_POST['jour']) && isset($_POST['mois']) && isset($_POST['annee'])) {
				$jour = $_POST['jour'] < 10 ? '0'.$_POST['jour'] : $_POST['jour'];
				$date = $_POST['annee'].'-'.$_POST['mois'].'-'.$jour;
				$condition .= ' WHERE date >= \''.$date.'\' ';
			}
		?>
		<div id="Treelle">
			<span>Temperatures réelles dépassant le seuil :</span><br>
			<?php
			$sql = "SELECT * FROM alerteTempReelle".$condition.";";
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
					
					<?php foreach($res as $row): ?>
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
			$sql = "SELECT * FROM alerteTempRessentie".$condition.";";
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
			$sql = "SELECT * FROM alertevent".$condition.";";
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
			$sql = "SELECT * FROM alertePrecip".$condition.";";
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
		<a href=".">Accueil</a>
				<div hidden>
			<select id="m_31">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>
			<select id="m_30">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
			</select>
			<select id="m_28">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
			</select>
		</div>
	</body>
</html>