<?php
require('top_file.php');
?>
<h3>Klassenliste anzeigen</h3>
Klassenlisten werden nur Personen gezeigt, die sich selber eingetragen haben.
Daher sind die folgenden Angaben erforderlich.
<p>
<form action="classlist_showdown.php" method=post>
	<table border=0>
		<tr>
			<td>Vorname</td>
			<td ><input name="vorname" size=30 maxlength=30 value="<?php
  print "$_REQUEST[vorname]";
?>"></td>
		</tr><tr>
			<td>Name</td>
			<td><input name="name" size=30 maxlength=30 value="<?
  print $_REQUEST[name];
?>"></td>
		</tr><tr>
			<td>Geboren (1974-03-22)</td>
			<td><input name="geboren" size=30 maxlength=60 value="<?
  print $_REQUEST[geboren];
?>"></td>
		</tr><tr><td colspan=2>&nbsp;</td></tr><tr>   
			<td>&nbsp;</td><td><input type=submit name="action" value="Meine Klassenliste anzeigen"></td>
		</tr>
	</table>
</form>
<p>&nbsp;<p>
Zum &auml;ndern Deiner Klassenzuordnung, <a href="editform.php">bitte hier entlang</a>.
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
