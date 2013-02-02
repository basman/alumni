<?php
require('top_file.php');
?>

<h3>Alle Versionen einer Adresse</h3>
<?
require("db.php");

$query = "SELECT * FROM personen, adressen WHERE personen.id=personen_id AND personen.id = ".mysql_escape_string($_REQUEST[person])." ORDER BY seit DESC, version DESC";

$result = mysql_query($query);

$i = mysql_num_rows($result);
print "<b>$i Adresse" . ($i!=1?"n":"") . " vorhanden</b>\n<p>\n";
print "<table border=0>\n";
while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	print "<tr><td colspan=2 bgcolor=\"blue\">$i ".
		"<!-- a href=\"edit.php?person=$line[personen_id]\">&auml;ndern</a -->".
		"</td></tr>\n";

	# konkrete Daten anfragen
	foreach($line as $field => $val) {
		if($field == "id" || $field == "eingetragen" || preg_match("/_id$/", $field)) continue;
		if($field == "adresse") $val = "<pre>".$val."</pre>";
		if($field == "seit") $field = "G&uuml;ltig seit";
		if($field == "eintritt") $field = "Eintrittsjahr";
		if($field == "austritt") $field = "Austrittsjahr";
		$field = ucfirst($field);
		print "<tr><td bgcolor=\"blue\" valign=top><font color=\"white\">$field</font></td><td bgcolor=\"#003070\">$val</td></tr>";
	}
	print "<tr><td colspan=2>&nbsp;</td></tr>\n";
	$i--;
}
print "</table>\n<p>";
print "<a href=\"search.php\">Neue Suche</a>&nbsp;<a href=\"insertform.php\">Eingabe</a>";
?>

</div>
</div>
<div id="background_footer"></div>
</body>
</html>

