<?php
require('top_file.php');
?>
<?php
function havebadfield($f) {
	global $_REQUEST;
	if(strstr($_REQUEST[bad], $f))
		print " bgcolor=\"red\"";
}

if($_REQUEST[seit] == "") { 
	$_REQUEST[seit] = date("Y-m-d");
}
?>
<body>
<h3><?
if(isset($_REQUEST[edit])) 
	print "Eintrag &auml;ndern"; 
else 
	print "Neuen Eintrag erstellen";
?></h3>
<?
if(isset($_REQUEST[bad])) 
	print "<font color=\"red\">Die rot markierten Eingaben sind fehlerhaft.</font>\n<p>\n";
?>
<font color="#d2b100"><small>Mit (*) gekennzeichnete Felder m&uuml;ssen
ausgef&uuml;llt werden. Sie dienen der eindeutigen Identifikation.</small></font>
<form action="insert.php" method="get">
<?
if(isset($_REQUEST[edit])) print "<input type=\"hidden\" name=\"edit\" value=\"1\">\n";
if(isset($_REQUEST[personen_id])) print "<input type=\"hidden\" name=\"personen_id\" value=\"$_REQUEST[personen_id]\">\n";
?>
	<table border=0>
		<tr>
			<td>Vorname&nbsp;<font color="#d2b100">*</font></td>
			<td<? havebadfield('vorname') ?>><input name="vorname" size=30 maxlength=30 value="<?php
  print "$_REQUEST[vorname]";
?>"></td>
		</tr><tr>
			<td>Name&nbsp;<font color="#d2b100">*</font></td>
			<td<? havebadfield('name') ?>><input name="name" size=30 maxlength=30 value="<?
  print $_REQUEST[name];
?>"></td>
		</tr><tr>
			<td>Geboren (1974-03-22)&nbsp;<font color="#d2b100">*</font></td>
			<td<? havebadfield('geboren') ?>><input name="geboren" size=30 maxlength=60 value="<?
  print $_REQUEST[geboren];
?>"></td>
		</tr><tr>
			<td>Rolle</td>
			<td><select name="art"><? print "
				<option value=\"schüler\"".($_REQUEST[art]=="schüler" || ! isset($_REQUEST[art])?" selected":"").">Sch&uuml;ler</option>
				<option value=\"lehrer\"".($_REQUEST[art]=="lehrer"?" selected":"").">Lehrer</option>
				<option value=\"sonstige\"".($_REQUEST[art]=="sonstige"?" selected":"").">Sonstige</option>
				<option value=\"\"".(isset($_REQUEST[art]) && "$_REQUEST[art]"==""?" selected":"").">keine Angabe</option>\n"; ?>
			</select></td>
		</tr><tr>
			<td>Eintrittsjahr (1984)</td>
			<td<? havebadfield('eintritt') ?>><input name="eintritt" size=30 maxlength=60 value="<?
  print $_REQUEST[eintritt];
?>"></td>
		</tr><tr>
			<td>Austrittsjahr (1995)</td>
			<td<? havebadfield('austritt') ?>><input name="austritt" size=30 maxlength=60 value="<?
  print $_REQUEST[austritt];
?>"></td>
		</tr><tr>
			<td>Ausbildung/Beruf</td>
			<td><input name="beruf" size=30 maxlength=60 value="<?
  print $_REQUEST[beruf];
?>"></td>
		</tr><tr>
			<td valign=top>Adresse</td>
			<td<? havebadfield('adresse') ?>><textarea name="adresse" rows=6 cols=34>
<?
  print $_REQUEST[adresse];
?></textarea></td>
		</tr><tr>
			<td>Gültig seit (1974-03-22)</td>
			<td<? havebadfield('seit') ?>><input name="seit" size=30 maxlength=60 value="<?
  print $_REQUEST[seit];
?>"></td>
		</tr><tr>
			<td>Email</td>
			<td<? havebadfield('email') ?>><input name="email" size=30 maxlength=500 value="<?
  print $_REQUEST[email];
?>"></td>
		</tr><tr>
			<td>Homepage</td>
			<td<? havebadfield('homepage') ?>><input name="homepage" size=30 maxlength=500 value="<?
  print $_REQUEST[homepage];
?>"></td>
		</tr><tr><td colspan=2>&nbsp;</td></tr><tr>   
			<td>&nbsp;</td><td><input type=submit value="Absenden">&nbsp;
			<input type=reset value="Reset"></td>
	</table>
</form>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>

