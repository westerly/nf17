<?php 
include("head.php");
include("connect.php"); ?>
<html>
	<head>
		<title>Statistiques</title>
		<script>
			var details;
			var chbx;
			var chbx_err;
			var submit;
			var mois_d;
			var jour_d;
			var mois_f;
			var jour_f;
			var m_31;
			var m_30;
			var m_28;
			
			function init() {
				details = document.getElementsByName("lieu_detail");
				chbx = document.getElementById("checkboxes").getElementsByTagName("input");
				chbx_err = document.getElementById("chbx_err");
				submit = document.getElementById("submit");
				mois_d = document.getElementById("mois_d");
				jour_d = document.getElementById("jour_d");
				mois_f = document.getElementById("mois_f");
				jour_f = document.getElementById("jour_f");
				m_31 = document.getElementById("m_31");
				m_30 = document.getElementById("m_30");
				m_28 = document.getElementById("m_28");
			}
			
			function hide_details() {
				for (var i in details) {
					details.item(i).hidden = "true";
				}
			}
			
			function switch_lieu(type) {
				hide_details();
				document.getElementById(type).removeAttribute("hidden");
			}
			
			function checkbox_validation() {
				var valid = false;
				for (var i in chbx) {
					if (chbx.item(i).checked) {
						valid = true;
						break;
					}
				}
				if (valid) {
					chbx_err.hidden = "true";
					submit.removeAttribute("disabled");
				} else {
					chbx_err.removeAttribute("hidden");
					submit.disabled = "true";
				}
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
			
			function update_f() {
				var mois = mois_f.value;
				// 31 : JAN1 / MAR3 / MAI5 / JUIL7 / AOU / OCT / DEC
				if((mois < 8 && mois%2 == 1) || (mois >= 8 && mois%2 == 0)) {
					jour_f.innerHTML = m_31.innerHTML;
				} 
				// 28 : FEV
				else if (mois == 2) {
					jour_f.innerHTML = m_28.innerHTML;
				} 
				// 30 : RESTE
				else {
					jour_f.innerHTML = m_30.innerHTML;
				}
			}
			
		</script>
	</head>
	<body onload="init()">
		<form method=POST action="stats_view.php">
			<input type="hidden" name="check">
			<div id="checkboxes">
				<p>Selectionnez un ou plusieurs types de mesure :<br>
					<input type="checkbox" name="temp" onClick="checkbox_validation()"> Température
					<input type="checkbox" name="vent" onClick="checkbox_validation()"> Vent
					<input type="checkbox" name="pres" onClick="checkbox_validation()"> Précipitation
					<span id="chbx_err" style="color:red" hidden="true"><br>Vous devez selectionner au moins un type.</span>
				</p>
			</div>
			<div id="locations">
				Selectionnez un type de location :<br>
				<input type="radio" name="type_lieu" onClick="switch_lieu('lieu')" value="lieu" checked> Lieu
				<input type="radio" name="type_lieu" onClick="switch_lieu('dept')" value="dept"> Département
				<input type="radio" name="type_lieu" onClick="switch_lieu('reg')" value="reg"> Région
				<span id="lieu" name="lieu_detail">
					<br>Veuillez selectionner : 
					<select name="lieu">
						<?php
							$sql = "Select nom FROM lieux;";
							$query = $db->prepare($sql);
							$query->execute();
							$res = $query->fetchAll();
							foreach($res as $lieu) {
								echo '<option value="'.$lieu['nom'].'">'.$lieu['nom'].'</option>';
							}
						?>
					</select>
				</span>
				<span id="dept" name="lieu_detail" hidden="true">
					<br>Veuillez selectionner : 
					<select name="dept">
						<?php
							$sql = "Select departement_id, nom FROM departements;";
							$query = $db->prepare($sql);
							$query->execute();
							$res = $query->fetchAll();
							foreach($res as $dpt) {
								echo '<option value="'.$dpt['departement_id'].'">'.$dpt['nom'].'</option>';
							}
						?>
					</select>
				</span>
				<span id="reg" name="lieu_detail" hidden="true">
					<br>Veuillez selectionner : 
					<select name="reg">
						<?php
							$sql = "Select num, nom FROM regions;";
							$query = $db->prepare($sql);
							$query->execute();
							$res = $query->fetchAll();
							foreach($res as $reg) {
								echo '<option value="'.$reg['num'].'">'.$reg['nom'].'</option>';
							}
						?>
					</select>
				</span>
			</div>
			<br>
			<div>
				Précisez la période :<br>
				<table>
					<tr>
						<td>Début :</td> 
						<td>
							<select id="jour_d" name="jour_d">
								<option value="01">1</option>
								<option value="02">2</option>
								<option value="03">3</option>
								<option value="04">4</option>
								<option value="05">5</option>
								<option value="06">6</option>
								<option value="07">7</option>
								<option value="08">8</option>
								<option value="09">9</option>
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
						</td>
						<td>
							<select id="mois_d" name="mois_d" onChange="update_d()">
								<option value="01">Janvier</option>
								<option value="02">Fevrier</option>
								<option value="03">Mars</option>
								<option value="04">Avril</option>
								<option value="05">Mai</option>
								<option value="06">Juin</option>
								<option value="07">Juillet</option>
								<option value="08">Août</option>
								<option value="09">Septembre</option>
								<option value="10">Octobre</option>
								<option value="11">Novembre</option>
								<option value="12">Decembre</option>
							</select>
						</td>
						<td>
							<select name="annee_d">
								<option value="1990">1990</option>
								<option value="1991">1991</option>
								<option value="1992">1992</option>
								<option value="1993">1993</option>
								<option value="1994">1994</option>
								<option value="1995">1995</option>
								<option value="1996">1996</option>
								<option value="1997">1997</option>
								<option value="1998">1998</option>
								<option value="1999">1999</option>
								<option value="2000">2000</option>
								<option value="2001">2001</option>
								<option value="2002">2002</option>
								<option value="2003">2003</option>
								<option value="2004">2004</option>
								<option value="2005">2005</option>
								<option value="2006">2006</option>
								<option value="2007">2007</option>
								<option value="2008">2008</option>
								<option value="2009">2009</option>
								<option value="2010">2010</option>
								<option value="2011">2011</option>
								<option value="2012">2012</option>
								<option value="2013">2013</option>
								<option value="2014">2014</option>
								<option value="2015">2015</option>
								<option value="2016">2016</option>
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Fin :</td> 
						<td>
							<select id="jour_f" name="jour_f">
								<option value="01">1</option>
								<option value="02">2</option>
								<option value="03">3</option>
								<option value="04">4</option>
								<option value="05">5</option>
								<option value="06">6</option>
								<option value="07">7</option>
								<option value="08">8</option>
								<option value="09">9</option>
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
						</td>
						<td>
							<select id="mois_f" name="mois_f" onChange="update_f()">
								<option value="01">Janvier</option>
								<option value="02">Fevrier</option>
								<option value="03">Mars</option>
								<option value="04">Avril</option>
								<option value="05">Mai</option>
								<option value="06">Juin</option>
								<option value="07">Juillet</option>
								<option value="08">Août</option>
								<option value="09">Septembre</option>
								<option value="10">Octobre</option>
								<option value="11">Novembre</option>
								<option value="12">Decembre</option>
							</select>
						</td>
						<td>
							<select name="annee_f">
								<option value="1990">1990</option>
								<option value="1991">1991</option>
								<option value="1992">1992</option>
								<option value="1993">1993</option>
								<option value="1994">1994</option>
								<option value="1995">1995</option>
								<option value="1996">1996</option>
								<option value="1997">1997</option>
								<option value="1998">1998</option>
								<option value="1999">1999</option>
								<option value="2000">2000</option>
								<option value="2001">2001</option>
								<option value="2002">2002</option>
								<option value="2003">2003</option>
								<option value="2004">2004</option>
								<option value="2005">2005</option>
								<option value="2006">2006</option>
								<option value="2007">2007</option>
								<option value="2008">2008</option>
								<option value="2009">2009</option>
								<option value="2010">2010</option>
								<option value="2011">2011</option>
								<option value="2012">2012</option>
								<option value="2013">2013</option>
								<option value="2014">2014</option>
								<option value="2015">2015</option>
								<option value="2016">2016</option>
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<br>
			<input type="submit" id="submit" disabled="true">
		</form>
		<br><br>
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