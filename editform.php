<?php
require('top_file.php');
?>
<h3>Bestehenden Eintrag &auml;ndern</h3>
Mit diesen Angaben wird der zu &auml;ndernde Eintrag ausgew&auml;hlt.
<p>
<form action="edit.php" method=post>
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
			<td colspan=2><input type=submit name="action" value="Steckbrief &auml;ndern">&nbsp; oder &nbsp;<input type=submit name="action" value="Klassenzuordnung &auml;ndern"></td>
		</tr>
	</table>
</form>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
