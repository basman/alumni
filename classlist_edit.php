<?php
require('top_file.php');
?>
<?
require "db.php";

function show_class_table($classes, $action_link) {
	global $_REQUEST;
	print "<table border=0>\n<tr><th>Aktion</th><th>Kommentar</th><th>Beginn</th><th>Ende</th><th>Personen</th></tr>\n";
	while($line = mysql_fetch_assoc($classes)) {
		# look for all class members of this person
		$q = "SELECT personen.* FROM klassenpersonen, personen WHERE klassen_id = '$line[klassen_id]' AND personen_id = personen.id";
		$classlist = mysql_query($q);
		$n = mysql_num_rows($classlist);
		# calc rows and columns of class member table
		$cols = ceil($n * 4 / 7);
		if($cols > 4) $cols = 4;
		$rows = ceil($n / $cols);
		print "<!-- $n class members, $cols cols, $rows rows -->\n";
		$insassen = "<table border=1><tr>";
		$i = 0;
		while($line2 = mysql_fetch_assoc($classlist)) {
			$i++;
			$color_start = "<font color=\"white\">";
			$color_stop = "";
			if($_REQUEST[personen_id] == $line2[id]) {
				$color_start = "<font color=\"green\">";
			} else if($line2[art] == "lehrer") {
				$color_start = "<font color=\"red\">";
			}
			if(!empty($color_start)) $color_stop = "</font>";
			$insassen .= "<td>$color_start$line2[vorname] $line2[name]$color_stop</td>";
			if($n > 1 && $i < $n && $i % $cols == 0) {
				$insassen .= "</tr><tr>";
			}
		}
		$insassen .= "</tr></table>";
		$action_link_do = preg_replace('/MACRO_KLASSEN_ID/',$line[klassen_id],$action_link);
		print "<tr>\n\t<td bgcolor=\"#003070\"><font color=\"white\">$action_link_do</td>
\t<td bgcolor=\"#003070\"><font color=\"white\">$line[kommentar]</font></td>
\t<td bgcolor=\"#003070\"><font color=\"white\">$line[beginn]</font></td>
\t<td bgcolor=\"#003070\"><font color=\"white\">$line[ende]</font></td>
\t<td bgcolor=\"#003070\"><font color=\"white\">$insassen</font></td></tr>";
	}
	print "</table>\n";
}

if($_REQUEST[action] == "delete") {
	# remove persons class connection
	mysql_query("DELETE FROM klassenpersonen WHERE personen_id = '".mysql_escape_string($_REQUEST[personen_id])."' AND klassen_id = '".mysql_escape_string($_REQUEST[klassen_id])."'") || print "<!-- mysql error: " . mysql_error() . " -->\n";;

	# delete all empty classes
	$emptyclasses = mysql_query("SELECT * FROM klassen LEFT JOIN klassenpersonen ON klassen.id = klassen_id WHERE klassen_id IS NULL");
	while($line = mysql_fetch_assoc($emptyclasses)) {
		mysql_query("DELETE FROM klassen WHERE id = '".$line[id]."'") || print "<!-- mysql error: " . mysql_error() . " -->\n";
	}
} else if($_REQUEST[action] == "add") {
	mysql_query("INSERT INTO klassenpersonen (personen_id, klassen_id) VALUES (".mysql_escape_string($_REQUEST[personen_id]).",".mysql_escape_string($_REQUEST[klassen_id]).")");
} else if($_REQUEST[action] == "new") {
	# check values given by user
	$wrong = 0;
	if(empty($_REQUEST[beginn])) {
		$_REQUEST[beginn_err] = "<br>Darf nicht leer gelassen werden.";
		$wrong = 1;
	} else if(!preg_match('/^[0-9]{4,4}$/',$_REQUEST[beginn])) {
		$_REQUEST[beginn_err] = "<br><font color=\"yellow\">$_REQUEST[beginn]</font> ist keine Jahreszahl";
		$wrong = 1;
	}
	if(empty($_REQUEST[ende])) {
		$_REQUEST[ende_err] = "<br>Darf nicht leer gelassen werden.";
		$wrong = 1;
	} else if(!preg_match('/^[0-9]{4,4}$/',$_REQUEST[ende])) {
		$_REQUEST[ende_err] = "<br><font color=\"yellow\">$_REQUEST[ende]</font> ist keine Jahreszahl";
		$wrong = 1;
	}
	if($wrong) {
		print "<b>Fehler: nicht alle erforderlichen Daten wurden in korrekter Weise angegeben. Die neue Klasse konnte nicht eingetragen werden.</b>\n";
	} else {
		# store new class
		mysql_query("INSERT INTO klassen (kommentar, beginn, ende) VALUES ('".mysql_escape_string($_REQUEST[kommentar])."','".mysql_escape_string($_REQUEST[beginn])."','".mysql_escape_string($_REQUEST[ende])."')");
		# add current person
		mysql_query("INSERT INTO klassenpersonen (personen_id, klassen_id) VALUES (".mysql_escape_string($_REQUEST[personen_id]).", ".mysql_insert_id().")");
	}
}


print "<h3>Bestehende Klassenzuordnungen von $_REQUEST[vorname] $_REQUEST[name]</h3>\n";

# look for classes this person belongs to
$myclasses = mysql_query("SELECT klassen.*, klassen.id AS klassen_id FROM klassen, klassenpersonen WHERE klassenpersonen.personen_id = '".mysql_escape_string($_REQUEST[personen_id])."' AND klassenpersonen.klassen_id = klassen.id");

$nof_classes = mysql_num_rows($myclasses);
if($nof_classes > 0) {
	show_class_table($myclasses, "<a href=\"$PHP_SELF?action=delete&personen_id=$_REQUEST[personen_id]&name=".urlencode($_REQUEST[name])."&vorname=".urlencode($_REQUEST[vorname])."&klassen_id=MACRO_KLASSEN_ID\">mich entfernen</a>");
} else {
	print "(Noch bist Du in keine Klasse eingetragen.)\n";
}

print "<p><table bgcolor=\"#003070\"><tr><td valign=\"top\"><font size=-1>Bedeutung der Farben:</font></td><td><font color=\"red\" size=-1>Lehrer</font><br><font color=\"green\" size=-1>Du selbst</font></td></tr></table>\n";
print "<hr>";

if($nof_classes == 0) {
	print "<h3>Neue Klassenzuordnung erstellen</h3>\n";
} else {
	print "<h3>Hast Du einmal in eine andere Klasse gewechselt?</h3>
...dann trage Dich auch noch in die andere Klasse ein.<p>\n";
}

# list all classes we are not yet in

$foreign_classes = mysql_query("SELECT klassen.*, klassen.id AS klassen_id FROM klassen LEFT JOIN klassenpersonen ON klassen_id = klassen.id AND personen_id = '".mysql_escape_string($_REQUEST[personen_id])."' WHERE personen_id IS NULL");

if(mysql_num_rows($foreign_classes) > 0) {
	show_class_table($foreign_classes, "<a href=\"$PHP_SELF?action=add&personen_id=$_REQUEST[personen_id]&name=".urlencode($_REQUEST[name])."&vorname=".urlencode($_REQUEST[vorname])."&klassen_id=MACRO_KLASSEN_ID\">mich hinzuf&uuml;gen</a>");
} else {
	print "Keine weiteren Klassen vorhanden.\n";
}

print "<hr><h3>Findest Du oben Deine Klasse nicht?</h3>
...dann hast Du die w&uuml;rdevolle Aufgabe, hier Deine Klasse zu er&ouml;ffnen.
<p>
<form action=\"$PHP_SELF\" method=\"post\">
<table border=0>
<tr><td>Jahr der Einschulung (z.B. 1987)</td><td bgcolor=\"red\"><input name=\"beginn\" value=\"$_REQUEST[beginn]\">$_REQUEST[beginn_err]</td></tr>
<tr><td>Ende</td><td bgcolor=\"red\"><input name=\"ende\" value=\"$_REQUEST[ende]\">$_REQUEST[ende_err]</td></tr>
<tr><td>Kommentar (z.B. Klassenlehrer Meier)</td><td><input name=\"kommentar\" value=\"$_REQUEST[kommentar]\"></td></tr>
<tr><td>&nbsp;</td><td align=\"center\"><input type=submit value=\"Ok\"></td></tr>
</table>
<input type=hidden name=\"action\" value=\"new\">
<input type=hidden name=\"personen_id\" value=\"$_REQUEST[personen_id]\">
<input type=hidden name=\"name\" value=\"$_REQUEST[name]\">
<input type=hidden name=\"vorname\" value=\"$_REQUEST[vorname]\">
</form>";
?>
</div>
</div>
<div id="background_footer"></div>
</body>
</html>
